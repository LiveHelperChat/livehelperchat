<?php
/**
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.2
 * @filesource
 * @package TranslationCacheTiein
 */

/**
 * Translation backend that reads translation data from a cache.
 *
 * This class is a backend implementation for the Translation system. This
 * specific one uses the Cache Component to store and serve cached translation
 * data.
 *
 * Example that uses both the {@link ezcCacheStorageFileArray} and {@link
 * ezcTranslationCacheBackend} classes:
 * <code>
 * <?php
 * // Create a cache object with content
 * $cacheObj = new ezcCacheStorageFileArray( 'ezcTranslationCacheBackendTest' );
 * 
 * $expected = array(
 *     new ezcTranslationData( 'Node ID: %node_id Visibility: %visibility',
 *                             'Knoop ID: %node_id Zichtbaar: %visibility',
 *                             false, ezcTranslationData::TRANSLATED )
 * );
 * $cacheObj->store( 'nl-nl/contentstructuremenu/show_content_structure', $expected );
 *
 * // Use the cache backend
 * $backend = new ezcTranslationCacheBackend( $cacheObj );
 * $context = $backend->getContext( 'nl-nl', 'contentstructuremenu/show_content_structure' );
 * $translation = new ezcTranslation( $context );
 * echo $translation->getTranslation( 'Node ID: %node_id Visibility: %visibility',
 *                                    array( 'node_id' => 42, 'visibility' => 'yes' ) );
 * ?>
 * </code>
 *
 * Example that stores a whole translation file into a cache by using {@link
 * ezcTranslationContextRead} interface that is implemented by the {@link
 * ezcTranslationTsBackend} and the {@link ezcTranslationContextWrite}
 * interface that is implemented by this class:
 * <code>
 * <?php
 * // Settings
 * $locale = 'nb-no';
 *
 * // Setup the cache object
 * $cacheObj = new ezcCacheStorageFileArray( 'ezcTranslationCacheBackendTest' );
 *
 * // Initialize the writer
 * $writer = new ezcTranslationCacheBackend( $cacheObj );
 * $writer->initWriter( $locale );
 *
 * // Initialize the reader
 * $reader = new ezcTranslationTsBackend( "translations" );
 * $reader->setOptions( array ( 'format' => '[LOCALE].xml' ) );
 * $reader->initReader( $locale );
 * 
 * // Process the data
 * $contexts = array();
 * foreach ( $reader as $contextName => $contextData )
 * {
 *     $writer->storeContext( $contextName, $contextData );
 * }
 *
 * // Deinitialize the writer and reader
 * $writer->deinitWriter();
 * $reader->deinitReader();
 * ?>
 * </code>
 *
 * @package TranslationCacheTiein
 * @version 1.1.2
 * @mainclass
 */
class ezcTranslationCacheBackend implements ezcTranslationBackend, ezcTranslationContextWrite
{
    /**
     * Stores the cache object to use for storing and fetching.
     *
     * @var ezcCacheStorageFileArray
     */
    private $cache;

    /**
     * The locale to write to.
     *
     * @var string
     */
    private $writeLocale;

    /**
     * Constructs a new ezcTranslationCacheBackend that will store data to $cacheObject.
     *
     * @param ezcCacheStorageFileArray $cacheObject
     */
    public function __construct( ezcCacheStorageFileArray $cacheObject )
    {
        $this->cache = $cacheObject;
    }

    /**
     * Sets configuration data
     *
     * This backend accepts no settings at all, and will always throw an
     * ezcBaseSettingNotFoundException for every setting that is contained
     * in the $configurationData.
     *
     * @param array $configurationData
     * @throws ezcBaseSettingNotFoundException if an unknown setting is passed.
     * @return void
     * @todo Implement ezcBaseOptions class, if options are added.
     */
    public function setOptions( $configurationData )
    {
        foreach ( $configurationData as $name => $value )
        {
            throw new ezcBaseSettingNotFoundException( $name );
        }
    }

    /**
     * Returns a array containing the translation map for the specified
     * $locale and $context.
     *
     * It uses the $tsLocationPath and
     * $tsFilenameFormat properties to locate the file, unless caching is
     * enabled. If a cache object is available it will be used to retrieve the
     * information from the cache.
     *
     * @throws ezcTranslationContextNotAvailableException if the context is not available.
     * @param string $locale
     * @param string $context
     * @return array(ezcTranslationData)
     */
    public function getContext( $locale, $context )
    {
        $cachedContext = $this->cache->restore( "$locale/$context" );
        if ( $cachedContext === false )
        {
            throw new ezcTranslationContextNotAvailableException( $context );
        }
        foreach ( $cachedContext as $key => $cachedElement )
        {
            if ( $cachedElement->status == ezcTranslationData::OBSOLETE )
            {
                unset( $cachedContext[$key] );
            }
        }
        return $cachedContext;
    }

    /**
     * Initializes the writer to write to the locale $locale.
     *
     * Before starting to writer contexts to the writer, you should call
     * this method to initialize it.
     *
     * @param string $locale
     * $return void
     */
    public function initWriter( $locale )
    {
        $this->writeLocale = $locale;
    }

    /**
     * Deinitializes the writer.
     *
     * This method should be called after the last context was written to
     * cleanup resources.
     *
     * @throws ezcTranslationWriterNotInitializedException when the writer is
     *         not initialized with initWriter().
     * @return void
     */
    public function deinitWriter()
    {
        if ( is_null( $this->writeLocale ) )
        {
            throw new ezcTranslationWriterNotInitializedException();
        }
        $this->writeLocale = null;
    }

    /**
     * Stores a context.
     *
     * This method stores the context that it received to the backend specified
     * storage place.
     *
     * @throws ezcTranslationWriterNotInitializedException when the writer is
     *         not initialized with initWriter().
     * @param string $context The context's name
     * @param array  $data The context's translation map
     * @return void
     */
    public function storeContext( $context, array $data )
    {
        if ( is_null( $this->writeLocale ) )
        {
            throw new ezcTranslationWriterNotInitializedException();
        }
        foreach ( $data as $key => $cachedElement )
        {
            if ( $cachedElement->status == ezcTranslationData::OBSOLETE )
            {
                unset( $data[$key] );
            }
        }
        $cachedContext = $this->cache->store( "{$this->writeLocale}/$context" , $data );
    }
}
?>
