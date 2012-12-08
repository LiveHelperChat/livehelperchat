<?php
/**
 * File containing the ezcFeedTextInputElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a text input feed element.
 *
 * @property string $name
 *                  The name of the text input element.
 * @property string $link
 *                  The URL that the text input points at.
 * @property string $title
 *                  The title of the text input.
 * @property string $description
 *                  The description of the text input.
 * @property string $about
 *                  An identifier for the text input (usually the same value
 *                  as the link property). Used only by RSS1.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedTextInputElement extends ezcFeedElement
{
    /**
     * The name of the text input element.
     *
     * @var string
     */
    public $name;

    /**
     * The link that the text input points at.
     *
     * @var string
     */
    public $link;

    /**
     * The title of the text input.
     *
     * @var string
     */
    public $title;

    /**
     * The description for the text input.
     *
     * @var string
     */
    public $description;

    /**
     * The identifier for the text input.
     *
     * @var string
     */
    public $about;
}
?>
