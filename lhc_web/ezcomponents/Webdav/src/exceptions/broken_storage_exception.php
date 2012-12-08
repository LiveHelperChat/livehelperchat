<?php
/**
 * File containing the ezcWebdavFileBackendBrokenStorageException class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a stored property storage could not be parsed.
 *
 * In rare cases it may happen, that the properties stored by {@link
 * ezcWebdavFileBackend} get broken (HD failure, bug,...). If the file backend
 * cannot parse the storage anymore, this exception is thrown.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavFileBackendBrokenStorageException extends ezcWebdavException
{
}

?>
