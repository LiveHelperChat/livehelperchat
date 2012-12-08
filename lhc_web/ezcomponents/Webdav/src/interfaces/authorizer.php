<?php
/**
 * File containing the ezcWebdavAuthorizer interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for classes providing authorization.
 *
 * This interface must be implemented by classes the provide authorization to
 * an {@link ezcWebdavBackend}. A back end will call the {@link authorize()}
 * method for each path that is affected by request.
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavAuthorizer
{
    /**
     * User desires read access. 
     */
    const ACCESS_READ = 1;

    /**
     * User desires write access. 
     */
    const ACCESS_WRITE = 2;

    /**
     * Checks authorization of the given $user to a given $path.
     *
     * This method checks if the given $user has the permission $access to the
     * resource identified by $path. The $path is the result of a translation
     * by the servers {@link ezcWebdavPathFactory} from the request URI.
     *
     * The $access parameter can be one of
     * <ul>
     *    <li>{@link ezcWebdavAuthorizer::ACCESS_WRITE}</li>
     *    <li>{@link ezcWebdavAuthorizer::ACCESS_READ}</li>
     * </ul>
     *
     * The implementation of this method must only check the given $path, but
     * MUST not check descendant paths, since the back end will issue dedicated
     * calls for such paths. In contrast, the algoritm MUST ensure, that parent
     * permission constraints of the given $paths are met.
     *
     * Examples:
     * Permission is rejected for the paths "/a", "/b/beamme" and "/c/connect":
     *
     * <code>
     * <?php
     * var_dump( $auth->authorize( 'johndoe', '/a' ) ); // false
     * var_dump( $auth->authorize( 'johndoe', '/b' ) ); // true
     * var_dump( $auth->authorize( 'johndoe', '/b/beamme' ) ); // false
     * var_dump( $auth->authorize( 'johndoe', '/c/connect/some/deeper/path' ) ); // false
     * ?>
     * </code>
     * 
     * @param string $user 
     * @param string $path 
     * @param int $access 
     * @return bool
     */
    public function authorize( $user, $path, $access = self::ACCESS_READ );
}

?>
