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
            'matched_keyword' => self::getMatchedKeywordForComp($value, $keyword, $comp),
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
     * For "Text like" conditions the keyword may be a list of alternatives (e.g. "Friday,Thursday")
     * or a combination (e.g. "edas && em"). The whole keyword string may never appear in the value,
     * so the individual alternative that actually matched is located and used as the anchor instead.
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

        list($matchedKeyword, $position) = self::findMatchedKeyword($value, $keyword);

        if ($position === false || $matchedKeyword === '') {
            return $value;
        }

        $matchedLength = mb_strlen($matchedKeyword);

        $beforeWords = preg_split('/\s+/u', trim(mb_substr($value, 0, $position)), -1, PREG_SPLIT_NO_EMPTY);
        $afterWords = preg_split('/\s+/u', trim(mb_substr($value, $position + $matchedLength)), -1, PREG_SPLIT_NO_EMPTY);

        $beforeWords = array_slice($beforeWords ?: [], -$wordsBefore);
        $afterWords = array_slice($afterWords ?: [], 0, $wordsAfter);

        return trim(implode(' ', array_merge($beforeWords, [$matchedKeyword], $afterWords)));
    }

    /**
     * @desc Returns the exact keyword that matched within a "Text like"/"Text not like" pattern.
     *
     * For "Text like" conditions the keyword can be a list of alternatives (e.g. "Friday,Thursday").
     * This returns the alternative that actually matched so it can be shown to the operator or used
     * in messages. For "Text not like" nothing is supposed to match, so an empty string is returned.
     * Any other comparator returns an empty string as keyword matching does not apply there.
     *
     * @param string $value
     * @param string $keyword
     * @param string $comp
     * @return string
     */
    public static function getMatchedKeywordForComp($value, $keyword, $comp = '')
    {
        if (in_array($comp, ['like', 'notlike'])) {
            return self::getMatchedKeyword($value, $keyword);
        }

        return '';
    }

    /**
     * @desc Returns the exact keyword from the pattern that is present in the value, or an empty string.
     *
     * @param string $value
     * @param string $keyword
     * @return string
     */
    public static function getMatchedKeyword($value, $keyword)
    {
        if (!is_string($value) || !is_string($keyword) || $value === '' || $keyword === '') {
            return '';
        }

        list($matchedKeyword) = self::findMatchedKeyword($value, $keyword);

        return $matchedKeyword;
    }

    /**
     * @desc Finds which keyword actually matched the value and its position.
     *
     * It first tries the whole keyword verbatim. If that is not present (e.g. "Friday,Thursday"
     * while the value contains only "Thursday"), it falls back to the individual alternatives.
     * When several alternatives are present the earliest one in the value wins.
     *
     * @param string $value
     * @param string $keyword
     * @return array [matchedKeyword, position|false]
     */
    private static function findMatchedKeyword($value, $keyword)
    {
        // The whole keyword is present in the value.
        $position = mb_stripos($value, $keyword);
        if ($position !== false) {
            return [mb_substr($value, $position, mb_strlen($keyword)), $position];
        }

        $matchedKeyword = '';
        $matchedPosition = false;

        foreach (self::getKeywordCandidates($keyword) as $candidate) {
            $position = mb_stripos($value, $candidate);
            if ($position !== false && ($matchedPosition === false || $position < $matchedPosition)) {
                $matchedKeyword = mb_substr($value, $position, mb_strlen($candidate));
                $matchedPosition = $position;
            }
        }

        return [$matchedKeyword, $matchedPosition];
    }

    /**
     * @desc Extracts individual keyword candidates from a "Text like" pattern.
     *
     * Supports comma separated alternatives ("Friday,Thursday") and "&&" combinations
     * ("edas && em"). Word settings such as typos ("word{2}"), the no-end-typo marker ("word$")
     * and wildcards ("*word", "word*") are stripped. Regular expression patterns are skipped.
     *
     * @param string $keyword
     * @return array
     */
    private static function getKeywordCandidates($keyword)
    {
        // Drop the "[params ...]" suffix used by "Text like" patterns.
        $paramsSentence = explode('[params ', $keyword);
        $pattern = trim($paramsSentence[0]);

        $candidates = [];

        foreach (explode('&&', $pattern) as $combination) {
            foreach (explode(',', $combination) as $candidate) {
                $candidate = trim($candidate);

                // Skip empty values and regular expression patterns (e.g. /foo/ or /foo/i).
                if ($candidate === '' || preg_match('/^\/(.*?)((\/[a-z]+)|(\/))$/', $candidate)) {
                    continue;
                }

                $candidate = self::getWordWithoutSettings($candidate);

                if ($candidate !== '') {
                    $candidates[] = $candidate;
                }
            }
        }

        return $candidates;
    }

    /**
     * @desc Removes "Text like" word settings leaving the plain word.
     *
     * @param string $word
     * @return string
     */
    private static function getWordWithoutSettings($word)
    {
        $word = preg_replace('/\{[0-9]\}/', '', trim($word)); // typos count e.g word{2}
        $word = preg_replace('/\$$/is', '', $word);           // no end typo e.g word$
        $word = preg_replace('/^\*/is', '', $word);           // wildcard start e.g *word
        $word = preg_replace('/\*$/is', '', $word);           // wildcard end e.g word*

        return trim($word);
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
