<?php

class erLhcoreClassLhRedis
{
    private $redis;

    public function __construct()
    {
        try {
            $params = erConfigClassLhConfig::getInstance()->getSetting( 'redis', 'server');
            $this->redis = new Redis();
	        $this->redis->pconnect($params['host'], $params['port'], 2.5);
	        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

            //select database by index
            if (isset($params['database'])) {
                $this->redis->select($params['database']);
            }
            
        } catch (Exception $e){
            // Do nothing
        }
    }

    /**
     * ttl = 0, means month store (cache keys versions in most cases), in any other case I use user provided expire key
     * */
    public function set($key, $value, $compress, $ttl = 0)
    {
        if ($ttl == 0) {
            $this->redis->setex($key,2678400,$value); // One month
        } else {
            $this->redis->setex($key,$ttl,$value);
        }
    }
    
    public function get($var)
    {
        return $this->redis->get($var);
    }
    
    /**
     * Incr does not work then we need to fetch, perhaps just verions issues so i just replace with simple set.
     * */
    public function increment($var,$version)
    {
       $this->redis->set($var,$version);
    }
    
    public function __destruct()
    {
        $this->redis->close();
    }
}
