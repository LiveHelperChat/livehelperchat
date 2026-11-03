<?php

namespace LiveHelperChat\mailConv\RestAPI;

use erLhcoreClassModelMailconvMailbox;
use erLhcoreClassModelMailconvMessage;
use erLhcoreClassGenericBotActionRestapi;

/**
 * Template variables and message transcript for Rest API requests executed on a mail conversation.
 *
 * A bot started for a mail conversation (mail.conversation_started[_passive],
 * mail.conversation_reply[_passive]) passes erLhcoreClassModelMailconvMessage as $chat.
 * Chat based logic of erLhcoreClassGenericBotActionRestapi can not build a transcript out of it,
 * so everything mail related is implemented here and the Rest API action only delegates to this class.
 *
 * Transcript block (same syntax as the chat one):
 *
 * {previous_visitor_messages_list_url__20__0}
 *     {separator},{/separator}
 *     {"role": "{{args.item.role}}", "content": {{args.item.msg}}}
 * {/previous_visitor_messages_list_url}
 *
 * The first number is the message limit, the second one (optional) is the offset.
 *
 * Item attributes {{args.item.X}} - X can be any attribute of erLhcoreClassModelMailconvMessage:
 * msg, body, alt_body, subject, from_address, from_name, to_data_front, date, udate,
 * udate_utc, ctime_utc, udate_front, role, is_outgoing, has_attachment, index, ...
 *
 * Item options:
 * {user}...{/user}, {assistant}...{/assistant}       - kept only for the matching message direction
 * {separator}...{/separator}, {start_separator}...{/start_separator}  - kept between the rendered messages, start_separator is kept before the first one
 * {end_separator}...{/end_separator}                 - kept after the last rendered message
 * {not_item_empty__args.item.X}...{/not_item_empty}  - kept only if the value is not empty
 * {is_item_empty__args.item.X}...{/is_item_empty}    - kept only if the value is empty
 * raw_{{args.item.X}} - value escaped for a JSON string, also sensitive_ and raw_sensitive_ variants
 *
 * Message files loop - {{args.item.files}} (or its alias {{args.item.attachments}}), all files
 * of the message are processed:
 *
 * {{args.item.files}}
 *     {separator}, {/separator}
 *     {if_image}{{file.name}} ({{file.width}}x{{file.height}}){/if_image}
 *     {if_not_image}Attachment: {{file.name}}{/if_not_image}
 *     {min_width}500{/min_width}{min_height}500{/min_height}
 * {{/args.item.files}}
 *
 * {{file.X}} - any attribute of erLhcoreClassModelMailconvFile (id, name, size, extension, type,
 * mime, width, height, disposition, caption, ...) plus is_image, size_kb and url.
 * {if_image}...{/if_image} / {if_not_image}...{/if_not_image} - image / not an image
 * {min_width}N{/min_width} / {min_height}N{/min_height} - an image with a smaller
 *     dimension is not rendered at all, other files are always kept. Used as {min_width}
 *     alone it is replaced by the image width (empty for other files), same for the height.
 * {separator}...{/separator}, {end_separator}...{/end_separator}, raw_{{file.X}}, same as above.
 *
 * Conversation files loop - all files of all messages of the conversation:
 *
 * {conversation_files_list__20__0}
 *     {separator},{/separator}
 *     {if_image}{"type": "input_image", "image_url": {{file_body}}}{/if_image}
 *     {if_not_image}{"type": "input_file", "filename": {{file_name}}, "file_data": {{file_body}}}{/if_not_image}
 * {/conversation_files_list}
 *
 * The first number is the file limit, the second one (optional) is the offset. Oldest file
 * comes first, with {separator} between the rendered files, {start_separator} before the first
 * one and {end_separator} after the last one. An empty list renders nothing at all, so a comma
 * in front of the block has to be placed inside {start_separator}...{/start_separator}.
 * {min_width}N{/min_width} and {min_height}N{/min_height} work the same way as in the
 * files loop of a message - an image with a smaller dimension is not rendered at all.
 *
 * File attributes - {{file.X}}, {{item.file.X}} and {{item.X}} are aliases of the same attribute:
 * any attribute of erLhcoreClassModelMailconvFile (id, name, file_name, size, extension, type,
 * width, height, disposition, caption, ...) plus is_image, mime, size_kb, url, download_url and
 * file_body (data url with the base64 encoded content).
 * {{file_body}}, {{file_name}}, {{file_mime}}, {{file_size}}, {{file_url}} and {{file_extension}}
 * are short forms of the most used attributes.
 *
 * Conditions {if_image}...{/if_image} and {if_not_image}...{/if_not_image} keep only images or
 * non images. raw_{{file.X}} returns the value without the JSON escaping.
 *
 * Only erLhcoreClassGenericBotActionRestapi decides when this class is used, so it accepts
 * a mail message only.
 */
