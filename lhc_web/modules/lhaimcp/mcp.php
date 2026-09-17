<?php

/**
 * Live Helper Chat - MCP (Model Context Protocol) endpoint.
 *
 * This file is a thin bridge. It authenticates the caller, turns the current PHP request into a
 * PSR-7 request and hands it to the official MCP PHP SDK (`mcp/sdk`). The protocol itself -
 * the `initialize` handshake, protocol revisions, sessions, `ping`, `tools/list`, `tools/call`,
 * JSON-RPC framing and CORS - is handled by the SDK.
 *
 * Tools are plain classes carrying `#[McpTool]` attributes and live in
 * `lib/vendor_lhc/LiveHelperChat/Mcp/Tools`. Shared access logic sits in
 * `LiveHelperChat\Mcp\Access`, object level rules in `LiveHelperChat\Mcp\Rules`.
 *
 * Endpoint  : https://<host>/site_admin/aimcp/mcp
 * Transport : Streamable HTTP (`Mcp\Server\Transport\StreamableHttpTransport`)
 *
 * Plug into ChatGPT (Settings -> Connectors -> Advanced -> Developer mode -> Add custom connector)
 * or Claude (Settings -> Connectors -> Add custom connector):
 *   URL            : https://<host>/site_admin/aimcp/mcp
 *   Authentication : None, or header "Authorization: Bearer <token>" / URL "?token=<token>" when a token is set
 *
 * Deployment notes:
 *   composer require mcp/sdk symfony/finder
 *   composer dump-autoload -o   # composer.json enables classmap-authoritative
 *
 * Sessions are kept in the `lh_mcp_session` table, which is created by the regular database update
 * (`system/update` in the back office or `cron/util/update_database`).
 */

// ---------------------------------------------------------------------------
// Configuration - back office "aimcp/key", stored as the `ai_mcp_options` chat config
// ---------------------------------------------------------------------------

$McpOptions = \erLhcoreClassModelChatConfig::fetch('ai_mcp_options');

// Empty token disables the endpoint.
$McpToken = isset($McpOptions->data['token']) ? trim((string)$McpOptions->data['token']) : '';

$McpServerName = !empty($McpOptions->data['server_name'])
    ? trim((string)$McpOptions->data['server_name'])
    : 'Live Helper Chat';

// Origins allowed to call the endpoint from a browser. The default keeps server side MCP hosts
// working - they send no `Origin` header at all.
$McpAllowedOrigins = !empty($McpOptions->data['allowed_origins'])
    ? array_values((array)$McpOptions->data['allowed_origins'])
    : array('*');

// Optional host allowlist for the SDK DNS rebinding protection. Empty means "do not install that
// middleware" - Live Helper Chat normally sits behind a web server which already validates `Host`.
$McpAllowedHosts = !empty($McpOptions->data['allowed_hosts'])
    ? array_values(array_filter(array_map('trim', (array)$McpOptions->data['allowed_hosts'])))
    : array();

$McpInstructions = 'Live Helper Chat administration tools. Use `get_user_id_by_email` to find an operator user ID '
    . 'by email, `get_user_id_by_username` to find it by username, '
    . '`get_url_permissions` to find what module/function a given back office URL requires, '
    . '`get_user_permissions` to see what a specific operator '
    . 'is allowed to do (including which departments he can access in read/write mode), '
    . '`explain_chat_access` to answer questions like "why user with id X cannot open chat Y" and '
    . '`check_user_object_access` to diagnose object editing (department, user, chat, ...). '
    . 'For chats nobody picked up use `explain_chat_auto_assign` ("would chat X be auto assigned?", '
    . '"why was example@example.com not auto assigned to chat X") and `get_department_auto_assign_settings` to inspect '
    . 'the auto assignment configuration of a department and its operators. '
    . 'For numbers use the read only, installation wide statistics tools: `count_chats` ("how many chats were there in the '
    . 'past 24 hours", counts per department/operator/status or per day), `get_chat_statistics` (chat volume and message '
    . 'counters of a window), `get_chat_activity` (time series per hour/day/week/month/weekday) and `get_chat_performance` '
    . '(wait and response times). `get_last_chats` is the follow up - it answers "give me the last chat ids of this '
    . 'visitor" with at most five chats, each as a chat id and a date, ordered by chat id: newest first by default, '
    . 'oldest first with `sort` = `oldest`; it also returns a `list_url` which opens the back office chat list '
    . 'with the same filters and ordering, so the reported chats can be verified manually. They take the same raw `filters` map, the data '
    . 'tools also accept a `nick`, and `get_chat_filter_fields` lists the accepted operators, the filterable fields and '
    . 'the time window shorthands.';

// ---------------------------------------------------------------------------
// Early exits - these happen before the SDK is involved, so they answer plain JSON
// ---------------------------------------------------------------------------

if (!function_exists('lhaimcp_output')) {

    function lhaimcp_output($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, Mcp-Protocol-Version, Mcp-Session-Id');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    function lhaimcp_header($name)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $header => $value) {
                if (strcasecmp($header, $name) === 0) {
                    return $value;
                }
            }
        }
        return null;
    }
}

// We never use the PHP session, closing it early avoids parallel calls blocking each other.
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

// Browsers do not send `Authorization` on a preflight, so it has to be answered before the token check.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, Mcp-Protocol-Version, Mcp-Session-Id');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
    header('Access-Control-Max-Age: 1728000');
    http_response_code(204);
    exit;
}

// A missing token disables the endpoint. The query parameter is supported for clients which cannot
// configure a custom Authorization header, but URL tokens can be exposed in logs.
if ($McpToken === '') {
    lhaimcp_output(array('error' => true, 'message' => 'MCP endpoint is not configured. Set an access token first.'), 503);
}

$authorization = lhaimcp_header('Authorization');
$queryToken = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
$headerValid = $authorization !== null && hash_equals('Bearer ' . $McpToken, $authorization);
$queryValid = $queryToken !== '' && hash_equals($McpToken, $queryToken);

if (!$headerValid && !$queryValid) {
    lhaimcp_output(array('error' => true, 'message' => 'Unauthorized'), 401);
}

// ---------------------------------------------------------------------------
// Hand the request over to the SDK
// ---------------------------------------------------------------------------

// Sessions are kept in the `lh_mcp_session` table by the server factory.
$McpServer = \LiveHelperChat\Mcp\ServerFactory::build($McpServerName, $McpInstructions);

// `php-http/discovery` is already a dependency and can build a PSR-7 request from the superglobals.
$McpPsr17 = new \Http\Discovery\Psr17Factory();

$McpTransport = new \Mcp\Server\Transport\StreamableHttpTransport(
    $McpPsr17->createServerRequestFromGlobals(),
    $McpPsr17,
    $McpPsr17,
    middleware: \LiveHelperChat\Mcp\ServerFactory::middleware($McpAllowedOrigins, $McpAllowedHosts)
);

$McpResponse = $McpServer->run($McpTransport);

// Emit the PSR-7 response the SDK produced.
http_response_code($McpResponse->getStatusCode());

foreach ($McpResponse->getHeaders() as $McpHeaderName => $McpHeaderValues) {
    $McpReplace = true;
    foreach ($McpHeaderValues as $McpHeaderValue) {
        header($McpHeaderName . ': ' . $McpHeaderValue, $McpReplace);
        $McpReplace = false;
    }
}

echo (string)$McpResponse->getBody();
exit;
