<?php
/**
 * File containing the ezcFeedPersonElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a person.
 *
 * @property string $name
 *                  The name of the person.
 * @property string $email
 *                  The email address of the person.
 * @property string $uri
 *                  The URI of the person.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedPersonElement extends ezcFeedElement
{
    /**
     * The name of the person.
     *
     * @var string
     */
    public $name = null;

    /**
     * The URI of the person.
     *
     * @var string
     */
    public $uri;

    /**
     * The email address of the person.
     *
     * @var string
     */
    public $email;

    /**
     * Returns the name attribute.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name . '';
    }
}
?>