class BotRestAPI
{
    /**
     * Replaces message transcript blocks inside the request body
     *
     * @param array $methodSettings Modified in place - transcript blocks are replaced by their content
     * @param array $paramsCustomer
     */
    public static function process(array & $methodSettings, $paramsCustomer = [])
    {
        if (!isset($paramsCustomer['chat'])) {
            return;
        }

        $message = $paramsCustomer['chat'];

        foreach (['body_raw', 'body_raw_file'] as $bodyAttribute) {
            if (isset($methodSettings[$bodyAttribute]) && is_string($methodSettings[$bodyAttribute])) {
                $methodSettings[$bodyAttribute] = self::replaceTranscriptBlocks($methodSettings[$bodyAttribute], $message);
                $methodSettings[$bodyAttribute] = self::replaceFileListBlocks($methodSettings[$bodyAttribute], $message);
            }
        }
    }

    /**
     * Replaces all transcript blocks inside the body
     *
     * @param string $body
     * @param erLhcoreClassModelMailconvMessage $message
     * @return string
     */
    public static function replaceTranscriptBlocks($body, $message)
    {
        $matches = [];

        preg_match_all('/\{previous_visitor_messages_list_url__([0-9]+)(?:__([0-9]+))?\}(.*?)\{\/previous_visitor_messages_list_url\}/is', $body, $matches);

        if (empty($matches[0])) {
            return $body;
        }

        foreach ($matches[0] as $index => $block) {
            $content = self::renderTranscript(
                $matches[3][$index],
                $message,
                (int)$matches[1][$index],
                (isset($matches[2][$index]) && is_numeric($matches[2][$index]) ? (int)$matches[2][$index] : 0)
            );

            // Placeholders of the rendered content must not be processed by the Rest API variable replacement
            $body = str_replace($block, str_replace('{{', '__lhc_bracket__', $content), $body);
        }

        // The chat based processing would drop the whole wrapper together with the rendered content,
        // so it is resolved here - the content is already final
        $body = str_replace(['{if_previous_visitor_messages_list}', '{/if_previous_visitor_messages_list}'], '', $body);

        return $body;
    }

    /**
     * Replaces conversation files blocks inside the body
     *
     * @param string $body
     * @param erLhcoreClassModelMailconvMessage $message
     * @return string
     */
    public static function replaceFileListBlocks($body, $message)
    {
        $matches = [];

        preg_match_all('/\{conversation_files_list__([0-9]+)(?:__([0-9]+))?\}(.*?)\{\/conversation_files_list\}/is', $body, $matches);

        if (empty($matches[0])) {
            return $body;
        }

        foreach ($matches[0] as $index => $block) {
            $content = self::renderFileList(
                $matches[3][$index],
                $message,
                (int)$matches[1][$index],
                (isset($matches[2][$index]) && is_numeric($matches[2][$index]) ? (int)$matches[2][$index] : 0)
            );

            // Placeholders of the rendered content must not be processed by the Rest API variable replacement
            $body = str_replace($block, str_replace('{{', '__lhc_bracket__', $content), $body);
        }

        // The content is final, so the wrapper is just removed
        $body = str_replace(['{if_conversation_files_list}', '{/if_conversation_files_list}'], '', $body);

        return $body;
    }

