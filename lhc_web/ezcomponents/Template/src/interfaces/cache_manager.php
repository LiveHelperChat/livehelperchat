<?php
/**
 * File containing the ezcTemplateCacheManager interface
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access public
 */

/**
 * Interface for the class that implement a cache manager.
 *
 * Implementing this interface gives the application using ezcTemplate the possibilities to: 
 *
 * - Mark cache files invalid based on certain criteria in your application.
 * - Remove old cache files.
 * - Keep track of the cache dependencies.
 *
 * The user application and ezcTemplate call methods of the implemented interface. In this way
 * the application communicates with the Template component. 
 *
 * The CacheManager implementation of should be made available to the Template component. 
 * Register the CacheManager implementation to the 'cacheManager' property in the TemplateConfiguration.
 *
 * The next example shows how the cache manager 'MyCacheManager' is registered:
 *
 * <code>
 * $config = ezcTemplateConfiguration::getInstance()
 * $config->cacheManager = new MyCacheManager();
 * </code>
 *
 * The methods in this interface that are called only by ezcTemplate:
 *
 * - startCaching:      A new or existing cache file is going to be (over)written.
 * - stopCaching:       The cache file is created.
 * - isValid:           The Template component request whether the given (parameter) cache file is valid. 
 * - includeTemplate:   A sub template is included.
 *
 * Remaining methods from the interface are only called by the user application:
 *
 * - register:  Register a value (eg. userID) that is related to the cache file currently created. 
 * - update:    A registered value is updated. This may indicate that a cache file is outdated. 
 *
 *
 * A typical cache creation process is as follows:
 *
 * 1. The user application calls $t->process("my_template.ezt").
 *
 * 2. The template "my_template.ezt" uses {cache_template}. A custom function {fetch} retrieves
 *    query results from the database and returns the results in one or an array of objects. The idea
 *    behind this is that when the cache is enabled, the expensive {fetch} query will be omitted.
 *
 * 3. If a cache file exists for the requested template, the template component calls 
 *    CacheManager::isValid() to see whether the cache file is still correct. If the 
 *    cache file is still correct, it will execute it and return. Otherwise it will (over)write
 *    the cache file.
 *
 * 4. When the cache file needs to be recreated, it calls CacheManager::startCaching() first.
 *    The CacheManager is now informed that the operations in the user application originate from
 *    the template that is creating the cache. For example, the operations performed on the database  
 *    are (indirectly) invoked, via a custom block or function, by the Template engine.
 *    
 * 5. The application code that returns the requested data should also inform the Cache Manager via the
 *    CacheManager::register() method. For example, the Custom Function {fetch} calls 
 *    CacheManager::register( "user", $userID ) for each requested user.
 *
 *    The CacheManager::register() method stores the values in the database and relates it to the cache file 
 *    currently in creation. When one of these values changes, the cache file would be outdated.
 *
 * 6. The CacheManager::includeTemplate() is called when another template is included. Relate the current
 *    cache with the included template and store it (for example in the database). The 
 *    CacheManager::isValid() can then also outdate the cache when a sub template is modified.
 *     
 * 7. The CacheManager::stopCaching() is called when the cache file is created.
 *
 * 8. The user application should call the CacheManager::update() method when a change in the database or 
 *    application may affect any of the caches.
 *
 *
 * Keeping track of the values used in the caches can probably best stored in a database. Using the 
 * file system only is also possible but makes things more complicated. The documentation of each method in 
 * this interface has also an example implementation. The examples rely on a MySQL database with at least
 * the following tables.
 *
 *
 * The table 'cache_files' has (at least) the following properties:
 * <code>
 * +---------------+------------------+------+-----+---------+----------------+
 * | Field         | Type             | Null | Key | Default | Extra          |
 * +---------------+------------------+------+-----+---------+----------------+
 * | id            | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
 * | cache         | varchar(255)     | NO   | MUL |         |                |
 * | expired       | tinyint(4)       | NO   |     | 0       |                |
 * +---------------+------------------+------+-----+---------+----------------+
 * </code>
 *
 * - id: Is the unique cache ID
 * - cache: The (relative) path to the cache file. 
 * - expired: Has the value 0 if the cache is valid, otherwise 1.
 *
 *
 * The table 'cache_values' has the following properties:
 *
 * <code>
 * +-------------+------------------+------+-----+---------+-------+
 * | Field       | Type             | Null | Key | Default | Extra |
 * +-------------+------------------+------+-----+---------+-------+
 * | cache_id    | int(10) unsigned | NO   | PRI |         |       |
 * | name        | varchar(50)      | NO   | PRI |         |       |
 * | value       | varchar(255)     | NO   | PRI |         |       |
 * +-------------+------------------+------+-----+---------+-------+
 * </code>
 *
 * - cache_id: The relation to 'cache_files.id'.
 * - name: Name of the value that is stored. 
 * - value: The value that belongs to the name. Name, value combination is up to user application.
 *
 *
 * Another table can also store the cache keys that belong to the cache. The structure
 * of this table is similar to the 'cache_values' table.
 *
 * @package Template
 * @version 1.4.2
 */
