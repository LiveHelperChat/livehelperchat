<?php

class erLhAbstractModelEmailTemplate {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_email_template';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'ASC';

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'from_name'  	=> $this->from_name,
			'from_name_ac'  => $this->from_name_ac,
			'from_email' 	=> $this->from_email,
			'from_email_ac' => $this->from_email_ac,
			'reply_to' 		=> $this->reply_to,
			'reply_to_ac' 	=> $this->reply_to_ac,
			'name'       	=> $this->name,
			'subject'       => $this->subject,
			'subject_ac'    => $this->subject_ac,
			'recipient'     => $this->recipient,
			'user_mail_as_sender'     => $this->user_mail_as_sender,
			'content'    	=> $this->content,
			'bcc_recipients'=> $this->bcc_recipients,
			'translations'=> $this->translations,
			'use_chat_locale'=> $this->use_chat_locale,
		);

		return $stateArray;
	}

    public function customForm() {
        return 'email_template.tpl.php';
    }

	public function __toString()
	{
		return $this->name;
	}

   	public function getFields()
   	{
        return include('lib/core/lhabstract/fields/erlhabstractmodelemailtemplate.php');
	}

    public function beforeUpdate()
    {
        $this->translations = json_encode($this->translations_array);
    }

    public function beforeSave()
    {
        $this->translations = json_encode($this->translations_array);
    }

	public function getModuleTranslations()
	{
		return array('permission_delete' => array('module' => 'lhsystem','function' => 'changetemplates'),'permission' => array('module' => 'lhsystem','function' => 'changetemplates'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/email_template','E-mail templates'));
	}

    public function translate($locale = '') {
        $chatLocale = null;
        $chatLocaleFallback = erConfigClassLhConfig::getInstance()->getDirLanguage('content_language');

        if ($this->use_chat_locale == 1) {
            if ($locale != '') {
                $chatLocale = $locale;
            } else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $parts = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
                $languages = explode(',',$parts[0]);
                if (isset($languages[0])) {
                    $chatLocale = $languages[0];
                }
            }
        }

        $attributesDirect = array(
            'subject',
            'from_name',
            'content'
        );

        $translatableAttributes = array_merge(array(
        ),$attributesDirect);

        $attributes = $this->translations_array;

        foreach ($translatableAttributes as $attr) {
            if (isset($attributes[$attr . '_lang'])) {

                $translated = false;

                if ($chatLocale !== null) {
                    foreach ($attributes[$attr . '_lang'] as $attrTrans) {
                        if (in_array($chatLocale, $attrTrans['languages']) && $attrTrans['content'] != '') {
                            $attributes[$attr] = $attrTrans['content'];
                            $translated = true;
                            break;
                        }
                    }
                }

                if ($translated == false) {
                    foreach ($attributes[$attr . '_lang'] as $attrTrans) {
                        if (in_array($chatLocaleFallback, $attrTrans['languages']) && $attrTrans['content'] != '') {
                            $attributes[$attr] = $attrTrans['content'];
                            $translated = true;
                            break;
                        }
                    }
                }

                if ($translated === true && in_array($attr,$attributesDirect)) {
                    $this->$attr = $attributes[$attr];
                }
            }
        }

        $this->translations_array = $attributes;
    }

    public function dependFooterJs()
    {
        return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.lhc.theme.js').'"></script>';
    }

	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;

       case 'translations_array':
           $attr = str_replace('_array','',$var);
           if (!empty($this->{$attr})) {
               $jsonData = json_decode($this->{$attr},true);
               if ($jsonData !== null) {
                   $this->{$var} = $jsonData;
               } else {
                   $this->{$var} = array();
               }
           } else {
               $this->{$var} = array();
           }
           return $this->{$var};
           break;

	   	default:
	   		break;
	   }
	}

   	public $id = null;
	public $name = '';
	public $subject = '';
	public $subject_ac = 0;
	public $from_name = '';
	public $from_name_ac = 0;
	public $from_email = '';
	public $from_email_ac = 0;
	public $reply_to = '';
	public $reply_to_ac = 0;
	public $user_mail_as_sender = 0;
	public $content = '';
	public $recipient = '';
	public $bcc_recipients = '';
	public $translations = '';
	public $use_chat_locale = 0;

	public $hide_add = true;
	public $hide_delete = true;

}

?>