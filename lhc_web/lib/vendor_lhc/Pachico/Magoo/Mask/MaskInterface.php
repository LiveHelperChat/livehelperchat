<?php

namespace Pachico\Magoo\Mask;

/**
 * Masks must implement this interface since
 * mask() method will be executed for all of them
 */
interface MaskInterface
{
    /**
     * @param array $params
     */
    public function __construct(array $params = []);

    /**
     * Masks a given string
     *
     * @param string $string
     */
    public function mask($string);
}