interface ezcTemplateCacheManager
{
    /**
     * The template engine calls this method when a new cache file will be created.
     *
     * The $template parameter contains the Template object. This allows you to reach
     * the current template configuration, if needed.
     * The $templatePath is the (relative) path to the template currently creating the cache file.
     * The $cachePath is the (relative) path to the cache file. 
     *
     * $cacheKeys contains all cache keys used in the template (if any).
     *
     * The startCaching method commonly performs the following steps, of course it's up to your application:
     *
     * 1  Register the cache file in the cache_file table of the database. If the cache file is available set
     *    the expired status to 0. Otherwise add the cache to the table, with a new id and expired set to 0.
     *
     * 2. Push the current cache information: cacheKeys, templatePath, Template object on an internal stack. Other
     *    CacheManager methods need this information.
     *
     * 3. Store the current template path in a database table that handles the included templates. The current
     *    template includes 'itself'. This makes it easier to mark the this cache outdated.
     *
     *
     * The following code demonstrates the steps from above and uses the ezcSignalSlot component to send the 
     * signals to the register method:
     *
     * <code>
     * // Get the current database handler.
     * $db = ezcDbInstance::get();
     *
     * // (1)
     * // Get the current cache ID, if it does exist. 
     * $q = $db->prepare("SELECT id FROM cache_files WHERE cache = :cache" );
     * $q->bindValue( ":cache", $cachePath );
     * $q->execute();
     *
     * // One result and make sure the query is terminated.
     * $r = $q->fetchAll();
     *
     * if ( sizeof( $r ) > 0 ) // Do we have any results?
     * {
     *     $id = $r[0]["id"];
     *     // Unexpire the cache_file.
     *     $s = $db->prepare( "UPDATE cache_files SET expired=0 WHERE id = :id" );
     *     $s->bindValue( ":id", $id );
     *     $s->execute();
     * }
     * else
     * {
     *     // Insert the new cache file
     *     $q = $db->prepare("INSERT INTO cache_files VALUES( '', :cache, '', 0)" );
     *     $q->bindValue( ":cache", $cachePath );
     *     $q->execute();
     *     $id = $db->lastInsertId();
     *
     *     // (3)
     *     // Insert your own template in the value table.
     *     $q = $db->prepare("REPLACE INTO cache_values VALUES(:id, :name, :value)" ); 
     *     $q->bindValue( ":id", $id );
     *     $q->bindValue( ":name", "include" );
     *     $q->bindValue( ":value", $templatePath );
     *     $q->execute();
     * }
     *
     * // (2)
     * // Depth keeps track of the amount of caches stored on the stack.
     * $this->depth++;
     * array_push( $this->keys, array( "cache_path" => $cachePath, "cache_id" => $id));
     * </code>
     *
     * The code above assumes that the private or protected member variables $depth and $keys
     * are available in the class.
     *
     *
     * @param ezcTemplate $template
     * @param string $templatePath
     * @param string $cachePath
     * @param array(string=>string) $cacheKeys
     * @return void
     */
    public function startCaching($template, $templatePath, $cachePath, $cacheKeys );

     /**
     * The stopCaching method is called by the Template Engine when the cache file is created.
     *
     * The current cache information: cacheKeys, templatePath, Template object can be popped from the 
     * internal stack. This is demonstrated in the following code:
     *
     * <code>
     * // Remove the current template cache in process.
     * $this->depth--;
     * array_pop( $this->keys );
     * </code>
     */
    public function stopCaching();

