<?php
/**
 * File containing the ezcDocumentPdfTransactionalDriverWrapperState class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Driver wrapper state.
 *
 * Struct representing a state in the transactional driver wrapper. For a more 
 * detailed explanation of the concept behind the transactional driver wrapper, 
 * see the class level doc block in ezcDocumentPdfMainRenderer class.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfTransactionalDriverWrapperState extends ezcBaseStruct
{
    /**
     * Recorded calls in the current transaction
     * 
     * @var array
     */
    public $calls = array();

    /**
     * Page creations, performed in the current transaction
     * 
     * @var array
     */
    public $pageCreations = array();

    /**
     * Current page, in this transaction
     * 
     * @var int
     */
    public $currentPage = 0;
}
?>
