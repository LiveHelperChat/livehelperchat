<?php
/**
 * File containing the ezcFeedImageElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining an image element.
 *
 * @property string $link
 *                  The URL where the image is stored.
 * @property string $title
 *                  The title of the image.
 * @property string $url
 *                  The URL the image points at.
 * @property string $description
 *                  A description for the image.
 * @property int $width
 *               The width of the image in pixels.
 * @property int $height
 *               The height of the image in pixels.
 * @property string $about
 *                  An identifier for the image (usually the same value as $link).
 *                  Used only by RSS1.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedImageElement extends ezcFeedElement
{
    /**
     * The URL to the image.
     *
     * @var string
     */
    public $link;

    /**
     * The title for the image.
     *
     * @var string
     */
    public $title;

    /**
     * The URL the image points at.
     *
     * @var string
     */
    public $url;

    /**
     * A description for the image.
     *
     * @var string
     */
    public $description;

    /**
     * The width of the image in pixels.
     *
     * @var int
     */
    public $width;

    /**
     * The height of the image in pixels.
     *
     * @var int
     */
    public $height;

    /**
     * The identifier of the image.
     *
     * @var string
     */
    public $about;

    /**
     * Returns the link attribute.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->link . '';
    }
}
?>