    /**
     * Renders a single conversation files block - all files of all messages of the conversation
     *
     * @param string $template
     * @param erLhcoreClassModelMailconvMessage $message
     * @param int $limit
     * @param int $offset
     * @return string
     */
    protected static function renderFileList($template, $message, $limit, $offset)
    {
        // Chat only options do not apply to the files of a mail conversation
        $template = str_replace(['{include_previous}', '{include_previous_email}', '{history_as_summary}'], '', $template);

        $messages = erLhcoreClassModelMailconvMessage::getList([
            'sort' => 'id ASC',
            'limit' => false,
            'filter' => [
                'conversation_id' => (int)$message->conversation_id
            ]
        ]);

        // Files are fetched by the message id's of the conversation
        $messageIds = [];

        foreach ($messages as $threadMessage) {
            $messageIds[] = (int)$threadMessage->id;
        }

        $files = [];

        if (!empty($messageIds)) {
            $files = \erLhcoreClassModelMailconvFile::getList([
                'sort' => 'id DESC',
                'limit' => $limit,
                'offset' => $offset,
                'filterin' => [
                    'message_id' => $messageIds
                ]
            ]);
        }

        // Images which do not match the min_width / min_height condition are not rendered at all
        $files = self::filterFilesByDimensions($files, $template);

        // Oldest file first - the same order as the transcript
        $files = array_reverse($files);

        $totalElements = count($files);

        $content = '';
        $counter = 0;

        foreach ($files as $file) {

            $itemTemplate = $template;

            // {start_separator} is kept only before the first rendered file
            if ($counter == 0) {
                $itemTemplate = trim(str_replace(['{start_separator}', '{/start_separator}'], '', $itemTemplate));
            } else {
                $itemTemplate = preg_replace('/\{start_separator\}(.*?)\{\/start_separator\}/ms', '', $itemTemplate);
            }

            if ($counter > 0 && $counter < $totalElements) {
                $itemTemplate = trim(str_replace(['{separator}', '{/separator}'], '', $itemTemplate));
            } else {
                $itemTemplate = preg_replace('/\{separator\}(.*?)\{\/separator\}/ms', '', $itemTemplate);
            }

            if ($counter == $totalElements - 1) {
                $itemTemplate = trim(str_replace(['{end_separator}', '{/end_separator}'], '', $itemTemplate));
            } else {
                $itemTemplate = preg_replace('/\{end_separator\}(.*?)\{\/end_separator\}/ms', '', $itemTemplate);
            }

            $content .= self::renderFileItem($itemTemplate, $file);
            $counter++;
        }

        return $content;
    }

    /**
     * Renders a single transcript block
     *
     * @param string $template
     * @param erLhcoreClassModelMailconvMessage $message
     * @param int $limit
     * @param int $offset
     * @return string
     */
    protected static function renderTranscript($template, $message, $limit, $offset)
    {
        // Chat only options do not apply to mail threads
        $template = str_replace(['{include_previous}', '{include_previous_email}', '{history_as_summary}'], '', $template);

        $messages = array_reverse(erLhcoreClassModelMailconvMessage::getList([
            'sort' => 'id DESC',
            'limit' => $limit,
            'offset' => $offset,
            'filter' => [
                'conversation_id' => (int)$message->conversation_id,
                // Auto-replies, out of office and bounce notifications are not part of the conversation
                'auto_submitted' => erLhcoreClassModelMailconvMessage::AUTO_SUBMITTED_NONE
            ]
        ]));

        $totalElements = count($messages);

        $content = '';
        $counter = 0;

        foreach ($messages as $threadMessage) {

            $isOutgoing = self::isOutgoing($threadMessage);

            // Attachments have to be rendered before the separators of this block are processed
            $itemTemplate = self::renderFiles($template, $threadMessage);

            // {start_separator} is kept only before the first rendered message
            if ($counter == 0) {
                $itemTemplate = trim(str_replace(['{start_separator}', '{/start_separator}'], '', $itemTemplate));
            } else {
                $itemTemplate = preg_replace('/\{start_separator\}(.*?)\{\/start_separator\}/ms', '', $itemTemplate);
            }

            if ($counter > 0 && $counter < $totalElements) {
                $itemTemplate = trim(str_replace(['{separator}', '{/separator}'], '', $itemTemplate));
            } else {
                $itemTemplate = preg_replace('/\{separator\}(.*?)\{\/separator\}/ms', '', $itemTemplate);
            }

            if ($counter == $totalElements - 1) {
                $itemTemplate = trim(str_replace(['{end_separator}', '{/end_separator}'], '', $itemTemplate));
            } else {
                $itemTemplate = preg_replace('/\{end_separator\}(.*?)\{\/end_separator\}/ms', '', $itemTemplate);
            }

            $content .= self::renderTranscriptItem($itemTemplate, $threadMessage, $counter, $isOutgoing);
            $counter++;
        }

        return $content;
    }