    /**
     * The isValid method is called by ezcTemplate to verify whether the cache is valid. 
     *
     * The steps that could be implemented:
     *
     * 1. Check if the current cache file is registered and if the cache file is expired. 
     *    If the cache is not registered or the cache file is marked invalid, return false.
     *
     * 2. An extra step could be to check whether the current cache file is newer than all
     *    included templates. The included templates are the templates included during
     *    the creation of this cache file. The check ensures that the templates modified
     *    by hand also renew the affected cache files. Usually, this step should only be
     *    performed during development, because of the overhead.
     *
     * The following code is an implementation of the steps above:
     * <code>
     * $db = ezcDbInstance::get();
     *
     * // (1)
     * // Check whether the cache is registered and if it's expired.
     * $q = $db->prepare("SELECT id, expired FROM cache_files WHERE cache = :cache" );
     * $q->bindValue( ":cache", $cacheName );
     * $q->execute();
     *
     * $r = $q->fetchAll(); // Expect 0 or 1 result
     *
     * if ( count($r) == 0 || $r[0]["expired"] == 1 )
     * {
     *     return false;
     * }
     * 
     * // (2)
     * // Go through all modification times.
     * $q = $db->prepare( "SELECT * FROM cache_values WHERE name = 'include' AND cache_id = :id");
     * $q->bindValue( ":id", $r[0]["id"] );
     * $q->execute();
     *
     * $r = $q->fetchAll();
     * foreach ( $r as $a )
     * {
     *     if ( filemtime( $a["value"] ) > filemtime( $cacheName ) )
     *     {
     *         return false;
     *     }
     * }
     *
     * return true;
     * </code>
     *
     * @param ezcTemplate $template
     * @param string $templateName
     * @param string $cacheName
     */
    public function isValid( $template, $templateName, $cacheName );


    /**
     * The user application should call this method to register values used in the current cache creation. 
     *
     * Typically, the function that does a database query and returns the result set should also call the register() method.
     * The implementation of the register method should update the 'cache_values' table for all the cache_files on the stack.
     * See the next example code:
     *
     * <code>
     * $db = ezcDbInstance::get();
     * for($i = 0; $i <= $this->depth; $i++)
     * { 
     *     $s = $db->prepare( "REPLACE INTO cache_values VALUES ( :id, :name, :value )" );
     *     $s->bindValue( ":id", $this->keys[$i]["cache_id"] );
     *     $s->bindValue( ":name", $name );
     *     $s->bindValue( ":value", $value );
     *     $s->execute();
     * }
     * </code>
     *
     * The member variables: $this->depth and $this->keys keep the amount of cache files and the cache file data that currently
     * created. Notice that the amount of cache files on the stack only increases with a 'template include' to another
     * cache file.
     *
     * In the cache_values table maps a name, value to an ID. The update() method uses this information.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function register( $name, $value );


    /**
     * The user application should call this method when the value changes that is previously registered with the register() method.
     *
     * Any name-value combination that is registered should be updated when the value changes. The cache file(s) using this name-value
     * are marked as expired.
     *
     * The next example implementation expires all the cache files that uses the name-value:
     *
     * <code>
     * $db = ezcDbInstance::get();
     * $qry = "UPDATE cache_files, cache_values SET cache_files.expired=1 ".
     *        "WHERE cache_files.id = cache_values.cache_id AND cache_values.name = :name AND cache_values.value = :value";
     * $s = $db->prepare( $qry ); 
     * $s->bindValue( ":name", $name );
     * $s->bindValue( ":value", $value );
     * $s->execute();
     * </code>
     *
     * @param string $name 
     * @param string $value 
     * @return void
     */
    public function update( $name, $value );


    /**
     * This method is called by the template engine when another template is included.
     *
     * The implementation of this method should register the current included template. The following code
     * registers all the included templates and relates them to the cache file in creation.
     *
     * <code>
     * if ( $this->depth >= 0 )
     * {
     *     $db = ezcDbInstance::get();
     *     $id = $this->keys[ $this->depth ]["cache_id"];
     *
     *     // Insert your parent template in the value table.
     *     $q = $db->prepare("REPLACE INTO cache_values VALUES(:id, :name, :value)" ); 
     *     $q->bindValue( ":id", $id );
     *     $q->bindValue( ":name", "include" );
     *     $q->bindValue( ":value", $templatePath );
     *     $q->execute();
     * }
     * </code>
     *
     * @param ezcTemplate $template
     * @param string $templatePath
     * @return void
     */
    public function includeTemplate( $template, $templatePath );


    /**
     * The cleanExpired method should remove the expired caches.
     *
     * The unused cache files on the hard-disk and the entries from the database tables: 
     * cache_values and cache_files can be removed. This method should be called once
     * in a while to garantee that the system is not flooded with expired cache data.
     *
     * An example implementation:
     * <code>
     * $db = ezcDbInstance::get();
     *
     * $q = $db->prepare("SELECT * FROM cache_templates WHERE expired = 1" );
     * $q->execute();
     * $rows = $q->fetchAll();
     *
     * foreach ($rows as $r)
     * {
     *     unlink( $r["cache"] );
     * }
     *
     * $db->exec("DELETE FROM cache_values USING cache_values, cache_templates WHERE cache_templates.id = cache_values.template_id AND cache_templates.expired = 1  ");
     * $db->exec("DELETE FROM cache_templates WHERE cache_templates.expired = 1");
     * </code>
     *
     * @return void
     */
    public function cleanExpired();

}

?>
