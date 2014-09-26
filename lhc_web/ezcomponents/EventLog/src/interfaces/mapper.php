<?php
/**
 * File containing the ezcLogMapper interface.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcLogMapper provides a public interface to implement a mapper.
 *
 * The ezcLogMapper interface has one method that must be implemented.
 * This method returns a writer (or in some cases a string) that matches the
 * incoming message.
 *
 * An implementation of ezcLogMapper is the {@link ezcLogFilterSet}.
 *
 * @package EventLog
 * @version 1.4
 */
interface ezcLogMapper
{
    /**
     * Returns the containers (results) that are mapped to this $severity, $source, and $category.
     *
     * @param int $severity
     * @param string $source
     * @param string $category
     * @return mixed|ezcLogWriter
     */
    public function get( $severity, $source, $category );
}
?>
