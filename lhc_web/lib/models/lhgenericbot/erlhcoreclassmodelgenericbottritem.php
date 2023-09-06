<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelGenericBotTrItem {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_tr_item';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'group_id' => $this->group_id,
            'identifier' => $this->identifier,
            'translation' => $this->translation,
            'auto_translate' => $this->auto_translate
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'translation_array':
                $attr = str_replace('_array','',$var);
                if (!empty($this->{$attr})) {
                    $jsonData = json_decode($this->{$attr},true);
                    if ($jsonData !== null) {
                        $this->{$var} = $jsonData;
                    } else {
                        $this->{$var} = array('default' => ($this->{$attr} != '' ? $this->{$attr} : null), 'items' => array());
                    }
                } else {
                    $this->{$var} = array('default' => '', 'items' => array());
                }
                return $this->{$var};
                break;

            default:
                break;
        }
    }

    /**
     * @desc translate auto responder if translation by chat exists
     *
     * @param $locale
     */
    public function translateByChat($locale) {

        $this->translation_front = $this->translation_array['default'];

        if ($locale === null) {
            return;
        }

        // Try to find exact match
        foreach ($this->translation_array['items'] as $data) {
            if (in_array($locale, $data['languages'])) {
                $this->translation_front = $data['message'];
                return;
            }
        }

        // Try to match general match by first two letters
        $localeShort = explode('-',$locale)[0];
        foreach ($this->translation_array['items'] as $data) {
            if (in_array($localeShort, $data['languages'])) {
                $this->translation_front = $data['message'];
                return;
            }
        }

        if ($this->auto_translate == 1) {
            $translationGroup = erLhcoreClassModelGenericBotTrGroup::fetch($this->group_id);
            if ($translationGroup instanceof erLhcoreClassModelGenericBotTrGroup && $translationGroup->bot_lang != '') {
                try {
                    $this->translation_front = erLhcoreClassTranslate::translateTo($this->translation_front, $translationGroup->bot_lang, $localeShort);
                } catch (Exception $e) {
                    erLhcoreClassLog::write( $e->getMessage() . "\n" . $e->getTraceAsString(),
                        ezcLog::SUCCESS_AUDIT,
                        array(
                            'source' => 'lhc',
                            'category' => 'translation_item',
                            'line' => $e->getLine(),
                            'file' => $e->getFile(),
                            'object_id' => $this->id
                        )
                    );
                }
            }
        }

    }

    public $id = null;
    public $name = '';
    public $identifier = '';
    public $translation = '';
    public $group_id = 0;
    public $auto_translate = 0;
    public $translation_front = '';
}