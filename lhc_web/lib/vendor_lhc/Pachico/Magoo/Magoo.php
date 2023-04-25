<?php

namespace Pachico\Magoo;

use \InvalidArgumentException;

/**
 * Magoo will mask sensitive data from strings.
 */
class Magoo implements MaskManagerInterface
{
    /**
     * Contains masks that will be executed
     *
     * @var array
     */
    protected $masks = [];

    /**
     * Adds (or rewrites) Mask\CreditCard mask
     *
     * @param string $replacement Character to replace matches
     *
     * @return Magoo
     */
    public function pushCreditCardMask($replacement = '*')
    {
        $this->masks['mask-creditcard'] = new Mask\Creditcard(
            [
                'replacement' => (string) $replacement
            ]
        );

        return $this;
    }

    /**
     * Adds Mask\Regex mask
     *
     * @param string $regex Regex to be executed
     * @param string $replacement Character to replace matches
     *
     * @return Magoo
     */
    public function pushByRegexMask($regex, $replacement = '*')
    {
        $uniqueId = uniqid('mask-regex-');

        $this->masks[$uniqueId] = new Mask\Regex(
            [
                'regex' => empty($regex) ? '/^$/' : (string) $regex,
                'replacement' => (string) $replacement
            ]
        );

        return $this;
    }

    /**
     * Adds (or rewrites) Mask\Email mask
     *
     * @param string $localReplacement Character to replace local part of email
     * @param string $domainReplacement Character to replace domain part of email
     *
     * @return Magoo
     */
    public function pushEmailMask($localReplacement = '*', $domainReplacement = null)
    {
        $params = [
            'localReplacement' => null,
            'domainReplacement' => null,
        ];

        $this->masks['mask-email'] = new Mask\Email(
            array_merge($params, [
                'localReplacement' => $localReplacement,
                'domainReplacement' => $domainReplacement,
            ])
        );

        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function pushMask(Mask\MaskInterface $customMask)
    {
        $uniqueId = uniqid('mask-custom-');
        $this->masks[$uniqueId] = $customMask;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function reset()
    {
        $this->masks = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMasked($input)
    {
        if (!is_string($input)) {
            throw new InvalidArgumentException(
                'Message to be masked needs to string - ' . gettype($input) . ' passed.'
            );
        }

        if (empty($this->masks)) {
            return $input;
        }

        foreach ($this->masks as $mask) {
            $input = $mask->mask($input);
        }

        return $input;
    }
}
