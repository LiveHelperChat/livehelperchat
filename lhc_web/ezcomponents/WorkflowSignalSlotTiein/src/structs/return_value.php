<?php
/**
 * File containing the ezcWorkflowSignalSlotReturnValue struct.
 *
 * @package WorkflowSignalSlotTiein
 * @version 1.0
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct used to pass return values to/from slots.
 *
 * @package WorkflowSignalSlotTiein
 * @version 1.0
 */
class ezcWorkflowSignalSlotReturnValue
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @param mixed $value
     * @ignore
     */
    public function __construct( $value = true )
    {
        $this->value = $value;
    }
}
?>
