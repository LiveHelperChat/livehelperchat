<?php
/**
 * 
 * @author Remigijus Kiminas
 * 
 * @desc Main chat survey object
 *
 */

class erLhAbstractModelSurvey {

    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_abstract_survey';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';
    
    public static $dbSortOrder = 'ASC';

    public static $dbDefaultSort = '`identifier` ASC, `id` DESC';

	public function getState()
	{
		$stateArray = array (
			'id'         	           => $this->id,
			'name'  		           => $this->name,
			'identifier'  		       => $this->identifier,

			'max_stars_1_title'		   => $this->max_stars_1_title,
			'max_stars_1'		       => $this->max_stars_1,
			'max_stars_1_pos'		   => $this->max_stars_1_pos,
			'max_stars_1_enabled'	   => $this->max_stars_1_enabled,
			'max_stars_1_req'	       => $this->max_stars_1_req,

			'max_stars_2_title'		   => $this->max_stars_2_title,
			'max_stars_2'		       => $this->max_stars_2,
			'max_stars_2_pos'		   => $this->max_stars_2_pos,
			'max_stars_2_enabled'	   => $this->max_stars_2_enabled,
			'max_stars_2_req'	       => $this->max_stars_2_req,

			'max_stars_3_title'		   => $this->max_stars_3_title,
			'max_stars_3'		       => $this->max_stars_3,
			'max_stars_3_pos'		   => $this->max_stars_3_pos,
			'max_stars_3_enabled'	   => $this->max_stars_3_enabled,
			'max_stars_3_req'	       => $this->max_stars_3_req,

			'max_stars_4_title'		   => $this->max_stars_4_title,
			'max_stars_4'		       => $this->max_stars_4,
			'max_stars_4_pos'		   => $this->max_stars_4_pos,
			'max_stars_4_enabled'	   => $this->max_stars_4_enabled,
			'max_stars_4_req'	       => $this->max_stars_4_req,
		    
			'max_stars_5_title'		   => $this->max_stars_5_title,
			'max_stars_5'		       => $this->max_stars_5,
			'max_stars_5_pos'		   => $this->max_stars_5_pos,
			'max_stars_5_enabled'	   => $this->max_stars_5_enabled,
			'max_stars_5_req'	       => $this->max_stars_5_req,
		    
			'question_options_1'	   	=> $this->question_options_1,
			'question_options_1_items' 	=> $this->question_options_1_items,
			'question_options_1_pos'   	=> $this->question_options_1_pos,
			'question_options_1_enabled'=> $this->question_options_1_enabled,
			'question_options_1_req'    => $this->question_options_1_req,
		    
			'question_options_2'	   	=> $this->question_options_2,
			'question_options_2_items' 	=> $this->question_options_2_items,
			'question_options_2_pos'   	=> $this->question_options_2_pos,
			'question_options_2_enabled'=> $this->question_options_2_enabled,
			'question_options_2_req'    => $this->question_options_2_req,
				
			'question_options_3'	   	=> $this->question_options_3,
			'question_options_3_items' 	=> $this->question_options_3_items,
			'question_options_3_pos'   	=> $this->question_options_3_pos,
			'question_options_3_enabled'=> $this->question_options_3_enabled,
			'question_options_3_req'    => $this->question_options_3_req,

			'question_options_4'	   	=> $this->question_options_4,
			'question_options_4_items' 	=> $this->question_options_4_items,
			'question_options_4_pos'   	=> $this->question_options_4_pos,
			'question_options_4_enabled'=> $this->question_options_4_enabled,
			'question_options_4_req'    => $this->question_options_4_req,
				
			'question_options_5'	   	=> $this->question_options_5,
			'question_options_5_items' 	=> $this->question_options_5_items,
			'question_options_5_pos'   	=> $this->question_options_5_pos,
			'question_options_5_enabled'=> $this->question_options_5_enabled,
			'question_options_5_req'    => $this->question_options_5_req,
		    
			'question_plain_1'         => $this->question_plain_1,
			'question_plain_1_pos'     => $this->question_plain_1_pos,
			'question_plain_1_enabled' => $this->question_plain_1_enabled,
			'question_plain_1_req'     => $this->question_plain_1_req,
		    
			'question_plain_2'         => $this->question_plain_2,
			'question_plain_2_pos'     => $this->question_plain_2_pos,
			'question_plain_2_enabled' => $this->question_plain_2_enabled,
			'question_plain_2_req'     => $this->question_plain_2_req,
		    
			'question_plain_3'         => $this->question_plain_3,
			'question_plain_3_pos'     => $this->question_plain_3_pos,
			'question_plain_3_enabled' => $this->question_plain_3_enabled,
			'question_plain_3_req'     => $this->question_plain_3_req,
		    
			'question_plain_4'         => $this->question_plain_4,
			'question_plain_4_pos'     => $this->question_plain_4_pos,
			'question_plain_4_enabled' => $this->question_plain_4_enabled,
			'question_plain_4_req'     => $this->question_plain_4_req,
		    
			'question_plain_5'         => $this->question_plain_5,
			'question_plain_5_pos'     => $this->question_plain_5_pos,
			'question_plain_5_enabled' => $this->question_plain_5_enabled,
			'question_plain_5_req'     => $this->question_plain_5_req,
		    
			'feedback_text'            => $this->feedback_text,
			'configuration'            => $this->configuration
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->name;
	}

   	public function getFields()
   	{
   	    return include('lib/core/lhabstract/fields/erlhabstractmodelsurvey.php');
	}

	public function getModuleTranslations()
	{
	    $metaData = array('permission_delete' => array('module' => 'lhsurvey','function' => 'manage_survey'),'permission' => array('module' => 'lhsurvey','function' => 'manage_survey'),'name' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/survey','Survey'));
	    /**
	     * Get's executed before permissions check. It can redirect to frontpage throw permission exception etc
	     * */
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('feature.can_use_survey', array('object_meta_data' => & $metaData));
	    
		return $metaData;
	}
	
	public function __get($var)
	{
	   switch ($var) {
	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;

       case 'configuration_array':
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

	   	case 'question_options_1_items_front':
	   	case 'question_options_2_items_front':
	   	case 'question_options_3_items_front':
	   	case 'question_options_4_items_front':
	   	case 'question_options_5_items_front':
	   			   $field = str_replace('_front', '', $var);
	   			   $items = explode('||==========||', $this->{$field});

	   			   foreach ($items as $index => $item) {

                        $matches = array();
                        preg_match('/\[value=(.*?)\]/', $item, $matches);

                        if (isset($matches[1]) && is_numeric($matches[1])){
                            $index = (int)$matches[1];
                        }

	   			   		$this->{$var}[$index] = array('option' => $item);
	   			   }
	   			   
	   		   return $this->{$var};
	   		break;

	   		
	   	default:
	   		break;
	   }
	}

    public function validateInput($params)
    {
        $params['obj'] = & $this;
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('abstract.survey_edit_validate', $params);
    }

	public function beforeRemove()
	{
	    $q = ezcDbInstance::get()->createDeleteQuery();
	    
	    // Messages
	    $q->deleteFrom( 'lh_abstract_survey_item' )->where( $q->expr->eq( 'survey_id', $this->id ) );
	    $stmt = $q->prepare();
	    $stmt->execute();
	}

	public function getStarsNumberVotes($id, $stars, $filter = array())
	{
	    $filterDefault = array('filterin' => array('max_stars_' . $id => $stars), 'filter' => array( 'survey_id' => $this->id));

        $filterDefault = array_replace_recursive($filter, $filterDefault);

	    return erLhAbstractModelSurveyItem::getCount($filterDefault);
	}

	public function getStarsNumberVotesTotal($id)
	{
	    return erLhAbstractModelSurveyItem::getCount(array('filtergt' => array('max_stars_' . $id => 0),'filter' => array( 'survey_id' => $this->id)));
	}

	public function getQuestionsOptionsVotesTotal($id)
    {
        return erLhAbstractModelSurveyItem::getCount(array('filtergt' => array('question_options_' . $id => 0),'filter' => array( 'survey_id' => $this->id)));
    }

    public function getQuestionsNumberVotes($id, $value, $filter = array())
    {
        $filterDefault = array('filterin' => array('question_options_' . $id => $value),'filter' => array( 'survey_id' => $this->id));

        $filterDefault = array_replace_recursive($filter, $filterDefault);

        return erLhAbstractModelSurveyItem::getCount($filterDefault);
    }

	public function getStarsNumberAverage($id, $filter = array())
	{
        $filterDefault = array('filtergt' => array('max_stars_' . $id => 0),'filter' => array( 'survey_id' => $this->id));

        $filterDefault = array_replace_recursive($filter, $filterDefault);

	    return erLhAbstractModelSurveyItem::getCount($filterDefault, 'AVG', 'max_stars_' . $id);
	}

	public function customForm() {
	    return 'survey.tpl.php';
	}

    public function beforeUpdate()
    {
        $this->configuration = json_encode($this->configuration_array);
    }

    public function beforeSave()
    {
        $this->configuration = json_encode($this->configuration_array);
    }

    public function dependFooterJs()
    {
        return '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.lhc.theme.js').'"></script>';
    }

    public function translate() {

        $chatLocaleFallback = erConfigClassLhConfig::getInstance()->getDirLanguage('content_language');
        $chatLocale = erLhcoreClassChatValidator::getVisitorLocale();

        $attributesDirect = array(
            'feedback_text'
        );

        $translatableAttributes = array_merge(array(
            'survey_title'
        ),$attributesDirect);

        $attributes = $this->configuration_array;

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

        $this->configuration_array = $attributes;
    }

   	public $id = null;
	public $name = '';
	
	public $max_stars_1_title = '';
	public $max_stars_1 = 0;
	public $max_stars_1_pos = 0;
	public $max_stars_1_enabled = 0;
	public $max_stars_1_req = 0;
	
	public $max_stars_2_title = '';
	public $max_stars_2 = 0;
	public $max_stars_2_pos = 0;
	public $max_stars_2_enabled = 0;
	public $max_stars_2_req = 0;
	
	public $max_stars_3_title = '';
	public $max_stars_3 = 0;
	public $max_stars_3_pos = 0;
	public $max_stars_3_enabled = 0;
	public $max_stars_3_req = 0;
	
	public $max_stars_4_title = '';
	public $max_stars_4 = 0;
	public $max_stars_4_pos = 0;
	public $max_stars_4_enabled = 0;
	public $max_stars_4_req = 0;
	
	public $max_stars_5_title = '';
	public $max_stars_5 = 0;
	public $max_stars_5_pos = 0;
	public $max_stars_5_enabled = 0;
	public $max_stars_5_req = 0;
	
	public $question_options_1 = '';
	public $question_options_1_items = '';
	public $question_options_1_pos = 0;
	public $question_options_1_enabled = 0;
	public $question_options_1_req = 0;
	
	public $question_options_2 = '';
	public $question_options_2_items = '';
	public $question_options_2_pos = 0;
	public $question_options_2_enabled = 0;
	public $question_options_2_req = 0;
	
	public $question_options_3 = '';
	public $question_options_3_items = '';
	public $question_options_3_pos = 0;
	public $question_options_3_enabled = 0;
	public $question_options_3_req = 0;
	
	public $question_options_4 = '';
	public $question_options_4_items = '';
	public $question_options_4_pos = 0;
	public $question_options_4_enabled = 0;
	
	public $question_options_5 = '';
	public $question_options_5_items = '';
	public $question_options_5_pos = 0;
	public $question_options_5_enabled = 0;
	public $question_options_5_req = 0;
	
	public $question_plain_1 = '';
	public $question_plain_1_pos = 0;
	public $question_plain_1_enabled = 0;
	public $question_plain_1_req = 0;
	
	public $question_plain_2 = '';
	public $question_plain_2_pos = 0;
	public $question_plain_2_enabled = 0;
	public $question_plain_2_req = 0;
	
	public $question_plain_3 = '';
	public $question_plain_3_pos = 0;
	public $question_plain_3_enabled = 0;
	public $question_plain_3_req = 0;
	
	public $question_plain_4 = '';
	public $question_plain_4_pos = 0;
	public $question_plain_4_enabled = 0;
	public $question_plain_4_req = 0;
	
	public $question_plain_5 = '';
	public $question_plain_5_pos = 0;
	public $question_plain_5_enabled = 0;
	public $question_plain_5_req = 0;
	
	public $feedback_text = '';
	public $configuration = '';
	public $identifier = '';

	public $hide_add = false;
	public $hide_delete = false;

}

?>