<?php

namespace Pachico\Magoo;

use \InvalidArgumentException;
use Pachico\Magoo\Mask\MaskInterface;

/**
 * Interface Magoo and compatibles must implement
 */
interface MaskManagerInterface
{
    /**
     * Resets Magoo by clearing all previously added masks
     *
     * @return MaskManagerInterface
     */
    public function reset();

    /**
     * Adds a custom mask instance
     *
     * @param MaskInterface $customMask
     *
     * @return MaskManagerInterface
     */
    public function pushMask(MaskInterface $customMask);

    /**
     * Applies all masks to a given string
     *
     * @param string $input Input string to be masked
     *
     * @throws InvalidArgumentException
     *
     * @return string Masked string
     */
    public function getMasked($input);
}
