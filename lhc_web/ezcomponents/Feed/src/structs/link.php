<?php
/**
 * File containing the ezcFeedLinkElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a link element.
 *
 * @property string $href
 *                  The URL value of the link element.
 * @property string $rel
 *                  The URL relation (eg. 'alternate', 'enclosure', etc).
 * @property string $type
 *                  The type of the resource pointed by href (eg. 'audio/x-mp3').
 * @property string $hreflang
 *                  The language of the resource pointed by href.
 * @property string $title
 *                  The title for the URL.
 * @property int $length
 *               The length in bytes for the resource pointed by href.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedLinkElement extends ezcFeedElement
{
    /**
     * The URL value.
     *
     * @var string
     */
    public $href;

    /**
     * The rel for the link.
     *
     * @var string
     */
    public $rel;

    /**
     * The type of the resource pointed by href.
     *
     * @var string
     */
    public $type;

    /**
     * The language for the resource pointed by href.
     *
     * @var string
     */
    public $hreflang;

    /**
     * The title for the link.
     *
     * @var string
     */
    public $title;

    /**
     * The length in bytes of the resource pointed by href.
     *
     * @var int
     */
    public $length;

    /**
     * Returns the href attribute.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->href . '';
    }
}
?>