    /**
     * Renders a single message of the transcript
     *
     * @param string $template
     * @param erLhcoreClassModelMailconvMessage $message
     * @param int $counter
     * @param bool $isOutgoing
     * @return string
     */
    protected static function renderTranscriptItem($template, $message, $counter, $isOutgoing)
    {
        if ($isOutgoing === true) {
            $template = preg_replace('/\{user\}(.*?)\{\/user\}/ms', '', $template);
            $template = trim(str_replace(['{assistant}', '{/assistant}'], '', $template));
        } else {
            $template = preg_replace('/\{assistant\}(.*?)\{\/assistant\}/ms', '', $template);
            $template = trim(str_replace(['{user}', '{/user}'], '', $template));
        }

        $attributePaths = [];
        $matches = [];

        preg_match_all('/\{(not|is)_item_empty__args\.((?:[^\{\}\}]++|(?R))*)\}/', $template, $matches);
        if (!empty($matches[2])) {
            foreach ($matches[2] as $attributePathRaw) {
                // a||b - both attributes are checked separately
                foreach (explode('||', $attributePathRaw) as $attributePath) {
                    $attributePaths[trim($attributePath)] = true;
                }
            }
        }

        preg_match_all('/\{\{args\.(item\.[^{}]+)\}\}/', $template, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $attributePath) {
                $attributePaths[trim($attributePath)] = true;
            }
        }

        $values = [];
        foreach (array_keys($attributePaths) as $attributePath) {
            if ($attributePath == '' || array_key_exists('{{args.' . $attributePath . '}}', $values)) {
                continue;
            }

            $values['{{args.' . $attributePath . '}}'] = json_encode(self::getItemAttribute($message, $attributePath));
        }

        // 1 based position of the rendered message
        $values['{{args.item.index}}'] = $counter + 1;

        $rawReplace = [];
        $maskParams = ['dep_id' => (int)$message->dep_id, 'type' => \LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::MSG_TYPE_REST];

        foreach ($values as $placeholder => $value) {
            if (str_contains($template, 'raw_sensitive_' . $placeholder)) {
                $rawReplace['raw_sensitive_' . $placeholder] = str_replace('\\/', '/', self::trimOnce(json_encode(\LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage(json_decode($value, true), $maskParams))));
            } elseif (str_contains($template, 'sensitive_' . $placeholder)) {
                $rawReplace['sensitive_' . $placeholder] = json_encode(\LiveHelperChat\Models\LHCAbstract\ChatMessagesGhosting::maskMessage(json_decode($value, true), $maskParams));
            } elseif (str_contains($template, 'raw_' . $placeholder)) {
                $rawReplace['raw_' . $placeholder] = str_replace('\\/', '/', self::trimOnce($value));
            }
        }

        $template = str_replace(array_keys($rawReplace), array_values($rawReplace), $template);
        $template = str_replace(array_keys($values), array_values($values), $template);

        // {not_item_empty__args.item.X}...{/not_item_empty}
        $matches = [];
        preg_match_all('/\{(not|is)_item_empty__args\.(.*?)\}(.*?)\{\/(not|is)_item_empty\}/ms', $template, $matches);

        if (!empty($matches[2])) {
            foreach ($matches[2] as $index => $attributePathRaw) {
                $keep = true;

                foreach (explode('||', $attributePathRaw) as $attributePath) {
                    $placeholder = '{{args.' . trim($attributePath) . '}}';
                    $value = array_key_exists($placeholder, $values) ? json_decode($values[$placeholder], true) : null;
                    $isEmpty = $value === null || (is_array($value) ? count($value) == 0 : trim((string)$value) === '');

                    if (($matches[1][$index] == 'not' && $isEmpty === true) || ($matches[1][$index] == 'is' && $isEmpty === false)) {
                        $keep = false;
                    }
                }

                $template = str_replace($matches[0][$index], $keep === true ? $matches[3][$index] : '', $template);
            }
        }

