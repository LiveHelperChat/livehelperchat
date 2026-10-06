<?php

namespace LiveHelperChat\Helpers\Bot;

class MatchedConditions
{
    // Holds only the latest matched condition. Exposed in messages as {bot_condition.*} variables.
    public static $botCondition = [];

    // Ready to use {bot_condition.*} replacements, built from $botCondition while setting.
    public static $botConditionReplace = [];

    /**
     * @desc Stores the latest matched condition details. Only latest matched condition is kept.
     *
     * @param string $variable Condition variable/attribute e.g {args.chat.subject}
     * @param mixed $value Resolved value of the condition variable
     * @param mixed $keyword Keyword/value used for comparison
     */
    public static function setLastMatchedCondition($variable, $value = '', $keyword = '', $comp = '')
    {
        $value = self::normalizeConditionValue($value);
        $keyword = self::normalizeConditionValue($keyword);

        self::$botCondition = [
            'variable' => str_replace(['{','}'],'',(string)$variable),
            'variable_value' => $value,
            'variable_value_partial' => self::getConditionPartialValue($value, $keyword),
            'variables_keyword' => $keyword,
            'comp' => $comp,
        ];

        // Build {bot_condition.*} replacements on the fly, so consumers don't have to hardcode keys.
        self::$botConditionReplace = [];
        foreach (self::$botCondition as $key => $valueItem) {
            self::$botConditionReplace['{bot_condition.' . $key . '}'] = $valueItem;
        }
    }

    /**
     * @desc Extracts a partial value around the matched keyword. 3 words before and 3 words after by default.
     *
     * @param string $value
     * @param string $keyword
     * @param int $wordsBefore
     * @param int $wordsAfter
     * @return string
     */
    public static function getConditionPartialValue($value, $keyword, $wordsBefore = 3, $wordsAfter = 3)
    {
        if (!is_string($value) || !is_string($keyword) || $value === '' || $keyword === '') {
            return $value;
        }

        $position = mb_stripos($value, $keyword);

        if ($position === false) {
            return $value;
        }

        $matchedKeyword = mb_substr($value, $position, mb_strlen($keyword));

        $beforeWords = preg_split('/\s+/u', trim(mb_substr($value, 0, $position)), -1, PREG_SPLIT_NO_EMPTY);
        $afterWords = preg_split('/\s+/u', trim(mb_substr($value, $position + mb_strlen($keyword))), -1, PREG_SPLIT_NO_EMPTY);

        $beforeWords = array_slice($beforeWords ?: [], -$wordsBefore);
        $afterWords = array_slice($afterWords ?: [], 0, $wordsAfter);

        return trim(implode(' ', array_merge($beforeWords, [$matchedKeyword], $afterWords)));
    }

    /**
     * @desc Normalizes condition values so they can be safely exposed as string variables.
     *
     * @param mixed $value
     * @return string
     */
    private static function normalizeConditionValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        if (is_bool($value)) {
            return $value ? '1' : '';
        }

        if ($value === null) {
            return '';
        }

        return (string)$value;
    }
}
