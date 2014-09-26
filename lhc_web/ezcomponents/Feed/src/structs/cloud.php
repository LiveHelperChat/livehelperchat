<?php
/**
 * File containing the ezcFeedCloudElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a cloud element.
 *
 * @property string $domain
 *                  The domain of the cloud element.
 * @property string $port
 *                  The port of the cloud element.
 * @property string $path
 *                  The path of the cloud element.
 * @property string $registerProcedure
 *                  The registerProcedure of the cloud element.
 * @property string $protocol
 *                  The protocol of the cloud element.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedCloudElement extends ezcFeedElement
{
    /**
     * The domain of the cloud.
     *
     * @var string
     */
    public $domain;

    /**
     * The port of the cloud.
     *
     * @var string
     */
    public $port;

    /**
     * The path in the cloud.
     *
     * @var string
     */
    public $path;

    /**
     * The procedure in the cloud.
     *
     * @var string
     */
    public $registerProcedure;

    /**
     * The protocol for the cloud.
     *
     * @var string
     */
    public $protocol;
}
?>
