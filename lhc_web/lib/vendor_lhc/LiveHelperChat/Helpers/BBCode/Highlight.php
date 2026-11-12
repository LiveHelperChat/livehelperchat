<?php

namespace LiveHelperChat\Helpers\BBCode;

class Highlight
{
    /**
     * Normalizes keywords list. Accepts single keyword or an array of keywords.
     *
     * @param string|array $keywords Keywords to highlight
     * @return array
     */
    public static function keywords($keywords)
    {
        $keywordsList = array();

        foreach ((array)$keywords as $keyword) {
            if (!is_string($keyword)) {
                continue;
            }

            $keyword = trim($keyword);

            if ($keyword !== '') {
                $keywordsList[] = $keyword;
            }
        }

        return $keywordsList;
    }

    /**
     * Wraps keywords with HTML in already rendered message body.
     *
     * Highlighting is applied after the message is rendered, so it also works when BBCode
     * tags are disabled for visitor messages. Existing HTML tags are not touched.
     *
     * @param string $body Already rendered message body (HTML)
     * @param string|array $keywords Keywords to highlight
     * @param string $wrapClass Class applied to the highlighted keyword
     * @return string
     */
    public static function render($body, $keywords, $wrapClass = 'bg-warning text-dark rounded p-1 d-inline-block')
    {
        if ($body === '' || $body === null) {
            return $body;
        }

        $keywordsList = self::keywords($keywords);

        if (empty($keywordsList)) {
            return $body;
        }

        foreach ($keywordsList as $keyword) {
            $keywordPattern = preg_quote($keyword, '/');

            // Match whole words only when keyword starts/ends with a word character
            if (preg_match('/^\w/u', $keyword)) {
                $keywordPattern = '(?<!\w)' . $keywordPattern;
            }

            if (preg_match('/\w$/u', $keyword)) {
                $keywordPattern .= '(?!\w)';
            }

            $body = preg_replace_callback('#(<[^>]*>)|(' . $keywordPattern . ')#isu', function ($matches) use ($wrapClass) {
                if (isset($matches[1]) && $matches[1] !== '') {
                    return $matches[1];
                }

                return '<span class="' . htmlspecialchars($wrapClass) . '"><em><strong><span style="font-size:14pt;">' . $matches[2] . '</span></strong></em></span>';
            }, $body);
        }

        return $body;
    }
}
