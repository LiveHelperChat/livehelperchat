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

    public function afterParse($params)
    {

        $matchesValues = [];
        preg_match_all('~\{(not|is)_empty__args\.([a-zA-Z0-9_\.]+(?:\.\{[a-zA-Z0-9_]+\})?)\}~', $this->translation_front, $matchesValues);
        $replaceVariables = [];

        if (!empty($matchesValues[0])) {
            foreach ($matchesValues[0] as $indexElement => $elementValue) {
                $params['args']['chat'] = isset($params['chat']) ? $params['chat'] : null;
                $valueAttribute = erLhcoreClassGenericBotActionRestapi::extractAttribute($params['args'], $matchesValues[2][$indexElement], '.');
                $replaceVariables['{{' . $matchesValues[2][$indexElement] . '}}'] = $valueAttribute['found'] ? $valueAttribute['value'] : null;
            }
        }

        if (isset($this->translation_front) && !empty($replaceVariables)) {
            $matchesExtension = [];
            preg_match_all('/\{(not|is)_empty__args\.([a-zA-Z0-9_\.]+(?:\.\{[a-zA-Z0-9_]+\})?)\}(.*?)\{\/(not|is)_empty\}/ms', $this->translation_front, $matchesExtension);
            if (!empty($matchesExtension[2])) {
                foreach ($matchesExtension[2] as $indexExtension => $varCheck) {
                    $varsCheck = explode('||', $varCheck);
                    $allFilled = true;
                    foreach ($varsCheck as $varCheckReplace) {
                        if (
                            ($matchesExtension[1][$indexExtension] == 'not' && empty($replaceVariables['{{'.$varCheckReplace.'}}']))
                            ||
                            ($matchesExtension[1][$indexExtension] == 'is' && !empty($replaceVariables['{{'.$varCheckReplace.'}}']))
                        ) {
                            $allFilled = false;
                        }
                    }
                    if ($allFilled) {
                        $this->translation_front = str_replace($matchesExtension[0][$indexExtension],$matchesExtension[3][$indexExtension], $this->translation_front);
                    } else {
                        $this->translation_front = str_replace($matchesExtension[0][$indexExtension],'',  $this->translation_front);
                    }
                }
            }
        }

        $this->translation_front = trim($this->translation_front);
    }

    /**
     * @desc translate auto responder if translation by chat exists
     *
     * @param $locale
     */
    public function translateByChat($locale, $params = []) {

        $this->translation_front = $this->translation_array['default'];
        $this->afterParse($params);

        if ($locale === null) {
            return;
        }

        // Try to find exact match
        foreach ($this->translation_array['items'] as $data) {
            if (in_array($locale, $data['languages'])) {
                $this->translation_front = $data['message'];
                $this->afterParse($params);
                return;
            }
        }

        // Try to match general match by first two letters
        $localeShort = explode('-',$locale)[0];
        foreach ($this->translation_array['items'] as $data) {
            if (in_array($localeShort, $data['languages'])) {
                $this->translation_front = $data['message'];
                $this->afterParse($params);
                return;
            }
        }

        if ($this->auto_translate == 1) {
            $translationGroup = erLhcoreClassModelGenericBotTrGroup::fetch($this->group_id);
            if ($translationGroup instanceof erLhcoreClassModelGenericBotTrGroup && $translationGroup->bot_lang != '') {
                try {
                    $this->translation_front = erLhcoreClassTranslate::translateTo($this->translation_front, $translationGroup->bot_lang, $localeShort);
                    $this->afterParse($params);
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