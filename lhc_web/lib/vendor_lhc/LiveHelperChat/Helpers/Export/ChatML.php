<?php

namespace LiveHelperChat\Helpers\Export;

class ChatML
{
	public static function fromChat($chat, $params = array())
	{
		$lastMessages = isset($params['last_messages']) && (int)$params['last_messages'] > 0 ? (int)$params['last_messages'] : 15;
		$excludeOperatorMessages = isset($params['exclude_operator_messages']) && $params['exclude_operator_messages'] == true;
		$onlyWithToolCalls = isset($params['only_with_tool_calls']) && $params['only_with_tool_calls'] == true;
		$tools = self::normalizeToolsDefinition(isset($params['tools']) ? $params['tools'] : array());
		$toolCallNames = array();
		$hasToolCallUsed = false;

		$messages = \erLhcoreClassModelmsg::getList(array(
            'filternotlikefields' => [/*['meta_msg' => '"debug":true'],*/['meta_msg' => '{"content":{"typing"'],['meta_msg' => '{"content":{"execute_js"']],
			'limit' => 150,
			'sort' => 'id ASC',
			'filter' => array('chat_id' => $chat->id)
		));


		$messages = array_values($messages);
		if ($lastMessages > 0 && count($messages) > $lastMessages) {
			return array('messages' => [], 'tools' => []); // Avoid returning incomplete chats
			//$messages = array_slice($messages, 0, $lastMessages);
		}

		if (empty($tools)) {
			$tools = self::extractToolsDefinitionFromMessages($messages);
		}

		$turns = array();
		$currentTurn = null;
		$hasBotMessageLogged = false;
		$transferToolCallInjected = false;
		$includeNextBotMessageAfterFileSearch = false;

		foreach ($messages as $message) {
            $message->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $message->msg);

			/*if ($includeNextBotMessageAfterFileSearch === true) {
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
			}*/

			if (self::hasFileSearchCompletedEvent($message)) {
				if ($currentTurn === null) {
					$currentTurn = array();
				}

				$toolCallId = 'file_search_' . (int)$chat->id . '_' . (int)$message->id;
				$currentTurn[] = array(
					'role' => 'assistant',
					'content' => '',
					'tool_calls' => array(
						array(
							'id' => $toolCallId,
							'type' => 'function',
							'function' => array(
								'name' => 'file_search',
								'arguments' => '{"question":"[question query from visitor message]"}'
							)
						)
					)
				);
				$hasToolCallUsed = true;

				$currentTurn[] = array(
					'role' => 'tool',
					'tool_call_id' => $toolCallId,
					'name' => 'file_search',
					'content' => '{"status":"success","message":"[content will be returned from file search]"}'
				);

				$includeNextBotMessageAfterFileSearch = true;
				continue;
			}

			if (self::isJSONMetaMessage($message)) {
				$jsonMessages = self::parseJsonMetaAsChatML($message, $toolCallNames, $hasToolCallUsed, $tools);
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
						'content' => '',
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
					$turns[] = self::normalizeTurnMessages($currentTurn);
				}

				$currentTurn = array(
					array('role' => 'user', 'content' => $content)
				);
			} elseif ($currentTurn !== null) {
				if ($injectTransferToolCall) {
					$toolCallId = 'transfer_to_operator_' . (int)$chat->id . '_' . (int)$message->id;
					$currentTurn[] = array(
						'role' => 'assistant',
						'content' => '',
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
			$turns[] = self::normalizeTurnMessages($currentTurn);
		}

		if (empty($turns)) {
			return array('messages' => array(), 'tools' => $tools);
		}

		if ($onlyWithToolCalls === true && $hasToolCallUsed === false) {
			return array('messages' => array(), 'tools' => $tools);
		}

		$chatMLMessages = array();

		if (isset($params['system_prompt']) && $params['system_prompt'] !== '') {
			$chatMLMessages[] = self::appendTrainOnTurn(array('role' => 'system', 'content' => (string)$params['system_prompt']));
		}

		foreach ($turns as $turn) {
			foreach ($turn as $turnMessage) {
				$chatMLMessages[] = self::appendTrainOnTurn($turnMessage);
			}
		}

		$chatMLMessages = self::removeDuplicateToolCalls($chatMLMessages);

		$chatMLMessages = self::removeConsecutiveAssistantMessages($chatMLMessages);

		return array('messages' => $chatMLMessages, 'tools' => $tools);
	}

	private static function removeDuplicateToolCalls($messages)
	{
		$seenToolCallIds = array();
		$indicesToRemove = array();

		for ($i = count($messages) - 1; $i >= 0; $i--) {
			$message = $messages[$i];
			if (!isset($message['role']) || $message['role'] !== 'assistant' || !isset($message['tool_calls']) || !is_array($message['tool_calls'])) {
				continue;
			}

			foreach ($message['tool_calls'] as $toolCall) {
				$callId = isset($toolCall['id']) ? (string)$toolCall['id'] : '';
				if ($callId === '') {
					continue;
				}

				if (isset($seenToolCallIds[$callId])) {
					$indicesToRemove[$i] = true;
					break;
				}

				$seenToolCallIds[$callId] = true;
			}
		}

		if (empty($indicesToRemove)) {
			return $messages;
		}

		return array_values(array_filter($messages, function($index) use ($indicesToRemove) {
			return !isset($indicesToRemove[$index]);
		}, ARRAY_FILTER_USE_KEY));
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
		return (isset($metaMessage['content']['attr_options']['as_json']) && $metaMessage['content']['attr_options']['as_json'] == true) || isset($metaMessage['content']['html']['debug']);
	}

	private static function hasFileSearchCompletedEvent($message)
	{
		return isset($message->meta_msg) && strpos((string)$message->meta_msg, 'response.file_search_call.completed') !== false && strpos((string)$message->meta_msg, 'response.function_call_arguments.done') === false;
	}

	private static function normalizeTurnMessages($turn)
	{
		$normalizedTurn = array();

		foreach ($turn as $turnMessage) {
			if (
				isset($turnMessage['role'], $turnMessage['tool_calls']) &&
				$turnMessage['role'] === 'assistant' &&
				(!isset($turnMessage['content']) || $turnMessage['content'] === '') &&
				!empty($normalizedTurn)
			) {
				$previousMessage = $normalizedTurn[count($normalizedTurn) - 1];

				if (
					isset($previousMessage['role'], $previousMessage['content']) &&
					$previousMessage['role'] === 'assistant' &&
					!isset($previousMessage['tool_calls']) &&
					$previousMessage['content'] !== ''
				) {
					$turnMessage['content'] = $previousMessage['content'];
					$normalizedTurn[count($normalizedTurn) - 1] = $turnMessage;
					continue;
				}
			}

			$normalizedTurn[] = $turnMessage;
		}

		return $normalizedTurn;
	}

	private static function appendTrainOnTurn($message)
	{
		$message['train_on_turn'] = isset($message['role']) && $message['role'] === 'assistant';

		return $message;
	}

	private static function removeConsecutiveAssistantMessages($messages)
	{
		$normalizedMessages = array();

		foreach ($messages as $message) {
			$lastMessage = !empty($normalizedMessages) ? $normalizedMessages[count($normalizedMessages) - 1] : null;
			$insertedToolMessages = false;

			if (
				isset($message['role']) &&
				$message['role'] === 'assistant' &&
				is_array($lastMessage) &&
				isset($lastMessage['role']) &&
				$lastMessage['role'] === 'assistant' &&
				isset($lastMessage['tool_calls']) &&
				is_array($lastMessage['tool_calls']) &&
				!empty($lastMessage['tool_calls'])
			) {
				foreach ($lastMessage['tool_calls'] as $toolCall) {
					$normalizedMessages[] = array(
						'role' => 'tool',
						'tool_call_id' => isset($toolCall['id']) ? (string)$toolCall['id'] : '',
						'name' => isset($toolCall['function']['name']) ? (string)$toolCall['function']['name'] : 'tool',
						'content' => '[response from tool]',
						'train_on_turn' => false
					);
				}

				$insertedToolMessages = true;
			}

			if (
				isset($message['role']) &&
				$message['role'] === 'assistant' &&
				is_array($lastMessage) &&
				isset($lastMessage['role']) &&
				$lastMessage['role'] === 'assistant' &&
				$insertedToolMessages === false
			) {
				continue;
			}

			$normalizedMessages[] = $message;
		}

		return $normalizedMessages;
	}

	private static function parseJsonMetaAsChatML($message, & $toolCallNames, & $hasToolCallUsed, & $tools = array())
	{
  
		$payload = json_decode((string)$message->msg, true);

		if (!is_array($payload)) {
			 $payload = json_decode((string)'['.$message->msg.']', true);
		}

		if (!is_array($payload)) {
			$payload = json_decode($message->meta_msg,true);
			if (isset($payload['content']['html']['debug'])){
				$debugContent = json_decode($payload['content']['html']['content'],true);
				if (empty($tools)) {
					$tools = self::extractToolsDefinitionFromMessage($message);
				}
				if (isset($debugContent['return_content']['output'])){
					$payload = $debugContent['return_content']['output'];
				} else if (isset($debugContent['stream']['content_raw'])) {
					$debugContent = json_decode($debugContent['stream']['content_raw'],true);
					if (isset($debugContent['item']['type'])) {
						$payload = [$debugContent['item']];
					}
				}
			}
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
					'content' => '',
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

	private static function extractToolsDefinitionFromMessages($messages)
	{
		foreach ($messages as $message) {
			$tools = self::extractToolsDefinitionFromMessage($message);
			if (!empty($tools)) {
				return $tools;
			}
		}

		return array();
	}

	private static function extractToolsDefinitionFromMessage($message)
	{
		if (!isset($message->meta_msg) || $message->meta_msg === '') {
			return array();
		}

		$payload = json_decode((string)$message->meta_msg, true);
		if (!is_array($payload) || !isset($payload['content']['html']['debug'])) {
			return array();
		}

		$debugContent = json_decode((string)$payload['content']['html']['content'], true);
		if (
			!is_array($debugContent) ||
			!isset($debugContent['params_request']['body']['tools']) ||
			!is_array($debugContent['params_request']['body']['tools'])
		) {
			return array();
		}

		return self::normalizeToolsDefinition($debugContent['params_request']['body']['tools']);
	}

	private static function normalizeToolsDefinition($tools)
	{
		if (is_string($tools) && trim($tools) !== '') {
			$decodedTools = json_decode($tools, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($decodedTools)) {
				$tools = $decodedTools;
			}
		}

		if (is_array($tools)) {
			foreach ($tools as & $tool) {
				if (isset($tool['type']) && $tool['type'] === 'file_search') {
					$tool = array(
						'type' => 'function',
						'function' => array(
							'name' => 'file_search',
							'description' => 'Fetches information about visitor question from local knowledge base. Links should be in markdown style.',
							'parameters' => array(
								'type' => 'object',
								'properties' => array(
									'question' => array(
										'type' => 'string',
										'description' => 'Visitor question. Question should be translated to english by ai. Question should contain only main keywords you think should be relevant to question.'
									)
								),
								'required' => array('question')
							)
						)
					);
				} elseif (isset($tool['type']) && $tool['type'] === 'function' && !isset($tool['function']) && isset($tool['name'])) {
					// Wrap flat function definition into the nested OpenAI-compatible format
					$functionDef = array('name' => $tool['name']);
					if (isset($tool['description'])) {
						$functionDef['description'] = $tool['description'];
					}
					if (isset($tool['parameters'])) {
						$functionDef['parameters'] = $tool['parameters'];
					}
					$tool = array(
						'type' => 'function',
						'function' => $functionDef
					);
				}

				// Ensure `properties` is always a JSON object `{}`, never an array `[]`
				if (
					isset($tool['function']['parameters']['properties']) &&
					is_array($tool['function']['parameters']['properties']) &&
					empty($tool['function']['parameters']['properties'])
				) {
					$tool['function']['parameters']['properties'] = new \stdClass();
				}
			}

			return array_values($tools);
		}

		return array();
	}
}

?>
