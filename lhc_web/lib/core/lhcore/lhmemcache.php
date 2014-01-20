<?php

class erLhcoreClassLhMemcache extends Memcache
{
    private $memcache;
    
    public function __construct()
    {     
         $this->memcache = new Memcache();          
         $hosts = erConfigClassLhConfig::getInstance()->getSetting( 'memecache', 'servers' );
         foreach ($hosts as $server) {
                $this->memcache->addServer($server['host'],$server['port'],$server['weight']);
         }
    }  
    
    public function set($key, $value, $compress, $ttl = 0)
    {      
         $this->memcache->set($key,$value,$compress,$ttl);       
    }
    
    public function __destruct()
    {
        $this->memcache->close();
    }   
    
    public function get($var)
    {
        return $this->memcache->get($var);
    }
    
    public function increment($var,$version)
    {
        return $this->memcache->increment($var);
    }
    
}


?>