<?php
/**
 * File containing the ezcWorkflowVisitable interface.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for visitable workflow elements that can be visited
 * by ezcWorkflowVisitor implementations for processing using the
 * Visitor design pattern.
 *
 * All elements that will be part of the workflow tree must
 * implement this interface.
 *
 * {@link http://en.wikipedia.org/wiki/Visitor_pattern Information on the Visitor pattern.}
 *
 * @package Workflow
 * @version 1.4.1
 */
interface ezcWorkflowVisitable
{
    /**
     * Accepts the visitor.
     *
     * @param ezcWorkflowVisitor $visitor
     */
    public function accept( ezcWorkflowVisitor $visitor );
}
?>
