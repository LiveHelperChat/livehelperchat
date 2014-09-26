<?php
/**
 * File containing the ezcDebugHtmlFormatter class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDebugHtmlFormatter class implements a HTML debug formatter that outputs
 * debug information nicely formated for inclusion on your web page.
 *
 * @package Debug
 * @version 1.2.1
 */
class ezcDebugHtmlFormatter implements ezcDebugOutputFormatter
{
    /**
     * Stores a mapping between the a verbosity level and the color to print it with.
     *
     * Format array(verbosity_level=>'color_name_using_css_names')
     *
     * @var array(int=>string)
     */
    private $verbosityColors = array();

    /**
     * Constructs a new HTML reporter.
     */
    public function __construct()
    {
    }

    /**
     * Sets the output $color of debug messages of the verbosity $verbosity.
     *
     * $color must be specified in a CSS color value.
     *
     * @param int $verbosity
     * @param string $color
     *
     * @deprecated This method does not have any effect anymore. Use CSS instead.
     */
    public function setVerbosityColor( $verbosity, $color )
    {
        $this->verbosityColors[$verbosity] = $color;
    }

    /**
     * Returns a string containing the HTML formatted output.
     *
     * Returns the data submitted in $timerData and $writerData as an HTML
     * formatted string to be displayed in a web browser.
     *
     * @param array $writerData
     * @param array(ezcDebugStructure) $timerData
     * @return string
     */
    public function generateOutput( array $writerData, array $timerData )
    {
        $str = '<div class="ezc-debug-output">';
        $str .= $this->getLog( $writerData );
        $str .= $this->getTimingsAccumulator( $timerData );
        $str .= "</div>\n";
        return $str;
    }

    /**
     * Returns a string containing the HTML formatted output based on $writerData.
     *
     * @param array $writerData
     * @return string
     */
    public function getLog( array $writerData )
    {
        $str = "<table class='log'>\n";
        foreach ( $writerData as $w )
        {
            $color = isset( $this->verbosityColors[$w->verbosity]) ? $this->verbosityColors[$w->verbosity] : "";
            $date = date( 'Y-m-d H:i:s O', $w->datetime );
            $str .= <<<ENDT
<tr class='debugheader'>
    <td class='source'>
        <span class='verbosity{$w->verbosity}'>{$w->verbosity}: {$w->source}::{$w->category}</span>
    </td>
    <td class='date'>{$date}</td>
</tr>
<tr class='debugbody'>
    <td colspan='2'>{$w->message}</td>
</tr>
ENDT;
            if ( isset( $w->stackTrace ) )
            {
                $str .= "<tr class='debugstacktrace'>";
                $str .= "<td colspan='2'>";
                $str .= $this->formatStackTrace( $w->stackTrace );
                $str .= "</td>";
                $str .= "</tr>";
            }
        }
        $str .= "</table>\n";

        return $str;
    }

    /**
     * Returns an HTML formatted representation of the given $stackTrace.
     *
     * Iterates through the given $stackTrace and returns an HTML formatted
     * string representation.
     * 
     * @param ezcDebugStacktraceIterator $stackTrace 
     * @return string
     */
    public function formatStackTrace( ezcDebugStacktraceIterator $stackTrace )
    {
        $res = <<<EOT
<table class='stacktrace'>
<tr>
    <th class='stacktraceno'>
        #
    </th>
    <th class='stacktracefunction'>
        Function
    </th>
    <th class='stacktracelocation'>
        Location
    </th>
</tr>
EOT;
        foreach ( $stackTrace as $index => $element )
        {
            $function = ( isset( $element['class'] ) ? "{$element['class']}::" : '' )
                . $element['function'] . '('
                . implode( ', ', $element['params'] )
                . ')';
            $location = "{$element['file']}:{$element['line']}";
            $res .= <<<EOT
<tr>
    <td class='stacktraceno'>
        $index
    </td>
    <td class='stacktracefunction'>
        $function
    </td>
    <td class='stacktracelocation'>
        $location
    </td>
</tr>
EOT;
        }
        $res .= "</table>\n";
        return  $res;
    }

