<?php

namespace Pachico\Magoo\Mask;

use Pachico\Magoo\Validator\ValidatorInterface;
use Pachico\Magoo\Validator\Luhn;

/**
 * The concept behind this is:
 * If we can find a valid Luhn long enough to be a CCard, match it via regex
 * If match isn't a valid Luhn, no need to mask it
 */
class Creditcard implements MaskInterface
{
    /**
     * @var string
     */
    protected $replacement = '*';

    /**
     * @var ValidatorInterface
     */
    protected $luhnValidator;

    /**
     * @param array $params
     * @param ValidatorInterface $luhnValidator
     */
    public function __construct(array $params = [], ValidatorInterface $luhnValidator = null)
    {
        if (isset($params['replacement']) && is_string($params['replacement'])) {
            $this->replacement = $params['replacement'];
        }

        $this->luhnValidator = $luhnValidator ?: new Luhn();
    }

    /**
     * This will only mask a CC number if it's a valid Luhn, since,
     * otherwise, it's not a correct CC number.
     *
     * {@inheritDoc}
     */
    public function mask($string)
    {
        $regex = '/(?:\d[ \t-]*?){13,19}/m';
        $matches = [];
        preg_match_all($regex, $string, $matches);

        // No credit card found
        if (!isset($matches[0]) || empty($matches[0])) {
            return $string;
        }

        foreach ($matches as $matchGroup) {
            foreach ($matchGroup as $match) {
                $strippedMatch = preg_replace('/[^\d]/', '', $match);

                if (false === $this->luhnValidator->isValid($strippedMatch)) {
                    continue;
                }

                $cardLength = strlen($strippedMatch);
                $replacement = str_pad('', $cardLength - 4, $this->replacement) . substr($strippedMatch, -4);
                $string = str_replace($match, $replacement, $string);
            }
        }

        return $string;
    }
}
