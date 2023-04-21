<?php

namespace Pachico\Magoo;

/**
 * MagooArray acts as Magoo but for (multidimensional) trying to convert
 * to string everything it can and masking it.
 */
class MagooArray
{
    /**
     * @var MaskManagerInterface
     */
    protected $maskManager;

    /**
     * @param MaskManagerInterface $maskManager
     */
    public function __construct(MaskManagerInterface $maskManager)
    {
        $this->maskManager = $maskManager;
    }

    /**
     * @param mixed $input
     *
     * @return string|object
     */
    protected function maskIndividualValue($input)
    {
        switch (gettype($input)) {
            case 'array':
                $input = $this->maskMultiDimensionalStructure($input);
                break;
            case 'string':
            case 'float':
            case 'double':
            case 'int':
                $input = $this->maskManager->getMasked((string) $input);
                break;
            case 'object':
                if (method_exists($input, '__toString')) {
                    $input = $this->maskManager->getMasked((string) $input);
                }
                break;
        }

        return $input;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function maskMultiDimensionalStructure(array $input)
    {
        foreach ($input as &$value) {
            $value = $this->maskIndividualValue($value);
        }

        return $input;
    }

    /**
     * @return MaskManagerInterface
     */
    public function getMaskManager()
    {
        return $this->maskManager;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function getMasked(array $input)
    {
        $output = $this->maskMultiDimensionalStructure($input);

        return $output;
    }
}
