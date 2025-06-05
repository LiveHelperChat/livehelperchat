<?php

class erLhcoreClassLazyDatabaseConfiguration implements ezcBaseConfigurationInitializer
{
     private static $connectionMaster;

     public static function configureObject( $instance )
     {
         $cfg = erConfigClassLhConfig::getInstance();
         switch ( $instance )
         {
             case 'slave':
                 if ($cfg->getSetting( 'db', 'use_slaves' ) === true) {
                     try {
        		         $dbSlaves = $cfg->getSetting( 'db', 'db_slaves' );
        		         $slaveParams = $dbSlaves[rand(0,count($dbSlaves)-1)];
                         $db = ezcDbFactory::create( "mysql://{$slaveParams['user']}:{$slaveParams['password']}@{$slaveParams['host']}:{$slaveParams['port']}/{$slaveParams['database']}" );
                         $db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                         $tz = $cfg->getSetting( 'db', 'tz', false );
                         if ($tz != '') {
                             try {
                                 $db->query("SET time_zone = '" . $tz . "';");
                             } catch (Exception $e) {

                             }
                         }
                     } catch (Exception $e){
                         error_log($e);
                         throw new Exception($e->getMessage());
                     }
                     return $db;
                 } else {
                     // Perhaps connection is already done with master?
                     if (isset(self::$connectionMaster)) return self::$connectionMaster;
                     try {
                        $db = ezcDbFactory::create( "mysql://{$cfg->getSetting( 'db', 'user' )}:{$cfg->getSetting( 'db', 'password' )}@{$cfg->getSetting( 'db', 'host' )}:{$cfg->getSetting( 'db', 'port' )}/{$cfg->getSetting( 'db', 'database' )}" );
                        $db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                        $tz = $cfg->getSetting( 'db', 'tz', false );
                        if ($tz != '') {
                            try {
                                $db->query("SET time_zone = '" . $tz . "';");
                            } catch (Exception $e) {
                             }
                        }
                        self::$connectionMaster = $db;
                        return $db;
                    } catch (Exception $e) {
                      error_log($e);
                      throw new Exception($e->getMessage());
                    }
                 }
                 break;

             case 'dbmongo':
                 try {
                    $cfg = erConfigClassLhConfig::getInstance();
                    $db = ezcDbFactory::create( "mongodb://{$cfg->getSetting( 'dbmongo', 'user' )}:{$cfg->getSetting( 'dbmongo', 'password' )}@{$cfg->getSetting( 'dbmongo', 'host' )}:{$cfg->getSetting( 'dbmongo', 'port' )}/{$cfg->getSetting( 'dbmongo', 'database' )}" );
                    return $db;
                 } catch (Exception $e) {
                     error_log($e);
                     throw new Exception($e->getMessage());
                 }
                 break;

             case false: // Default instance
             {
                try {
                    if (isset(self::$connectionMaster)) return self::$connectionMaster; // If we do not user slaves and slave request already got connection
                    $db = ezcDbFactory::create( "mysql://{$cfg->getSetting( 'db', 'user' )}:{$cfg->getSetting( 'db', 'password' )}@{$cfg->getSetting( 'db', 'host' )}:{$cfg->getSetting( 'db', 'port' )}/{$cfg->getSetting( 'db', 'database' )}" );
                    $db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                    // $db->query("SET sql_mode='ONLY_FULL_GROUP_BY'"); For future testing purposes
                    $tz = $cfg->getSetting( 'db', 'tz', false );
                    if ($tz != '') {
                        try {
                            $db->query("SET time_zone = '" . $tz . "';");
                        } catch (Exception $e) {
                        }
                    }
                    self::$connectionMaster = $db;
                    return $db;
                } catch (Exception $e) {
                	// Are we installed?
                	if ($cfg->getSetting( 'site', 'installed' ) == false) {
                		header('Location: ' .erLhcoreClassDesign::baseurldirect('site_admin/install/install') );
                		exit;
                  	}
                    error_log($e);
                    throw new Exception($e->getMessage());
                }
             }

             case 'sqlite':
             return ezcDbFactory::create( 'sqlite://:memory:' );
         }
     }
 }




?>