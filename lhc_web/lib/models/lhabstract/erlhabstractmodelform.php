<?php

class erLhAbstractModelForm {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_form';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

	public function getState()
	{
		$stateArray = array (
			'id'         	=> $this->id,
			'name'  		=> $this->name,
			'content'  		=> $this->content,
			'recipient'  	=> $this->recipient,
			'active' 		=> $this->active,
			'name_attr' 	=> $this->name_attr,
			'intro_attr' 	=> $this->intro_attr,
			'xls_columns' 	=> $this->xls_columns,
			'pagelayout' 	=> $this->pagelayout,
			'post_content' 	=> $this->post_content
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}

   	public function getFields()
   	{
        return include('lib/core/lhabstract/fields/erlhabstractmodelform.php');
	}

    public function dependJs()
    {
        return "<script type=\"text/javascript\" src=\"".erLhcoreClassDesign::designJS('js/colorpicker.js;js/ace/ace.js')."\"></script>
        <script>
        $(function() {
            ace.config.set('basePath', '".erLhcoreClassDesign::design('js/ace') . "');
            $('textarea[data-editor]').each(function() {
                var textarea = $(this);
                var mode = textarea.data('editor');
                var editDiv = $('<div>', {
                    width: '100%',
                    height: '200px',
                    id: 'ace-'+textarea.attr('name')
                }).insertBefore(textarea);
                textarea.css('display', 'none');
                var editor = ace.edit(editDiv[0]);
                editor.renderer.setShowGutter(true);
                editor.getSession().setValue(textarea.val());
                editor.getSession().setMode('ace/mode/'+mode);
                editor.setOptions({
                    autoScrollEditorIntoView: true,
                    copyWithEmptySelection: true,
                });
                editor.setTheme('ace/theme/github');
                // copy back to textarea on form submit...
                textarea.closest('form').submit(function() {
                    textarea.val(editor.getSession().getValue());
                })
            });
        });
        </script>";
    }

	public function getModuleTranslations()
	{
	    $metaData = array('path' => array('url' => erLhcoreClassDesign::baseurl('form/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('browseoffer/index','Form')),'permission_delete' => array('module' => 'lhform','function' => 'delete_fm'), 'permission' => array('module' => 'lhform','function' => 'manage_fm'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/browserofferinvitation','Forms list'));
	    
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_forms', array('object_meta_data' => & $metaData));
	    
		return $metaData;
	}

	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;

	   	case 'content_rendered':
	   			return erLhcoreClassFormRenderer::renderForm($this);

	   	case 'content_rendered_admin':
	   			return erLhcoreClassFormRenderer::renderForm($this, true);

	   	case 'xls_columns_data':
	   			$parts = explode('||',$this->xls_columns);
	   			$totalParts = array();
	   			
	   			foreach ($parts as $part) {
	   				$subParts = explode(';', $part);
	   				$dataParts = array();
	   				foreach ($subParts as $subPart) {
	   					$data = explode('=', $subPart);
	   					$dataParts[$data[0]] = $data[1];
	   				}
	   				$totalParts[] = $dataParts;
	   			}
	   			
	   			return $this->xls_columns_data = $totalParts;

	   		
	   	case 'hide_delete':
	   			return $this->hide_delete = !erLhcoreClassUser::instance()->hasAccessTo('lhform','delete_fm');

	   	default:
	   		break;
	   }
	}

   	public $id = null;
	public $name = '';
	public $content = '';	
	public $active = 1;
	public $recipient = '';
	public $name_attr = '';
	public $intro_attr = '';	
	public $xls_columns = '';	
	public $pagelayout = '';	
	public $post_content = '';	
	
	public $hide_add = false;

}

?>