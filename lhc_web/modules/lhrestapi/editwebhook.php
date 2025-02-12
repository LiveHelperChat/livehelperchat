GNU nano 7.2                                                                                                   editwebhook.php                                                                                                             <?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'), true);

    if (!isset($requestBody['id']) || !isset($requestBody['disabled'])) {
        throw new Exception("Parâmetros inválidos. ID e status são obrigatórios.");
    }

    $webhook = erLhcoreClassModelChatWebhook::fetch((int)$requestBody['id']);

    if (!$webhook) {
        throw new Exception("Webhook não encontrado.");
    }

    if (isset($requestBody['configuration'])) {
    $webhook->configuration = $requestBody['configuration'];
    }
    $webhook->disabled = $requestBody['disabled'];
    $webhook->updateThis();

    erLhcoreClassRestAPIHandler::outputResponse([
        'success' => true,
        'message' => 'Webhook atualizado com sucesso.',
        'webhook_id' => $webhook->id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    erLhcoreClassRestAPIHandler::outputResponse([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

exit();
?>