    /**
     * Returns a string containing the HTML formatted output based on $timerData.
     *
     * @param array(ezcDebugStructure) $timerData
     * @return string
     */
    public function getTimingsAccumulator( array $timerData )
    {
        $groups = $this->getGroups( $timerData );

        if ( sizeof( $groups ) > 0 )
        {
            $str = <<<ENDT
<table class='accumulator'>
    <tr>
        <th>Timings</th>
        <th>Elapsed</th>
        <th>Percent</th>
        <th>Count</th>
        <th>Average</th>
    </tr>

ENDT;

            foreach ( $groups as $groupName => $group )
            {
                $str .= <<<ENDT
    <tr class='group-header'>
        <th colspan='5'>{$groupName}</th>
    </tr>

ENDT;

                // Calculate the total time.
                foreach ( $group->elements as $name => $element )
                {
                    $elapsedTime = sprintf( '%.5f', $element->elapsedTime );
                    $percent = sprintf( '%.2f', (100 * ($element->elapsedTime / $group->elapsedTime ) ) );
                    $average = sprintf( '%.5f', ( $element->elapsedTime / $element->count ) );
                    $str .= <<<ENDT
    <tr class='group'>
        <td class='tp-name'>{$name}</td>
        <td class='tp-elapsed'>{$elapsedTime}</td>
        <td class='tp-percent'>{$percent} %</td>
        <td class='tp-count'>{$element->count}</td>
        <td class='tp-average'>{$average}</td>
    </tr>

ENDT;
                   foreach ( $element->switchTime as $switch )
                   {
                       $elapsedTime = sprintf( '%.5f', $switch->time - $element->startTime );
                       $percent = sprintf( '%.2f', ( 100 * ( $elapsedTime / $group->elapsedTime ) ) );

                       $str .= <<<ENDT
    <tr class='switch'>
        <td class='tp-name'>{$switch->name}</td>
        <td class='tp-elapsed'>{$elapsedTime}</td>
        <td class='tp-percent'>{$percent} %</td>
        <td class='tp-empty'>-</td>
        <td class='tp-empty'>-</td>
    </tr>

ENDT;
                   }
                }

                if ( $group->count > 1 )
                {
                    $elapsedTime = sprintf( '%.5f', $group->elapsedTime );
                    $average = sprintf( '%.5f', ( $group->elapsedTime / $group->count ) );
                    $str .= <<<ENDT
    <tr class='totals'>
        <th class='tp-total'>Total:</th>
        <td class='tp-elapsed'>{$elapsedTime}</td>
        <td class='tp-percent'>100.00 %</td>
        <td class='tp-count'>{$group->count}</td>
        <td class='tp-average'>{$average}</td>
    </tr>

ENDT;
                }
           }

            $str .= "</table>";
            return $str;
       }

       return "";
    }

    /**
     * Returns the timer groups of the given $timers.
     *
     * @param array(ezcDebugStructure) $timers
     * @return array(ezcDebugStructure)
     */
    private function getGroups( array $timers )
    {
        $groups = array();
        foreach ( $timers as $time )
        {
            if ( !isset( $groups[$time->group] ) )
            {
                $groups[$time->group] = new ezcDebugStructure();
                $groups[$time->group]->elements = array();
                $groups[$time->group]->count = 0;
                $groups[$time->group]->elapsedTime = 0;
                $groups[$time->group]->startTime = INF;  // Infinite high number.
                $groups[$time->group]->stopTime = 0;
            }

            // $groups[$time->group]->elements[] = $time;
            $this->addElement( $groups[$time->group]->elements, $time );

            $groups[$time->group]->count++;
            $groups[$time->group]->elapsedTime += $time->elapsedTime;
            $groups[$time->group]->startTime = min( $groups[$time->group]->startTime, $time->startTime );
            $groups[$time->group]->stopTime = max( $groups[$time->group]->stopTime, $time->stopTime );
        }

        return $groups;
    }

    /**
     * Prepares $element to contain $timeStruct information.
     *
     * @param array $element
     * @param ezcDebugTimerStruct $timeStruct
     */
    private function addElement( &$element, $timeStruct )
    {
        if ( !isset( $element[$timeStruct->name] ) )
        {
            $element[$timeStruct->name] = new ezcDebugStructure();

            $element[$timeStruct->name]->count = 0;
            $element[$timeStruct->name]->elapsedTime = 0;
            $element[$timeStruct->name]->startTime = INF;
            $element[$timeStruct->name]->stopTime = 0;
        }

        $element[$timeStruct->name]->count++;
        $element[$timeStruct->name]->elapsedTime += $timeStruct->elapsedTime;
        $element[$timeStruct->name]->startTime = min( $element[$timeStruct->name]->startTime, $timeStruct->startTime );
        $element[$timeStruct->name]->stopTime = max( $element[$timeStruct->name]->stopTime, $timeStruct->stopTime );


        $element[$timeStruct->name]->switchTime = $timeStruct->switchTime;
    }
}
?>
