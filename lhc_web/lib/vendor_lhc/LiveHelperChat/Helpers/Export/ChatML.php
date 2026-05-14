<?php

namespace LiveHelperChat\Helpers\Export;

class ChatML
{
	public static function fromChat($chat, $params = array())
	{
		$lastMessages = isset($params['last_messages']) && (int)$params['last_messages'] > 0 ? (int)$params['last_messages'] : 15;
		$excludeOperatorMessages = isset($params['exclude_operator_messages']) && $params['exclude_operator_messages'] == true;
		$onlyWithToolCalls = isset($params['only_with_tool_calls']) && $params['only_with_tool_calls'] == true;
		$toolCallNames = array();
		$hasToolCallUsed = false;

		$messages = \erLhcoreClassModelmsg::getList(array(
            'filternotlikefields' => [/*['meta_msg' => '"debug":true'],*/['meta_msg' => '{"content":{"typing"'],['meta_msg' => '{"content":{"execute_js"']],
			'limit' => false,
			'sort' => 'id ASC',
			'filter' => array('chat_id' => $chat->id)
		));

		$messages = array_values($messages);
		if ($lastMessages > 0 && count($messages) > $lastMessages) {
			$messages = array_slice($messages, 0, $lastMessages);
		}

		$turns = array();
		$currentTurn = null;
		$hasBotMessageLogged = false;
		$transferToolCallInjected = false;
		$includeNextBotMessageAfterFileSearch = false;

		foreach ($messages as $message) {
            $message->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $message->msg);

			if ($includeNextBotMessageAfterFileSearch === true) {
				if ((int)$message->user_id === -2) {
					$content = (string)$message->msg;
					if ($content !== '') {
						if ($currentTurn === null) {
							$currentTurn = array();
						}

						$currentTurn[] = array('role' => 'assistant', 'content' => $content);
					}

					break;
				}

				continue;
			}

			if (self::hasFileSearchCompletedEvent($message)) {
				if ($currentTurn === null) {
					$currentTurn = array();
				}

				$toolCallId = 'file_search_' . (int)$chat->id . '_' . (int)$message->id;
				$currentTurn[] = array(
					'role' => 'assistant',
					'tool_calls' => array(
						array(
							'id' => $toolCallId,
							'type' => 'function',
							'function' => array(
								'name' => 'file_search',
								'arguments' => '{}'
							)
						)
					)
				);
				$hasToolCallUsed = true;

				$currentTurn[] = array(
					'role' => 'tool',
					'tool_call_id' => $toolCallId,
					'name' => 'file_search',
					'content' => '{"status":"success","message":"content will be returned from file search"}'
				);

				$includeNextBotMessageAfterFileSearch = true;
				continue;
			}

			if (self::isJSONMetaMessage($message)) {
				$jsonMessages = self::parseJsonMetaAsChatML($message, $toolCallNames, $hasToolCallUsed);
				if (!empty($jsonMessages) && $currentTurn !== null) {
					foreach ($jsonMessages as $jsonMessage) {
						$currentTurn[] = $jsonMessage;
					}
				}
				continue;
			}

			$role = self::resolveRole($message);
			if ($role === null) {
				continue;
			}

			if ((int)$message->user_id === -2) {
				$hasBotMessageLogged = true;
			}

			$content = (string)$message->msg;
			if ($content === '') {
				continue;
			}

			if ($excludeOperatorMessages && (int)$message->user_id > 0) {
				if ($currentTurn !== null && !empty($currentTurn) && $hasBotMessageLogged === true && $transferToolCallInjected === false) {
					$toolCallId = 'transfer_to_operator_' . (int)$chat->id . '_' . (int)$message->id;
					$currentTurn[] = array(
						'role' => 'assistant',
						'tool_calls' => array(
							array(
								'id' => $toolCallId,
								'type' => 'function',
								'function' => array(
									'name' => 'transfer_to_operator',
									'arguments' => '{}'
								)
							)
						)
					);
					// Not interested in this event as tool call
					// $hasToolCallUsed = true;

					$currentTurn[] = array(
						'role' => 'tool',
						'tool_call_id' => $toolCallId,
						'name' => 'transfer_to_operator',
						'content' => '{"status":"success"}'
					);

					$transferToolCallInjected = true;
				}

				break;
			}

			$injectTransferToolCall = (
				$role === 'assistant' &&
				(int)$message->user_id > 0 &&
				$hasBotMessageLogged === true &&
				$transferToolCallInjected === false
			);

			if ($role === 'user') {
				if ($currentTurn !== null && !empty($currentTurn)) {
					$turns[] = $currentTurn;
				}

				$currentTurn = array(
					array('role' => 'user', 'content' => $content)
				);
			} elseif ($currentTurn !== null) {
				if ($injectTransferToolCall) {
					$toolCallId = 'transfer_to_operator_' . (int)$chat->id . '_' . (int)$message->id;
					$currentTurn[] = array(
						'role' => 'assistant',
						'tool_calls' => array(
							array(
								'id' => $toolCallId,
								'type' => 'function',
								'function' => array(
									'name' => 'transfer_to_operator',
									'arguments' => '{}'
								)
							)
						)
					);
					$hasToolCallUsed = true;

					$currentTurn[] = array(
						'role' => 'tool',
						'tool_call_id' => $toolCallId,
						'name' => 'transfer_to_operator',
						'content' => '{"status":"success"}'
					);

					$transferToolCallInjected = true;
				}

				$currentTurn[] = array('role' => 'assistant', 'content' => $content);
			}
		}

