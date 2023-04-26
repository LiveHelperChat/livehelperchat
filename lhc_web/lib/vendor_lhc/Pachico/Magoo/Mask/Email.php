<?php

namespace Pachico\Magoo\Mask;

/**
 * Email regex detection is still controversial.
 * Please report any improvements
 */
class Email implements MaskInterface
{

    /**
     * @var string
     */
    protected $replacementLocal = null;

    /**
     * @var string
     */
    protected $replacementDomain = null;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $params = [])
    {
        if (isset($params['localReplacement']) && is_string($params['localReplacement'])) {
            $this->replacementLocal = $params['localReplacement'];
        }

        if (isset($params['domainReplacement']) && is_string($params['domainReplacement'])) {
            $this->replacementDomain = $params['domainReplacement'];
        }

        if (is_null($this->replacementLocal) && is_null($this->replacementDomain)) {
            $this->replacementLocal = '*';
        }
    }

    /**
     * @param string $match
     *
     * @return string
     */
    protected function maskIndividualEmailMatch($match)
    {
        $matchReplacement = $match;

        if ($this->replacementLocal) {
            $localPart = substr($match, 0, stripos($match, '@'));
            $matchReplacement = str_replace(
                $localPart,
                str_pad('', strlen($localPart), $this->replacementLocal),
                $matchReplacement
            );
        }

        if ($this->replacementDomain) {
            $domainPart = substr($match, stripos($match, '@') + 1);
            $matchReplacement = str_replace(
                $domainPart,
                str_pad('', strlen($domainPart), $this->replacementDomain),
                $matchReplacement
            );
        }

        return $matchReplacement;
    }

    /**
     * {@inheritDoc}
     */
    public function mask($string)
    {
        $regex = "/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*"
            . "|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")"
            . "@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]"
            . "*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}"
            . "(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:"
            . "(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";

        $matches = [];
        preg_match_all($regex, $string, $matches);

        if (!isset($matches[0]) || empty($matches[0])) {
            return $string;
        }
        foreach ($matches as $matchGroup) {
            foreach ($matchGroup as $match) {
                $string = str_replace($match, $this->maskIndividualEmailMatch($match), $string);
            }
        }

        return $string;
    }
}
