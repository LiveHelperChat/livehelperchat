<?php
/**
 * File containing the ezcFeedGeneratorElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a generator element.
 *
 * @property string $name
 *                  The name of the generator.
 * @property string $version
 *                  The version of the generator.
 * @property string $url
 *                  The URL of the generator.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedGeneratorElement extends ezcFeedElement
{
    /**
     * The name of the generator.
     *
     * @var string
     */
    public $name;

    /**
     * The version of the generator.
     *
     * @var string
     */
    public $version;

    /**
     * The URL of the generator.
     *
     * @var string
     */
    public $url;
}
?>
