<?php

class lhSecurity {

    public static $method = 'AES-256-CBC';

    public static function encrypt(string $data, string $key) : string
    {
        $ivSize = openssl_cipher_iv_length(self::$method);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, self::$method, $key, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }

    public static function decrypt(string $data, string $key) : string
    {
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length(self::$method);
        $iv = substr($data, 0, $ivSize);
        $data = openssl_decrypt(substr($data, $ivSize), self::$method, $key, OPENSSL_RAW_DATA, $iv);

        return $data;
    }
}

?>

<script type="text/javascript">
var LHCChatOptions = {};
LHCChatOptions.opt = {widget_height:340,widget_width:300,popup_height:670,popup_width:500};
LHCChatOptions.attr = new Array();
LHCChatOptions.attr.push({'name':'Bet ID 2','value':'<?php echo lhSecurity::encrypt('emailuserdata','d-fD_f90sF_Sdf0sdf_SDFSDF)SDF_SDF_SD)F_F')?>','type':'hidden','size':0,'encrypted':true});

LHCChatOptions.attr_prefill_admin = new Array();
LHCChatOptions.attr_prefill_admin.push({'index':'0', 'value':'<?php echo lhSecurity::encrypt('emailuserdata','d-fD_f90sF_Sdf0sdf_SDFSDF)SDF_SDF_SD)F_F')?>', 'encrypted':true, 'hidden':true});

(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
var refferer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://')+1)) : '';
var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
po.src = '//example.com/index.php/chat/getstatus/(check_operator_messages)/true/(dot)/true/(click)/internal/(position)/middle_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true/?r='+refferer+'&l='+location;
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>