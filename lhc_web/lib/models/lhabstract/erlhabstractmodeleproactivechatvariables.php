<?php

class erLhAbstractModelProactiveChatVariables{

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_proactive_chat_variables';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'identifier'  	=> $this->identifier,
			'store_timeout' => $this->store_timeout,
			'filter_val'    => $this->filter_val
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}

	public function getFields()
   	{
   		return include('lib/core/lhabstract/fields/erlhabstractmodeleproactivechatvariables.php');
	}

	public function getModuleTranslations()
	{
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    $metaData = array('permission_delete' => array('module' => 'lhchat','function' => 'administrateinvitations'),'permission' => array('module' => 'lhchat','function' => 'administrateinvitations'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','Pro active chat variables'));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_proactive', array('object_meta_data' => & $metaData));	
	    
		return $metaData;
	}

	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;

	   	default:
	   		break;
	   }
	}

   	public $id = null;
	public $name = '';
	public $identifier = '';
	public $store_timeout = 0;
	public $filter_val = 0;

	public $hide_add = false;
	public $hide_delete = false;
}

?>