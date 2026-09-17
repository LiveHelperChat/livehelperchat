<?php

$tpl = erLhcoreClassTemplate::getInstance('lhaimcp/key.tpl.php');

$mcpOptions = erLhcoreClassModelChatConfig::fetch('ai_mcp_options');
$data = (array)$mcpOptions->data;

if (isset($_POST['StoreOptions']) || isset($_POST['GenerateToken'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('aimcp/key');
        exit;
    }

    $definition = array(
        'server_name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'token' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );

    $data['server_name'] = $form->hasValidData('server_name') ? trim($form->server_name) : '';

    if (isset($_POST['GenerateToken'])) {
        $data['token'] = bin2hex(random_bytes(32));
        $tpl->set('updated', 'generated');
    } else {
        $data['token'] = $form->hasValidData('token') ? trim($form->token) : '';
        $tpl->set('updated', 'done');
    }

    $mcpOptions->explain = '';
    $mcpOptions->type = 0;
    $mcpOptions->hidden = 1;
    $mcpOptions->identifier = 'ai_mcp_options';
    $mcpOptions->value = serialize($data);
    $mcpOptions->saveThis();
}

$tpl->set('mcp_server_name', isset($data['server_name']) ? (string)$data['server_name'] : '');
$mcpToken = isset($data['token']) ? trim((string)$data['token']) : '';
$tpl->set('mcp_token', $mcpToken);

$mcpEndpoint = erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('aimcp/mcp');
if ($mcpToken !== '') {
    $mcpEndpoint .= '?token=' . rawurlencode($mcpToken);
}

$tpl->set('mcp_endpoint', $mcpEndpoint);

// Read from the `#[McpTool]` attributes, so the page cannot drift away from what `tools/list` answers.
$mcpTools = \LiveHelperChat\Mcp\ServerFactory::tools();

$tpl->set('mcp_tools', $mcpTools);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhaimcp/key', 'AI MCP Server')
    )
);

?>
