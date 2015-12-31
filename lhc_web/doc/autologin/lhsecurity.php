<?php

/**
 * Reference: http://wpy.me/blog/15-encrypt-and-decrypt-data-in-php-using-aes-256
 * */
/*
class lhSecurity {

    # Private key
    public static $salt = 'ZfTfbip&_F-f8_df)Ha0gahptzLN7ROi%gy';

    public static $secretHash = '';
    
    public static function setSecretHash($secretHash) {
        self::$secretHash = $secretHash;
    }
    
    # Encrypt a value using AES-256.
    public static function encrypt($plain, $key = null, $hmacSalt = null) {

        if (empty($key)) { 
            $key = sha1(self::$secretHash . self::$salt);
        }

        self::_checkKey($key, 'encrypt()');
  
        if ($hmacSalt === null) {
            $hmacSalt = sha1(self::$salt . self::$secretHash . self::$salt);            
        }
  
        $key = substr(hash('sha256', $key . $hmacSalt), 0, 32); # Generate the encryption and hmac key
  
        $algorithm = MCRYPT_RIJNDAEL_128; # encryption algorithm
        $mode = MCRYPT_MODE_CBC; # encryption mode
  
        $ivSize = mcrypt_get_iv_size($algorithm, $mode); # Returns the size of the IV belonging to a specific cipher/mode combination
        $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM); # Creates an initialization vector (IV) from a random source
        $ciphertext = $iv . mcrypt_encrypt($algorithm, $key, $plain, $mode, $iv); # Encrypts plaintext with given parameters
        $hmac = hash_hmac('sha256', $ciphertext, $key); # Generate a keyed hash value using the HMAC method
        return $hmac . $ciphertext;
    }

    # Check key
    protected static function _checkKey($key, $method) {
        if (strlen($key) < 32) {
            echo "Invalid key $key, key must be at least 256 bits (32 bytes) long."; die();
        }
    }
  
    # Decrypt a value using AES-256.
    public static function decrypt($cipher, $key = null, $hmacSalt = null) {
        if(empty($key)) {           
            $key = sha1(self::$secretHash . self::$salt);
        }
         
        self::_checkKey($key, 'decrypt()');
         
        if (empty($cipher)) {
            echo 'The data to decrypt cannot be empty.'; die();
        }
        
        if ($hmacSalt === null) {                       
            $hmacSalt = sha1(self::$salt . self::$secretHash . self::$salt);
        }
  
        $key = substr(hash('sha256', $key . $hmacSalt), 0, 32); # Generate the encryption and hmac key.
  
        # Split out hmac for comparison
        $macSize = 64;
        $hmac = substr($cipher, 0, $macSize);
        $cipher = substr($cipher, $macSize);
  
        $compareHmac = hash_hmac('sha256', $cipher, $key);
        if ($hmac !== $compareHmac) {
            return false;
        }
  
        $algorithm = MCRYPT_RIJNDAEL_128; # encryption algorithm
        $mode = MCRYPT_MODE_CBC; # encryption mode
        $ivSize = mcrypt_get_iv_size($algorithm, $mode); # Returns the size of the IV belonging to a specific cipher/mode combination
  
        $iv = substr($cipher, 0, $ivSize);
        $cipher = substr($cipher, $ivSize);
        $plain = mcrypt_decrypt($algorithm, $key, $cipher, $mode, $iv);
        return rtrim($plain, "\0");
    }
}
*/ ?>

<?php include 'lhsecurity.php';?>

<script type="text/javascript">
var LHCChatOptions = {};
LHCChatOptions.opt = {widget_height:340,widget_width:300,popup_height:670,popup_width:500};
LHCChatOptions.attr = new Array();
LHCChatOptions.attr.push({'name':'Bet ID 2','value':'<?php echo base64_encode(lhSecurity::encrypt('emailuserdata','d-fD_f90sF_Sdf0sdf_SDFSDF)SDF_SDF_SD)F_F','d-fD$5_F_dfsdf45sdf4f_SdfosdfjsdkfjlsdfjF'))?>','type':'hidden','size':0,'encrypted':true});

LHCChatOptions.attr_prefill_admin = new Array();
LHCChatOptions.attr_prefill_admin.push({'index':'0', 'value':'<?php echo base64_encode(lhSecurity::encrypt('emailuserdata','d-fD_f90sF_Sdf0sdf_SDFSDF)SDF_SDF_SD)F_F','d-fD$5_F_dfsdf45sdf4f_SdfosdfjsdkfjlsdfjF'))?>', 'encrypted':true, 'hidden':true});

(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
var refferer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://')+1)) : '';
var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
po.src = '//example.com/index.php/chat/getstatus/(check_operator_messages)/true/(dot)/true/(click)/internal/(position)/middle_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true/?r='+refferer+'&l='+location;
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>