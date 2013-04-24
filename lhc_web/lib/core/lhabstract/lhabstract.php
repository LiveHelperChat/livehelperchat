<?php

class erLhcoreClassAbstract {

    public static function renderInput($name, $attr, $object)
    {
        switch ($attr['type']) {

        	case 'text':
        	       if (isset($attr['multilanguage']) && $attr['multilanguage'] == true) {
        	           $returnString = '';
        	           foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_locales' ) as $locale){
        	                   $returnString .= '<input class="abstract_input" name="AbstractInput_'.$name.'_'.$locale.'" type="text" value="'.htmlspecialchars($object->{$name.'_'.strtolower($locale)}).'" />'.$locale.'<br/>';
        	           }

        	           return $returnString;
        	       } else {
        		      return '<input class="abstract_input" class="abstract_input" name="AbstractInput_'.$name.'" type="text" value="'.htmlspecialchars($object->$name).'" />';
        	       }
        		break;

        	case 'textarea':

        		$height = isset($attr['height']) ? $attr['height'] : '300px';

        		if (isset($attr['multilanguage']) && $attr['multilanguage'] == true) {
        			$returnString = '';

        			foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_locales' ) as $locale){
        				$returnString .= '<textarea style="height:'.$height.';"  class="abstract_input" name="AbstractInput_'.$name.'_'.$locale.'">'.htmlspecialchars($object->{$name.'_'.strtolower($locale)}).'</textarea>'.$locale.'<br/>';
        			}

        			return $returnString;
        		} else {
        		      return '<textarea style="height:'.$height.';"  class="abstract_input" name="AbstractInput_'.$name.'">'.htmlspecialchars($object->$name).'</textarea>';
        		}
        		break;

        	case 'checkbox':
        	          $selected = $object->$name == 1 ? ' checked="checked" ' : '';
        		      return '<input type="checkbox" name="AbstractInput_'.$name.'" value="1" '.$selected.' />';
        		break;

			case 'imgfile':
        		      return '<input type="file" name="AbstractInput_'.$name.'"/>';
        		break;

        	case 'file':
        			$fields = $object->getFields();
        			$img = $object->{$attr['frontend']};
        		    if($img){
        				return '<input type="file" name="AbstractInput_'.$name.'"/><br/><br/>'.$img.'<br/><br/><input type="checkbox" name="AbstractInput_'.$name.'_delete" value="1" /> Delete Image';
        		    } else {
        		    	return '<input type="file" name="AbstractInput_'.$name.'"/>';
        		    }
        		break;

        	case 'filebinary':
        			$fields = $object->getFields();
        			$img = $object->file_url;
        		    if($img){
        				return '<input type="file" name="AbstractInput_'.$name.'"/><br/><br/>'.$img.'<br/><br/><input type="checkbox" name="AbstractInput_'.$name.'_delete" value="1" /> Delete File';
        		    } else {
        		    	return '<input type="file" name="AbstractInput_'.$name.'"/>';
        		    }
        		break;

        	case 'combobox':

        	        $onchange = isset($attr['on_change']) ? $attr['on_change'] : '';

            	    $return = '<select class="abstract_input" name="AbstractInput_'.$name.'"'.$onchange.'><option value="0">Choose option</option>';
            	    $items = call_user_func($attr['source'],$attr['params_call']);
            	    foreach ($items as $item)
            	    {
            	        $selected = $object->$name == $item->id ? 'selected="selected"' : '';
            	        $nameAttr = isset($attr['name_attr']) ? $item->{$attr['name_attr']} : ((string)$item);

            	        $return .= '<option value="'.$item->id.'" '.$selected.'>'.((string)$nameAttr).'</option>';
            	    }
            	    $return .= "</select>";

            	    if (isset($attr['div_id'])){
                        $return = '<div id="'.$attr['div_id'].'">'.$return.'</div>';
                    }

            	    return $return;
        	    break;