		if ($currentTurn !== null && !empty($currentTurn)) {
			$turns[] = $currentTurn;
		}

		if (empty($turns)) {
			return array('messages' => array());
		}

		if ($onlyWithToolCalls === true && $hasToolCallUsed === false) {
			return array('messages' => array());
		}

		$chatMLMessages = array();

		if (isset($params['system_prompt']) && $params['system_prompt'] !== '') {
			$chatMLMessages[] = array('role' => 'system', 'content' => (string)$params['system_prompt']);
		}

		foreach ($turns as $turn) {
			foreach ($turn as $turnMessage) {
				$chatMLMessages[] = $turnMessage;
			}
		}

		return array('messages' => $chatMLMessages);
	}

	public static function exportChats($chats, $params = array())
	{
		$filename = 'chatml-' . date('Y-m-d') . '.jsonl';

		header('Content-type: application/x-ndjson; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);

		$fp = fopen('php://output', 'w');

		foreach ($chats as $chat) {
			$chatObject = $chat;
			if (!is_object($chatObject)) {
				$chatObject = \erLhcoreClassModelChat::fetch($chat, false);
			}

			if (!is_object($chatObject)) {
				continue;
			}

			$payload = self::fromChat($chatObject, $params);
			if (empty($payload['messages'])) {
				continue;
			}

			fwrite($fp, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
		}

		fclose($fp);
		exit;
	}

	private static function resolveRole($message)
	{
		if ((int)$message->user_id === 0) {
			return 'user';
		}

		if ((int)$message->user_id === -2 || (int)$message->user_id > 0) {
			return 'assistant';
		}

		return null;
	}

	private static function isJSONMetaMessage($message)
	{
		if ((int)$message->user_id !== -1 || !isset($message->meta_msg)) {
			return false;
		}

		$metaMessage = json_decode((string)$message->meta_msg, true);
		return isset($metaMessage['content']['attr_options']['as_json']) && $metaMessage['content']['attr_options']['as_json'] == true;
	}

	private static function hasFileSearchCompletedEvent($message)
	{
		return isset($message->meta_msg) && strpos((string)$message->meta_msg, 'response.file_search_call.completed') !== false;
	}

	private static function parseJsonMetaAsChatML($message, & $toolCallNames, & $hasToolCallUsed)
	{
  
		$payload = json_decode((string)$message->msg, true);

		if (!is_array($payload)) {
			 $payload = json_decode((string)'['.$message->msg.']', true);
		}

		if (!is_array($payload)) {
			return array();
		}

		$result = array();

		foreach ($payload as $item) {
			if (!is_array($item) || !isset($item['type'])) {
				continue;
			}

			if ($item['type'] === 'function_call' && !empty($item['name'])) {
				$callId = isset($item['call_id']) && $item['call_id'] !== '' ? (string)$item['call_id'] : ('call_' . md5(json_encode($item)));
				$toolCallNames[$callId] = (string)$item['name'];
				$hasToolCallUsed = true;

				$arguments = isset($item['arguments']) ? $item['arguments'] : '';
				if (is_array($arguments) || is_object($arguments)) {
					$arguments = json_encode($arguments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				} else {
					$arguments = (string)$arguments;
				}

				$result[] = array(
					'role' => 'assistant',
					'tool_calls' => array(
						array(
							'id' => $callId,
							'type' => 'function',
							'function' => array(
								'name' => (string)$item['name'],
								'arguments' => $arguments
							)
						)
					)
				);
			} elseif ($item['type'] === 'function_call_output' && !empty($item['call_id'])) {
				$hasToolCallUsed = true;
				$output = isset($item['output']) ? $item['output'] : '';
				if (is_array($output) || is_object($output)) {
					$output = json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				} else {
					$output = (string)$output;
				}

				$callId = (string)$item['call_id'];
				$result[] = array(
					'role' => 'tool',
					'tool_call_id' => $callId,
					'name' => isset($toolCallNames[$callId]) ? $toolCallNames[$callId] : 'tool',
					'content' => $output
				);
			}
		}

		return $result;
	}
}

?>
