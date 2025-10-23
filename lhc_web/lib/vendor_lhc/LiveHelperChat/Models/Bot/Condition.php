<?php

namespace LiveHelperChat\Models\Bot;
#[\AllowDynamicProperties]
class Condition {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_bot_condition';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'name ASC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'identifier' => $this->identifier,
            'configuration' => $this->configuration
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function isValid($params)
    {

        $conditions = json_decode($this->configuration,true);
        $conditionsRefactored = [];

        $comparatorMatching = [
            '=' => 'eq',
            '!=' => 'neq',
            '<' => 'lt',
            '<=' => 'lte',
            '>=' => 'gte',
            '>' => 'gt',
            'like' => 'like',
            'notlike' => 'notlike',
            'contains' => 'contains',
            'in_list' => 'in_list',
            'in_list_lowercase' => 'in_list_lowercase',
            'not_in_list' => 'not_in_list',
            'not_in_list_lowercase' => 'not_in_list_lowercase',
            'start_or' => 'start_or'
        ];

        foreach ($conditions as $condition) {
            $conditionsRefactored[] = [
                'content' => [
                    'attr' => $condition['field'],
                    'attr_math' => $condition['field_math'] ?? false,
                    'comp' => $comparatorMatching[$condition['comparator']],
                    'val' => $condition['value'],
                    'val_math' => $condition['value_math'] ?? false
                ]
            ];
        }

        $replaceArray = [];

        if (isset($params['replace_array']) && !empty($params['replace_array'])){
            $replaceArray['replace_array'] = $params['replace_array'];
        }

        $response = \erLhcoreClassGenericBotActionConditions::process(
            $params['chat'],
            [
                'content' => [
                    'conditions' => $conditionsRefactored
                ],
                'current_trigger' => null
            ],
            null,
            $replaceArray
        );

        return isset($response['status']) && $response['status'] == 'stop';
    }

    public function __get($var)
    {
        switch ($var) {

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $identifier = '';
    public $configuration = '';
}