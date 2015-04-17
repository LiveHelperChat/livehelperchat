<?php

/**
 * This code is mix of WP and phpBB :)
 * */
class erLhcoreClassBBCode
{
    // From WP, that's why we love open source :)
    public static function _make_url_clickable_cb($matches) {
    	$url = $matches[2];
    	$suffix = '';

    	/** Include parentheses in the URL only if paired **/
    	while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
    		$suffix = strrchr( $url, ')' ) . $suffix;
    		$url = substr( $url, 0, strrpos( $url, ')' ) );
    	}

    	if ( empty($url) )
    		return $matches[0];

    	return $matches[1] . "<a href=\"$url\" class=\"link\" target=\"_blank\">$url</a>" . $suffix;
   }

   private static $outArray = null;
   
   public static function getOutArray() {
   	
   		if (self::$outArray == null) {   			
   			$tpl = new erLhcoreClassTemplate();
   			$smileys = explode('||', str_replace("\n", '', $tpl->fetch('lhbbcode/smiley.tpl.php')));   			
   			self::$outArray = $smileys;
   		}
   	
	   	return self::$outArray;
   }
   
   public static function BBCode2Html($text) {
    	$text = trim($text);

    	// Smileys to find...
    	$in = array( 	 ':)',
    					 ':D:',
    					 ':(',
    					 ':o:',
    					 ':p:',
    					 ';)'
    	);

    	// And replace them by...
    	$out = self::getOutArray();
    	
    	$in[] = '[/*]';
    	$in[] = '[*]';
    	$out[] = '</li>';
    	$out[] = '<li>';
    	    	
    	$text = str_replace($in, $out, $text);

    	// BBCode to find...
    	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
    					 '/\[i\](.*?)\[\/i\]/ms',
    					 '/\[u\](.*?)\[\/u\]/ms',
    					 '/\[s\](.*?)\[\/s\]/ms',
    					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
    					 '/\[list\](.*?)\[\/list\]/ms',
    					 '/\[\*\]\s?(.*?)\n/ms'
    	);
    	// And replace them by...
    	$out = array(	 '<strong>\1</strong>',
    					 '<em>\1</em>',
    					 '<u>\1</u>',
    					 '<strike>\1</strike>',
    					 '<ol start="\1">\2</ol>',
    					 '<ul>\1</ul>',
    					 '<li>\1</li>'
    	);
    	$text = preg_replace($in, $out, $text);


    	$text = preg_replace_callback('/\[img\](.*?)\[\/img\]/ms', "erLhcoreClassBBCode::_make_url_embed_image", $text);

    	$text = preg_replace_callback('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms', "erLhcoreClassBBCode::_make_url_embed", $text);

    	$text = preg_replace_callback('/\[flattr\](.*?)\[\/flattr\]/ms', "erLhcoreClassBBCode::_make_flattr_embed", $text);


    	// Prepare quote's
    	$text = str_replace("\r\n","\n",$text);

    	// paragraphs
    	$text = str_replace("\r", "", $text);
    	$text = nl2br($text);

    	// clean some tags to remain strict
    	// not very elegant, but it works. No time to do better ;)
    	if (!function_exists('removeBr')) {
    		function removeBr($s) {
    			return str_replace("<br />", "", $s[0]);
    		}
    	}

    	$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
    	$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

    	$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
    	$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

    	return $text;
    }

    public static function _make_url_embed_image($matches){

        $in = $matches[1];
        $in = trim($in);
		$error = false;
        $forumImage = false;


		$in = str_replace(' ', '%20', $in);

	    $inline =  ')';
		$scheme = '[a-z\d+\-.]';
		// generated with regex generation file in the develop folder
		$exp_url = "[a-z]$scheme*:/{2}(?:(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+|[0-9.]+|\[[a-z0-9.]+:[a-z0-9.]+:[a-z0-9.:]+\])(?::\d*)?(?:/(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";

		$inline = ')';
		$www_url = "www\.(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+(?::\d*)?(?:/(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";

		// Localy uploaded photo
		$instance = erLhcoreClassSystem::instance();
		$instance->wwwDir();
		if (preg_match('#^'.$instance->wwwDir().'/var/forum/[a-zA-Z0-9_\-.\/\\\]*$#i', $in) ) {
			$forumImage = true;
		// Checking urls
		} elseif ( !preg_match('#^' . $exp_url . '$#i', $in) && !preg_match('#^' . $www_url . '$#i', $in)) {
		    return '[img]' . $in . '[/img]';
		}

		// Try to cope with a common user error... not specifying a protocol but only a subdomain
		if ($forumImage == false && !preg_match('#^[a-z0-9]+://#i', $in))
		{
			$in = 'http://' . $in;
		}

        return "<div class=\"img_embed\"><img src=\"".htmlspecialchars($in)."\" alt=\"\" /></div>";
   }

   public static function _make_url_embed($matches){

        $in = $matches[1];
        $in = trim($in);
		$error = false;

		$in = str_replace(' ', '%20', $in);

	    $inline =  ')';
		$scheme = '[a-z\d+\-.]';
		// generated with regex generation file in the develop folder
		$exp_url = "[a-z]$scheme*:/{2}(?:(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+|[0-9.]+|\[[a-z0-9.]+:[a-z0-9.]+:[a-z0-9.:]+\])(?::\d*)?(?:/(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";

		$inline = ')';
		$www_url = "www\.(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+(?::\d*)?(?:/(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";

		// Checking urls
		if (!preg_match('#^' . $exp_url . '$#i', $in) && !preg_match('#^' . $www_url . '$#i', $in))
		{
		    return '[url='.$matches[1].']' . $matches[2] . '[/url]';
		}

		if (!preg_match('#^[a-z][a-z\d+\-.]*:/{2}#i', $in))
		{
			$in = 'http://' . $in;
		}

        return '<a class=\"link\" target=\"_blank\" href="'.$in.'">'.$matches[2].'</a>';
   }

   public static function _make_flattr_embed($matches){

        $in = $matches[1];
        $in = trim($in);
		$error = false;

		$in = str_replace(' ', '%20', $in);

	    $inline =  ')';
		$scheme = '[a-z\d+\-.]';
		// generated with regex generation file in the develop folder
		$exp_url = "[a-z]$scheme*:/{2}(?:(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+|[0-9.]+|\[[a-z0-9.]+:[a-z0-9.]+:[a-z0-9.:]+\])(?::\d*)?(?:/(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";

		$inline = ')';
		$www_url = "www\.(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+(?::\d*)?(?:/(?:[a-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";

		// Checking urls
		if (!preg_match('#^' . $exp_url . '$#i', $in) && !preg_match('#^' . $www_url . '$#i', $in))
		{
		    return '[flattr]' . $matches[1] . '[/flattr]';
		}

		if (!preg_match('#^[a-z][a-z\d+\-.]*:/{2}#i', $in))
		{
			$in = 'http://' . $in;
		}

        return '<a href="'.$in.'" rel="nofollow" title="Flattr this"><img src="'.erLhcoreClassDesign::design('images/icons/flattr-badge-large.png').'"</a>';
   }

   // From WP :)
   public static function _make_web_ftp_clickable_cb($matches) {
    	$ret = '';
    	$dest = $matches[2];
    	$dest = 'http://' . $dest;
    	if ( empty($dest) )
    		return $matches[0];

    	// removed trailing [.,;:)] from URL
    	if ( in_array( substr($dest, -1), array('.', ',', ';', ':', ')') ) === true ) {
    		$ret = substr($dest, -1);
    		$dest = substr($dest, 0, strlen($dest)-1);
    	}
    	return $matches[1] . "<a href=\"$dest\" class=\"link\" target=\"_blank\">$dest</a>$ret";
   }

   // From WP :)
   public static function _make_email_clickable_cb($matches) {
    	$email = $matches[2] . '@' . $matches[3];
    	return $matches[1] . "<a href=\"mailto:$email\" class=\"mail\">$email</a>";
   }

   public static function _make_paypal_button($matches){

         if (filter_var($matches[1],FILTER_VALIDATE_EMAIL)) {
            return '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_donations">
            <input type="hidden" name="business" value="'.$matches[1].'">
            <input type="hidden" name="lc" value="US">
            <input type="hidden" name="no_note" value="0">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
            <input type="image" title="Support an artist" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>';
        } else {
            return $matches[0];
        }
   }

   public static function _make_youtube_block($matches) {

         $data = parse_url($matches[1]);

         if (isset($data['query'])){
             parse_str($data['query'],$query);
             if (stristr($data['host'],'youtube.com') && isset($query['v']) && ($query['v'] != '')) {
                 return '<iframe class="youtube-frame" title="YouTube video player" width="480" height="300" src="http://www.youtube.com/embed/'.urlencode($query['v']).'" frameborder="0" allowfullscreen></iframe>';
             } else {
                 return $matches[0];
             }
         } else {
             return $matches[0];
         }
   }

   public static function _make_url_file($matches){

   		if (isset($matches[1])){
   			list($fileID,$hash) = explode('_',$matches[1]);
   			try {
   				$file = erLhcoreClassModelChatFile::fetch($fileID);

   				// Check that user has permission to see the chat. Let say if user purposely types file bbcode
   				if ($hash == md5($file->name.'_'.$file->chat_id)) {
   					return "<a href=\"" . erLhcoreClassDesign::baseurl('file/downloadfile')."/{$file->id}/{$hash}\" target=\"_blank\" class=\"link\" >" . erTranslationClassLhTranslation::getInstance()->getTranslation('file/file','Download file').' - '.htmlspecialchars($file->upload_name).' ['.$file->extension.']' . "</a>";
   				}
   			} catch (Exception $e) {

   			}

   			return '';
   		}
   		return '';
   }

   public static function _make_url_mail_file($matches){

   		if (isset($matches[1])){
   			list($fileID,$hash) = explode('_',$matches[1]);
   			try {
   				$file = erLhcoreClassModelChatFile::fetch($fileID);

   				// Check that user has permission to see the chat. Let say if user purposely types file bbcode
   				if ($hash == md5($file->name.'_'.$file->chat_id)) {
   					return erLhcoreClassXMP::getBaseHost().$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurldirect('file/downloadfile')."/{$file->id}/{$hash}";
   				}
   			} catch (Exception $e) {

   			}

   			return '';
   		}
   		return '';
   }

   // From WP :)
   public static function make_clickable($ret) {
    	$ret = ' ' . $ret;
    	// in testing, using arrays here was found to be faster
    	$ret = preg_replace_callback('#(?<!=[\'"])(?<=[*\')+.,;:!&$\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#%~/?@\[\]-]|[\'*(+.,;:!=&$](?![\b\)]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', 'erLhcoreClassBBCode::_make_url_clickable_cb', $ret);
    	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', 'erLhcoreClassBBCode::_make_web_ftp_clickable_cb', $ret);
    	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'erLhcoreClassBBCode::_make_email_clickable_cb', $ret);

    	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
    	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);

    	$ret = self::BBCode2Html($ret);

    	// Paypal button
    	$ret = preg_replace_callback('#\[paypal\](.*?)\[/paypal\]#is', 'erLhcoreClassBBCode::_make_paypal_button', $ret);

    	// Youtube block
    	$ret = preg_replace_callback('#\[youtube\](.*?)\[/youtube\]#is', 'erLhcoreClassBBCode::_make_youtube_block', $ret);

    	$ret = preg_replace('#\[translation\](.*?)\[/translation\]#is', '<span class="tr-msg">$1</span>', $ret);

    	// File block
    	$ret = preg_replace_callback('#\[file="?(.*?)"?\]#is', 'erLhcoreClassBBCode::_make_url_file', $ret);

    	$ret = trim($ret);
    	return $ret;
   }

   public static function parseForMail($ret){
   		// File block
   		$ret = preg_replace_callback('#\[file="?(.*?)"?\]#is', 'erLhcoreClassBBCode::_make_url_mail_file', $ret);
   		return trim($ret);
   }
   
   // Makes plain text from BB code
   public static function make_plain($ret){
        $ret = ' ' . $ret;

        // BBCode to find...
    	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
    					 '/\[i\](.*?)\[\/i\]/ms',
    					 '/\[u\](.*?)\[\/u\]/ms',
    					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
    					 '/\[list\](.*?)\[\/list\]/ms',
    					 '/\[\*\]\s?(.*?)\n/ms',
    					 '/\[img\](.*?)\[\/img\]/ms',
    					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
    					 '/\[quote\]/ms',
    					 '/\[\/quote\]/ms',
    					 '/\n/ms',
    	);

    	// And replace them by...
    	$out = array(	 '\1',
    					 '\1',
    					 '\1',
    					 '\2',
    					 '\1',
    					 '\1',
    					 '',
    					 '\2 \1',
    					 '',
    					 ' ',
    	);

    	$ret = preg_replace($in, $out, $ret);

        $ret = trim($ret);
        return $ret;
   }

}


?>