        return $template;
    }

    /**
     * Renders files of the message: {{args.item.files}}...{{/args.item.files}}.
     * {{args.item.attachments}}...{{/args.item.attachments}} is an alias of the same block.
     *
     * @param string $template
     * @param erLhcoreClassModelMailconvMessage $message
     * @return string
     */
    protected static function renderFiles($template, $message)
    {
        // Both names are processed the same way - all files of the message
        foreach (['files', 'attachments'] as $collection) {

            $matches = [];

            preg_match_all('/\{\{args\.item\.' . $collection . '\}\}(.*?)\{\{\/args\.item\.' . $collection . '\}\}/is', $template, $matches);

            if (empty($matches[0])) {
                continue;
            }

            foreach ($matches[0] as $blockIndex => $fileBlock) {

                $blockTemplate = $matches[1][$blockIndex];

                // Images which do not match the min_width / min_height condition are not rendered at all
                $files = self::filterFilesByDimensions($message->files, $blockTemplate);
                $totalElements = count($files);

                $content = '';
                $counter = 0;

                foreach ($files as $file) {

                    $itemTemplate = $blockTemplate;

                    // {start_separator} is kept only before the first rendered file
                    if ($counter == 0) {
                        $itemTemplate = trim(str_replace(['{start_separator}', '{/start_separator}'], '', $itemTemplate));
                    } else {
                        $itemTemplate = preg_replace('/\{start_separator\}(.*?)\{\/start_separator\}/ms', '', $itemTemplate);
                    }

                    if ($counter > 0 && $counter < $totalElements) {
                        $itemTemplate = trim(str_replace(['{separator}', '{/separator}'], '', $itemTemplate));
                    } else {
                        $itemTemplate = preg_replace('/\{separator\}(.*?)\{\/separator\}/ms', '', $itemTemplate);
                    }

                    if ($counter == $totalElements - 1) {
                        $itemTemplate = trim(str_replace(['{end_separator}', '{/end_separator}'], '', $itemTemplate));
                    } else {
                        $itemTemplate = preg_replace('/\{end_separator\}(.*?)\{\/end_separator\}/ms', '', $itemTemplate);
                    }

                    $content .= self::renderFileItem($itemTemplate, $file);
                    $counter++;
                }

                $template = str_replace($fileBlock, $content, $template);
            }
        }

        return $template;
    }

    /**
     * Removes images which do not match the {min_width} / {min_height} condition of the block.
     * The condition applies to images only, other files are always kept.
     *
     * @param array $files
     * @param string $template
     * @return array
     */
    protected static function filterFilesByDimensions($files, $template)
    {
        $conditions = [];

        foreach (['min_width' => 'width', 'min_height' => 'height'] as $directive => $attribute) {
            $matches = [];

            preg_match_all('~(?<!\{)\{' . $directive . '\}(.*?)\{/' . $directive . '\}(?!\})~ms', $template, $matches);

            foreach ($matches[1] as $minimumValue) {
                $minimumValue = (int)trim($minimumValue);
                $conditions[$attribute] = max(isset($conditions[$attribute]) ? $conditions[$attribute] : 0, $minimumValue);
            }
        }

        if (empty($conditions)) {
            return $files;
        }

        $filtered = [];

        foreach ($files as $file) {

            if (self::isImageFile($file) === false) {
                $filtered[] = $file;
                continue;
            }

            $matchesConditions = true;

            foreach ($conditions as $attribute => $minimumValue) {
                if ((int)$file->{$attribute} < $minimumValue) {
                    $matchesConditions = false;
                    break;
                }
            }

            if ($matchesConditions === true) {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

    /**
     * Renders a single attachment
     *
     * @param string $template
     * @param erLhcoreClassModelMailconvFile $file
     * @return string
     */
    protected static function renderFileItem($template, $file)
    {
        $isImage = self::isImageFile($file);

        // {if_image} keeps images only, {if_not_image} keeps all other files
        if ($isImage === true) {
            $template = preg_replace('~(?<!\{)\{if_not_image\}(.*?)\{/if_not_image\}(?!\})~ms', '', $template);
            $template = preg_replace('~(?<!\{)\{/?if_image\}(?!\})~', '', $template);
        } else {
            $template = preg_replace('~(?<!\{)\{if_image\}(.*?)\{/if_image\}(?!\})~ms', '', $template);
            $template = preg_replace('~(?<!\{)\{/?if_not_image\}(?!\})~', '', $template);
        }

        // {min_width}200{/min_width} / {min_height}200{/min_height} are applied on the files
        // list level, so the tags are just removed here
        foreach (['min_width', 'min_height'] as $directive) {
            $template = preg_replace('~(?<!\{)\{' . $directive . '\}.*?\{/' . $directive . '\}(?!\})~ms', '', $template);
        }

        // {min_width} / {min_height} alone - value of an image
        $template = preg_replace(
            ['~(?<!\{)\{min_width\}(?!\})~', '~(?<!\{)\{min_height\}(?!\})~'],
            [$isImage === true ? (int)$file->width : '', $isImage === true ? (int)$file->height : ''],
            $template
        );

        // {{file.X}}, {{item.file.X}} and {{item.X}} are aliases of the same attachment attribute
        $attributePaths = [];
        $matches = [];

        preg_match_all('/\{\{(?:item\.)?file\.([^{}]+)\}\}/', $template, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $attributePath) {
                $attributePaths[trim($attributePath)] = true;
            }
        }

        preg_match_all('/\{\{item\.([^{}]+)\}\}/', $template, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $attributePath) {
                $attributePaths[trim($attributePath)] = true;
            }
        }

        // Short forms of the most used attributes
        $shortcuts = [
            'file_body' => 'file_body',
            'file_name' => 'name',
            'file_mime' => 'mime',
            'file_size' => 'size',
            'file_url' => 'url',
            'file_extension' => 'extension'
        ];

        foreach ($shortcuts as $shortcut => $attributePath) {
            if (str_contains($template, '{{' . $shortcut . '}}') || str_contains($template, 'raw_{{' . $shortcut . '}}')) {
                $attributePaths[$attributePath] = true;
            }
        }

        $values = [];
        $valuesByAttribute = [];

        foreach (array_keys($attributePaths) as $attributePath) {

            if ($attributePath == '') {
                continue;
            }

            // Value is resolved only once per attachment attribute
            if (!array_key_exists($attributePath, $valuesByAttribute)) {
                $valuesByAttribute[$attributePath] = json_encode(self::getFileAttribute($file, 'file.' . $attributePath));
            }

            foreach (['{{file.' . $attributePath . '}}', '{{item.file.' . $attributePath . '}}', '{{item.' . $attributePath . '}}'] as $placeholder) {
                if (str_contains($template, $placeholder)) {
                    $values[$placeholder] = $valuesByAttribute[$attributePath];
                }
            }
        }

        foreach ($shortcuts as $shortcut => $attributePath) {
            if (isset($valuesByAttribute[$attributePath]) && str_contains($template, '{{' . $shortcut . '}}')) {
                $values['{{' . $shortcut . '}}'] = $valuesByAttribute[$attributePath];
            }
        }

        $rawReplace = [];

        foreach ($values as $placeholder => $value) {
            if (str_contains($template, 'raw_' . $placeholder)) {
                $rawReplace['raw_' . $placeholder] = str_replace('\\/', '/', self::trimOnce($value));
            }
        }

        $template = str_replace(array_keys($rawReplace), array_values($rawReplace), $template);
        $template = str_replace(array_keys($values), array_values($values), $template);

        return $template;
    }

    /**
     * Resolves a single {{file.X}} attribute
     *
     * @param erLhcoreClassModelMailconvFile $file
     * @param string $attributePath file.attribute
     * @return mixed
     */
    protected static function getFileAttribute($file, $attributePath)
    {
        switch ($attributePath) {
            case 'file.is_image':
                return self::isImageFile($file);

            case 'file.exists':
                return $file->file_path_server != '' && file_exists($file->file_path_server);

            case 'file.file_body':
                return $file->file_path_server != '' && file_exists($file->file_path_server) ? $file->file_body : '';

            case 'file.mime':
                return trim(explode(';', (string)$file->type)[0]);

            case 'file.size_kb':
                return (int)round((int)$file->size / 1024);

            case 'file.url':
            case 'file.download_url':
                return \erLhcoreClassSystem::getHost() . \erLhcoreClassDesign::baseurl('mailconv/inlinedownload') . '/' . $file->id . '/' . $file->conversation_id;
        }

        $attribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(['file' => $file], $attributePath, '.');

        return $attribute['found'] === true ? $attribute['value'] : null;
    }

    /**
     * Attachment is an image
     *
     * @param erLhcoreClassModelMailconvFile $file
     * @return bool
     */
    public static function isImageFile($file)
    {
        return in_array(strtolower((string)$file->extension), \erLhcoreClassMailconvParser::IMAGE_EXTENSIONS) ||
            strpos(trim(explode(';', (string)$file->type)[0]), 'image/') === 0;
    }

    /**
     * Resolves a single {{args.item.X}} attribute of a mail message
     *
     * @param erLhcoreClassModelMailconvMessage $message
     * @param string $attributePath item.attribute
     * @return mixed
     */
    protected static function getItemAttribute($message, $attributePath)
    {
        switch ($attributePath) {
            case 'item.msg':
                return self::getMsg($message);

            case 'item.role':
                return self::isOutgoing($message) === true ? 'assistant' : 'user';

            case 'item.is_outgoing':
                return self::isOutgoing($message);

            case 'item.udate_utc':
                return (int)$message->udate > 0 ? gmdate('Y-m-d H:i', (int)$message->udate) . ' UTC' : '';

            case 'item.ctime_utc':
                return (int)$message->ctime > 0 ? gmdate('Y-m-d H:i', (int)$message->ctime) . ' UTC' : '';
        }

        $attribute = erLhcoreClassGenericBotActionRestapi::extractAttribute(['item' => $message], $attributePath, '.');

        return $attribute['found'] === true ? $attribute['value'] : null;
    }

    /**
     * Plain text version of the message
     *
     * @param erLhcoreClassModelMailconvMessage $message
     * @return string
     */
    public static function getMsg($message)
    {
        $msg = (string)$message->alt_body;

        if (trim($msg) == '') {
            $msg = html_entity_decode(strip_tags(preg_replace('~<br\s*/?>|</p>|</div>~i', "\n", (string)$message->body)), ENT_QUOTES, 'UTF-8');
        }

        // &nbsp; entities and non breaking spaces (also produced by html_entity_decode) are regular spaces
        $msg = str_replace("\xC2\xA0", ' ', str_ireplace(['&nbsp;', '&#160;', '&#xa0;'], ' ', $msg));

        return trim($msg);
    }

    /**
     * Message was sent by us (mailbox owner) - reply, forward or operator started conversation
     *
     * @param erLhcoreClassModelMailconvMessage $message
     * @return bool
     */
    public static function isOutgoing($message)
    {
        if ((int)$message->response_type === erLhcoreClassModelMailconvMessage::RESPONSE_INTERNAL) {
            return true;
        }

        $mailbox = $message->mailbox;

        return $mailbox instanceof erLhcoreClassModelMailconvMailbox &&
            (string)$mailbox->mail != '' &&
            strcasecmp((string)$message->from_address, (string)$mailbox->mail) === 0;
    }

    /**
     * Trims a single leading and trailing double quote (same behaviour as in Rest API action)
     *
     * @param string $string
     * @return string
     */
    protected static function trimOnce($string)
    {
        if (!is_string($string) || $string === '') {
            return $string;
        }

        if ($string[0] == '"') {
            $string = substr($string, 1);
        }

        if ($string !== '' && $string[strlen($string) - 1] == '"') {
            $string = substr($string, 0, strlen($string) - 1);
        }

        return $string;
    }
}