        	    case 'combobox_multi':
            	    $return = '<select class="abstract_input" name="AbstractInput_'.$name.'[]" multiple size="5">';
            	    $items = call_user_func($attr['source'],$attr['params_call']);
            	    foreach ($items as $item)
            	    {
            	        $selected = in_array($item->id,$object->{$attr['backend']}) ? 'selected="selected"' : '';
            	        $return .= '<option value="'.$item->id.'" '.$selected.'>'.((string)$item).'</option>';
            	    }
            	    $return .= "</select>";
            	    return $return;
        	    break;


        	default:
        		break;
        }
    }

    public static function validateInput(& $object)
    {
        $definition = array();
        $fields = $object->getFields();
        foreach ($fields as $key => $field){

            if (isset($field['multilanguage']) && $field['multilanguage'] == true) {
                foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_locales' ) as $locale) {
                    $definition['AbstractInput_'.$key.'_'.$locale] = $field['validation_definition'];
                }
            } else {
                $definition['AbstractInput_'.$key] = $field['validation_definition'];
            }
        }

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        foreach ($fields as $key => $field)
        {
            if ($field['type'] == 'checkbox' ) {

                if ($form->hasValidData( 'AbstractInput_'.$key ) && $form->{'AbstractInput_'.$key} == 1) {
                    $object->$key = 1;
                } else {
                   $object->$key = 0;
                }

            } elseif ($field['type'] == 'location') {

                if ( $form->hasValidData( 'AbstractInput_'.$key ) ) {
                    $object->$key = $form->{'AbstractInput_'.$key};
                    $object->lat = $_POST['AbstractInput_'.$key.'_lat'];
                    $object->lon = $_POST['AbstractInput_'.$key.'_lon'];
                }

            } elseif ($field['type'] == 'combobox_multi') {

                if ($form->hasValidData( 'AbstractInput_'.$key )){
                    $object->{$field['backend_call']}($form->{'AbstractInput_'.$key});
                } else {
                    $object->{$field['backend_call']}(array());
                }

            } elseif ($field['type'] == 'file' || $field['type'] == 'filebinary'){
            	 if (erLhcoreClassSearchHandler::isFile( 'AbstractInput_'.$key)){
                   $object->{$field['backend_call']}();
                 }

                 if (isset($_POST['AbstractInput_'.$key.'_delete']) && $_POST['AbstractInput_'.$key.'_delete'] == 1) {
                 	$object->{$field['delete_call']}();
                 }

            } elseif ($field['type'] == 'imgfile'){
            	 if (erLhcoreClassSearchHandler::isFile( 'AbstractInput_'.$key)){
                   $object->{$field['backend_call']}();
               }

            } elseif ($field['type'] == 'textarea') {

            	if ( isset($field['multilanguage']) && $field['multilanguage'] == true ) {
            		foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_locales' ) as $locale) {
            			$object->{$key.'_'.strtolower($locale)}  = $form->{'AbstractInput_'.$key.'_'.$locale};
            		}
            	} else {
            		if ($form->hasValidData( 'AbstractInput_'.$key )){
            			$object->$key = $form->{'AbstractInput_'.$key};
            		}
            	}


            } elseif ($field['type'] == 'text' && isset($field['multilanguage']) && $field['multilanguage'] == true) {

                foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_locales' ) as $locale) {
                    $object->{$key.'_'.strtolower($locale)}  = $form->{'AbstractInput_'.$key.'_'.$locale};
                }

            } elseif ($form->hasValidData( 'AbstractInput_'.$key ) && (($field['required'] == false) || ($field['type'] == 'combobox') ||($field['required'] == true && $field['type'] == 'text' && $form->{'AbstractInput_'.$key} != '') )) {

                if (isset($field['multilanguage']) && $field['multilanguage'] == true) {

                    $partsTranslated = array();
                    foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_locales' ) as $locale) {
                        $partsTranslated[$locale] = $form->{'AbstractInput_'.$key.'_'.$locale};
                    }

                    $object->$key = serialize($partsTranslated);

                } else {
                    $object->$key = $form->{'AbstractInput_'.$key};
                }

            } elseif ($field['required'] == true) {
                $Errors[$key] = $field['trans'].' is required';
            }
        }

        return $Errors;

    }

    public static function getSession()
    {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhabstract' )
            );
        }
        return self::$persistentSession;
    }

    private static $persistentSession;
    private static $instance = null;
}

?>