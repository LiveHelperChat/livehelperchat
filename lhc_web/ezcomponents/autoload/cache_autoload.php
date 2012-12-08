<?php
/**
 * Autoloader definition for the Cache component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.5
 * @filesource
 * @package Cache
 */

return array(
    'ezcCacheException'                      => 'Cache/exceptions/exception.php',
    'ezcCacheApcException'                   => 'Cache/exceptions/apc_exception.php',
    'ezcCacheInvalidDataException'           => 'Cache/exceptions/invalid_data.php',
    'ezcCacheInvalidIdException'             => 'Cache/exceptions/invalid_id.php',
    'ezcCacheInvalidKeyException'            => 'Cache/exceptions/invalid_key.php',
    'ezcCacheInvalidMetaDataException'       => 'Cache/exceptions/invalid_meta_data.php',
    'ezcCacheInvalidStorageClassException'   => 'Cache/exceptions/invalid_storage_class.php',
    'ezcCacheMemcacheException'              => 'Cache/exceptions/memcache_exception.php',
    'ezcCacheStackIdAlreadyUsedException'    => 'Cache/exceptions/stack_id_already_used.php',
    'ezcCacheStackStorageUsedTwiceException' => 'Cache/exceptions/stack_storage_used_twice.php',
    'ezcCacheStackUnderflowException'        => 'Cache/exceptions/stack_underflow.php',
    'ezcCacheUsedLocationException'          => 'Cache/exceptions/used_location.php',
    'ezcCacheStackMetaDataStorage'           => 'Cache/interfaces/meta_data_storage.php',
    'ezcCacheStackableStorage'               => 'Cache/interfaces/stackable_storage.php',
    'ezcCacheStorage'                        => 'Cache/storage.php',
    'ezcCacheStackMetaData'                  => 'Cache/interfaces/meta_data.php',
    'ezcCacheStackReplacementStrategy'       => 'Cache/interfaces/replacement_strategy.php',
    'ezcCacheStorageMemory'                  => 'Cache/storage/memory.php',
    'ezcCacheMemoryBackend'                  => 'Cache/backends/memory_backend.php',
    'ezcCacheStackBaseMetaData'              => 'Cache/interfaces/base_meta_data.php',
    'ezcCacheStackBaseReplacementStrategy'   => 'Cache/interfaces/base_replacement_strategy.php',
    'ezcCacheStorageApc'                     => 'Cache/storage/apc.php',
    'ezcCacheStorageApcOptions'              => 'Cache/options/storage_apc.php',
    'ezcCacheStorageFile'                    => 'Cache/storage/file.php',
    'ezcCacheStorageMemcache'                => 'Cache/storage/memcache.php',
    'ezcCacheApcBackend'                     => 'Cache/backends/apc/apc_backend.php',
    'ezcCacheManager'                        => 'Cache/manager.php',
    'ezcCacheMemcacheBackend'                => 'Cache/backends/memcache/memcache_backend.php',
    'ezcCacheMemoryVarStruct'                => 'Cache/structs/memory_var.php',
    'ezcCacheStack'                          => 'Cache/stack.php',
    'ezcCacheStackConfigurator'              => 'Cache/interfaces/stack_configurator.php',
    'ezcCacheStackLfuMetaData'               => 'Cache/stack/lfu_meta_data.php',
    'ezcCacheStackLfuReplacementStrategy'    => 'Cache/replacement_strategies/lfu.php',
    'ezcCacheStackLruMetaData'               => 'Cache/stack/lru_meta_data.php',
    'ezcCacheStackLruReplacementStrategy'    => 'Cache/replacement_strategies/lru.php',
    'ezcCacheStackOptions'                   => 'Cache/options/stack.php',
    'ezcCacheStackStorageConfiguration'      => 'Cache/stack/storage_configuration.php',
    'ezcCacheStorageApcPlain'                => 'Cache/storage/apc/plain.php',
    'ezcCacheStorageFileApcArray'            => 'Cache/storage/apc/apc_array.php',
    'ezcCacheStorageFileApcArrayDataStruct'  => 'Cache/structs/file_apc_array_data.php',
    'ezcCacheStorageFileApcArrayOptions'     => 'Cache/options/storage_apc_array.php',
    'ezcCacheStorageFileArray'               => 'Cache/storage/file/array.php',
    'ezcCacheStorageFileEvalArray'           => 'Cache/storage/file/eval_array.php',
    'ezcCacheStorageFileObject'              => 'Cache/storage/file/object.php',
    'ezcCacheStorageFileOptions'             => 'Cache/options/storage_file.php',
    'ezcCacheStorageFilePlain'               => 'Cache/storage/file/plain.php',
    'ezcCacheStorageMemcacheOptions'         => 'Cache/options/storage_memcache.php',
    'ezcCacheStorageMemcachePlain'           => 'Cache/storage/memcache/plain.php',
    'ezcCacheStorageMemoryDataStruct'        => 'Cache/structs/memory_data.php',
    'ezcCacheStorageMemoryRegisterStruct'    => 'Cache/structs/memory_register.php',
    'ezcCacheStorageOptions'                 => 'Cache/options/storage.php',
);
?>
