<?php

namespace LiveHelperChat\Mcp;

use Mcp\Server;
use Mcp\Server\Session\FileSessionStore;
use Mcp\Server\Transport\Http\Middleware\CorsMiddleware;
use Mcp\Server\Transport\Http\Middleware\DnsRebindingProtectionMiddleware;

/**
 * Builds the MCP server used by `modules/lhaimcp/mcp.php`.
 *
 * Everything here is SDK configuration - the module keeps only the authentication gate and the
 * request/response plumbing, so protocol concerns (handshake, protocol eras, sessions, JSON-RPC
 * framing, tools/list and tools/call) stay in the SDK.
 *
 * Tools are discovered from the `#[McpTool]` attributes inside the `Tools` directory. Only that
 * directory is scanned on purpose: discovery aborts and reports zero elements if a single
 * discovered file cannot be loaded, so the scan stays as narrow as possible.
 *
 * NOTE: `composer.json` enables `classmap-authoritative`, so a new or renamed tool class needs a
 * `composer dump-autoload -o` run before it becomes discoverable.
 */
class ServerFactory
{
    const SERVER_VERSION = '1.0.0';

    /** Scanned for `#[McpTool]`, `#[McpResource]`, ... attributes - relative to this file. */
    const TOOLS_DIR = 'Tools';

    /** The attribute the SDK discovers tool methods by. */
    const TOOL_ATTRIBUTE = 'Mcp\\Capability\\Attribute\\McpTool';

    /**
     * @param string      $name         Server name shown in the MCP client.
     * @param string|null $instructions Hints handed to the model during `initialize`.
     * @param string|null $sessionDir   Writable directory for HTTP sessions. HTTP needs a persistent
     *                                  store because PHP keeps no state between requests.
     *
     * @return Server
     */
    public static function build($name, $instructions = null, $sessionDir = null)
    {
        $builder = Server::builder()
            ->setServerInfo($name, self::SERVER_VERSION)
            ->setDiscovery(__DIR__, array(self::TOOLS_DIR), array());

        if ($instructions !== null && $instructions !== '') {
            $builder->setInstructions($instructions);
        }

        if ($sessionDir !== null && $sessionDir !== '') {
            $builder->setSession(new FileSessionStore($sessionDir));
        }

        return $builder->build();
    }

    /**
     * Transport middleware.
     *
     * The SDK defaults are replaced on purpose:
     *  - `CorsMiddleware` sets no `Access-Control-Allow-Origin` by default, which would block the
     *    browser based MCP clients;
     *  - `DnsRebindingProtectionMiddleware` allows localhost only by default, which would reject
     *    every remote MCP host (ChatGPT, Claude, ...).
     *
     * Host validation is only added back when hosts are configured - Live Helper Chat normally sits
     * behind a web server which already validates the `Host` header.
     *
     * @param string[] $allowedOrigins
     * @param string[] $allowedHosts
     *
     * @return array
     */
    public static function middleware(array $allowedOrigins, array $allowedHosts)
    {
        $middleware = array(new CorsMiddleware(allowedOrigins: $allowedOrigins));

        if (!empty($allowedHosts)) {
            $middleware[] = new DnsRebindingProtectionMiddleware(allowedHosts: $allowedHosts);
        }

        return $middleware;
    }

    /**
     * Lists the tools the endpoint exposes, read from the `#[McpTool]` attributes of the classes in
     * `TOOLS_DIR` - the very same attributes `tools/list` is built from, so the list cannot drift
     * away from what a client really gets.
     *
     * The class and method are resolved by reflection and no tool is instantiated, so the catalog
     * also works when the SDK is not installed yet - the exact tool names are pinned in the
     * attribute arguments though, so without the SDK the method name is reported instead.
     *
     * @return array<int,array{name:string,description:string,handler:string}>
     */
    public static function tools()
    {
        $tools = array();

        foreach ((array)glob(__DIR__ . '/' . self::TOOLS_DIR . '/*.php') as $file) {
            $class = self::classFromFile($file);

            if ($class === null) {
                continue;
            }

            if (!class_exists($class)) {
                // `classmap-authoritative` is enabled, so a class added since the last
                // `composer dump-autoload -o` is not autoloadable yet.
                require_once $file;
            }

            if (!class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isStatic() || $method->getDeclaringClass()->getName() !== $reflection->getName()) {
                    continue;
                }

                $arguments = null;

                foreach ($method->getAttributes() as $attribute) {
                    if ($attribute->getName() !== self::TOOL_ATTRIBUTE) {
                        continue;
                    }

                    $arguments = array();

                    try {
                        $arguments = $attribute->getArguments();
                    } catch (\Throwable $e) {
                        // Reading the arguments instantiates them (`new ToolAnnotations(...)`), so
                        // this needs the SDK. Without it the name falls back to the method name.
                        $arguments = array();
                    }

                    break;
                }

                if ($arguments === null) {
                    continue;
                }

                $tools[] = array(
                    'name' => isset($arguments['name']) ? (string)$arguments['name'] : $method->getName(),
                    'description' => self::description($method),
                    'handler' => $reflection->getName() . '::' . $method->getName(),
                );
            }
        }

        return $tools;
    }

    /**
     * Resolves the class declared in a tool file from its source, without loading the file.
     *
     * @param string $file
     *
     * @return string|null
     */
    private static function classFromFile($file)
    {
        $source = (string)file_get_contents($file);

        if (!preg_match('/^\s*namespace\s+([^;{]+)[;{]/m', $source, $namespace) ||
            !preg_match('/^\s*(?:final\s+|abstract\s+)?class\s+(\w+)/m', $source, $class)) {
            return null;
        }

        return trim($namespace[1]) . '\\' . $class[1];
    }

    /**
     * Summary of a method docblock - what the SDK uses as the tool description.
     *
     * @param \ReflectionMethod $method
     *
     * @return string
     */
    private static function description(\ReflectionMethod $method)
    {
        $docblock = (string)$method->getDocComment();

        if ($docblock === '') {
            return '';
        }

        $summary = array();

        foreach (preg_split('/\R/', $docblock) as $line) {
            $line = trim((string)preg_replace('/^\s*\/?\*+\s?/', '', $line));

            if ($line === '') {
                if (empty($summary)) {
                    continue; // the `/**` opening and the empty line before `*/`
                }
                break;
            }

            if ($line[0] === '@') {
                break;
            }

            $summary[] = $line;
        }

        return trim((string)preg_replace('/\s+/', ' ', implode(' ', $summary)));
    }
}
