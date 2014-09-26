<?php
/**
 * File containing the ezcLogFilter class.
 *
 * @package EventLog
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcLogFilter class provides a structure to set a log filter.
 *
 * ezcLogFilter has three public member variables:
 * - severity, contains the severity of the log message.
 * - source, contain the source of the log message.
 * - category, contains the category of the log message.
 *
 * Severity is an integer mask that expects one more multiple ezcLog severity constants.
 * Multiple values can be assigned by using a logical-or on the values. The value zero
 * represents all possible severities.
 *
 * Source and category are an array. An empty array reprseents all possible sources
 * and categories.
 *
 * The ezclogFilter class is mainly used by the {@link ezcLog::attach()} and {@link ezcLog::detach()}
 * methods.
 *
 * @package EventLog
 * @version 1.4
 * @mainclass
 */
class ezcLogFilter extends ezcBaseStruct
{
   /**
    * The severities that are accepted by the ezcLogFilter.
    *
    * The default value zero specifies that all severities are accepted.
    *
    * @var int
    */
   public $severity;

   /**
    * The source of the log message.
    *
    * The default empty array specifies that all sources are accepted by this filter.
    *
    * @var array(string)
    */
   public $source;

   /**
    * The category of the log message.
    *
    * The default empty array specifies that all categories are accepted by this filter.
    *
    * @var array(string)
    */
   public $category;

   /**
    * Empty constructor
    *
    * @param int $severity
    * @param array(string) $source
    * @param array(string) $category
    */
   public function __construct( $severity = 0, array $source = array(), array $category = array() )
   {
       $this->severity = $severity;
       $this->source = $source;
       $this->category = $category;
   }
}
?>
