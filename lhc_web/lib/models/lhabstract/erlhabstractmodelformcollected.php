<?php
#[\AllowDynamicProperties]
class erLhAbstractModelFormCollected
{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_form_collected';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'form_id' => $this->form_id,
            'ctime' => $this->ctime,
            'ip' => $this->ip,
            'content' => $this->content,
            'identifier' => $this->identifier,
            'custom_fields' => $this->custom_fields,
            'chat_id' => $this->chat_id,
            'user_id' => $this->user_id,
            'attr_int_1' => $this->attr_int_1,
            'attr_int_2' => $this->attr_int_2,
            'attr_int_3' => $this->attr_int_3,
        );

        return $stateArray;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'left_menu':
                $this->left_menu = '';
                return $this->left_menu;

            case 'ctime_front':
                return $this->ctime_front = date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);

            case 'ctime_full_front':
                return $this->ctime_full_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);

            case 'content_array':
                $content = json_decode($this->content,true);
                if ($content === null) {
                    $content = unserialize($this->content);
                }
                return $this->content_array = $content;

            case 'custom_fields_array':
                return $this->custom_fields_array = $this->custom_fields != '' ? json_decode($this->custom_fields, true) : [];

            case 'form':
                return $this->form = erLhAbstractModelForm::fetch($this->form_id);

            case 'user':
       		   $this->user = false;
       		   if ($this->user_id > 0) {
       		   		try {
       		   			$this->user = erLhcoreClassModelUser::fetch($this->user_id,true);
       		   		} catch (Exception $e) {
       		   			$this->user = false;
       		   		}
       		   }
       		   return $this->user;

            case 'chat':
                $this->chat = false;
                if ($this->chat_id > 0) {
                    try {
                        $this->chat = erLhcoreClassModelChat::fetch($this->chat_id,true);
                    } catch (Exception $e) {
                        $this->chat = false;
                    }
                }
                return $this->chat;

            case 'form_content':
                return $this->getFormattedContent();
            default:
                break;
        }
    }

    public function getFormattedContent()
    {
        $dataCollected = array();
        foreach ($this->content_array as $nameAttr => $contentArray) {
            
            if ($nameAttr === 'lhc_field_changes') continue;

            $nameLiteral = $contentArray['definition']['name_literal'] ?? 'n/a';
            if (isset($contentArray['definition']['type']) && $contentArray['definition']['type'] == 'file') {
                $dataCollected[] = $nameLiteral . " - " . erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect('user/login') . '/(r)/' . rawurlencode(base64_encode('form/download/' . $this->id . '/' . $nameAttr));
            } elseif (isset($contentArray['definition']['type']) && $contentArray['definition']['type'] == 'checkbox') {
                $dataCollected[] = $nameLiteral . " - " . ($contentArray['value'] == 1 ? 'Y' : 'N');
            } else {
                $dataCollected[] = $nameLiteral . " - " . $contentArray['value'];
            }
        }

        return implode("\n", $dataCollected);
    }

    public function getAttrValue($attrDesc)
    {
        $attrs = explode(',', $attrDesc);

        $attrCollected = array();

        foreach ($attrs as $attr) {
            if (strpos($attr, 'prefix__') === 0) {
                if (!empty($attrCollected)) {
                    $lastIdx = count($attrCollected) - 1;
                    $attrCollected[$lastIdx] = substr($attr, 8) . $attrCollected[$lastIdx];
                }
            } elseif (strpos($attr, 'suffix__') === 0) {
                if (!empty($attrCollected)) {
                    $lastIdx = count($attrCollected) - 1;
                    $attrCollected[$lastIdx] .= substr($attr, 8);
                }
            } elseif (isset($this->content_array[$attr]['value'])) {
                $attrCollected[] = $this->content_array[$attr]['value'];
            }
        }

        return implode(', ', $attrCollected);
    }

    public function beforeRemove()
    {
        foreach ($this->content_array as $key => $content) {
            if ($content['definition']['type'] == 'file') {

                if ($content['filename'] != '') {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('form.remove_file', array('filepath' => $content['filepath'], 'filename' => $content['filename']));
                }

                if ($content['filepath'] != '' && file_exists($content['filepath'] . $content['filename'])) {
                    unlink($content['filepath'] . $content['filename']);
                    erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/', str_replace('var/', '', $content['filepath']));
                }
            }
        }
    }

    public $id = null;
    public $form_id = null;
    public $ctime = null;
    public $ip = '';
    public $content = '';
    public $identifier = '';
    public $custom_fields = '';
    public $chat_id = 0;
    // Operator who filled a form
    public $user_id = 0;
    public $attr_int_1 = 0;
    public $attr_int_2 = 0;
    public $attr_int_3 = 0;

}

?>