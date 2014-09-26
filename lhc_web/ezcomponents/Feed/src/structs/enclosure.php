<?php
/**
 * File containing the ezcFeedEnclosureElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining an enclosure element.
 *
 * @property string $url
 *                  The URL of the enclosure.
 * @property string $length
 *                  The length of the enclosure (usually in bytes).
 * @property string $type
 *                  The type of the enclosure (eg. 'audio/x-mp3').
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedEnclosureElement extends ezcFeedElement
{
    /**
     * The URL value.
     *
     * @var string
     */
    public $url;

    /**
     * The length in bytes of the resource pointed by href.
     *
     * @var int
     */
    public $length;

    /**
     * The type of the resource pointed by href.
     *
     * @var string
     */
    public $type;
}
?>
