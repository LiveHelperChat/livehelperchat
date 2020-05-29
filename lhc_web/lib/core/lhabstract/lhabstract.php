<?php

class erLhcoreClassAbstract
{

    public static function renderInput($name, $attr, $object, $defaultValue = '')
    {
        switch ($attr['type']) {

            case 'number':
            case 'text':
                if (isset($attr['multilanguage']) && $attr['multilanguage'] == true) {
                    $returnString = '';
                    foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_locales') as $locale) {
                        $returnString .= '<input class="form-control" name="AbstractInput_' . $name . '_' . $locale . '" type="text" value="' . htmlspecialchars($object->{$name . '_' . strtolower($locale)}) . '" />' . $locale . '<br/>';
                    }

                    return $returnString;
                } else {

                    if (isset($attr['main_attr']) && !empty($attr['main_attr'])) {

                        if (isset($object->{$attr['main_attr']}[$name])) {
                            $value = $object->{$attr['main_attr']}[$name];
                        } else {
                            $value = $defaultValue;
                        }

                    } else {
                        $value = $object->$name;
                    }

                    $ngModel = isset($attr['nginit']) ? ' ng-init=\'ngModelAbstractInput_' . $name . '=' . json_encode($value, JSON_HEX_APOS) . '\' ng-model="ngModelAbstractInput_' . $name . '" ' : '';

                    if (isset($attr['placeholder'])) {
                        $ngModel .= " placeholder=\"{$attr['placeholder']}\" ";
                    }

                    if (isset($attr['maxlength'])) {
                        $ngModel .= " maxlength=\"{$attr['maxlength']}\" ";
                    }

                    return '<input class="form-control" ' . $ngModel . ' name="AbstractInput_' . $name . '" type="' . $attr['type'] . '" value="' . htmlspecialchars($value) . '" />';
                }
                break;

            case 'colorpicker':
                if (isset($attr['main_attr']) && !empty($attr['main_attr'])) {
                    if (isset($object->{$attr['main_attr']}[$name])) {
                        $value = $object->{$attr['main_attr']}[$name];
                    } else {
                        $value = '';
                    }
                } else {
                    $value = $object->$name;
                }
                return '<div class="input-group" ng-init=\'bactract_bg_color_' . $name . '=' . json_encode($value, JSON_HEX_APOS) . '\'><div class="input-group-prepend"><span style="background-color:#{{bactract_bg_color_' . $name . '}}" class="input-group-text">#</span></div><input class="form-control" class="abstract_input" ng-model="bactract_bg_color_' . $name . '" id="id_AbstractInput_' . $name . '" name="AbstractInput_' . $name . '" type="text" value="' . htmlspecialchars($value) . '" /></div><script>$(\'#id_AbstractInput_' . $name . '\').ColorPicker({	onSubmit: function(hsb, hex, rgb, el) {		$(el).val(hex);	$(el).trigger(\'input\'); $(el).trigger(\'change\'); $(el).ColorPickerHide();	},	onBeforeShow: function () {		$(this).ColorPickerSetColor(this.value);	}});</script>';
                break;

            case 'textarea':

                $height = isset($attr['height']) ? $attr['height'] : '300px';

                if (isset($attr['multilanguage']) && $attr['multilanguage'] == true) {
                    $returnString = '';

                    foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_locales') as $locale) {
                        $returnString .= '<textarea ng-non-bindable style="height:' . $height . ';"  class="form-control" name="AbstractInput_' . $name . '_' . $locale . '">' . htmlspecialchars($object->{$name . '_' . strtolower($locale)}) . '</textarea>' . $locale . '<br/>';
                    }

                    return $returnString;
                } else {
                    $placeholder = isset($attr['placeholder']) ? 'placeholder="' . htmlspecialchars($attr['placeholder']) . '"' : '';

                    if (isset($attr['main_attr']) && !empty($attr['main_attr'])) {
                        if (isset($object->{$attr['main_attr']}[$name])) {
                            $value = $object->{$attr['main_attr']}[$name];
                        } else {
                            $value = $defaultValue;
                        }
                    } else {
                        $value = $object->$name;
                        if ($value == '') {
                            $value = $defaultValue;
                        }
                    }

                    $ngModel = isset($attr['nginit']) ? ' ng-init=\'ngModelAbstractInput_' . $name . '=' . json_encode($value, JSON_HEX_APOS) . '\' ng-model="ngModelAbstractInput_' . $name . '" ' : 'ng-non-bindable';
                    $aceEditor = isset($attr['ace_editor']) ? ' data-editor="'.$attr['ace_editor'].'" ' : '';

                    return '<textarea  style="height:' . $height . ';" ' . $placeholder . ' ' . $ngModel . ' ' . $aceEditor . ' class="form-control" name="AbstractInput_' . $name . '">' . htmlspecialchars($value) . '</textarea>';
                }
                break;

            case 'checkbox':

                if (isset($attr['main_attr']) && !empty($attr['main_attr'])) {
                    if (isset($object->{$attr['main_attr']}[$name]) && $object->{$attr['main_attr']}[$name] == 1) {
                        $selectedValue = 1;
                    } else {
                        $selectedValue = 0;
                    }
                } else {
                    $selectedValue = $object->$name;
                }

                $selected = $selectedValue == 1 ? ' checked="checked" ' : '';
                return '<input ng-init="abstract_checked_' . $name . '=' . ($selectedValue == 1 ? 'true' : 'false') . '" ng-model="abstract_checked_' . $name . '" type="checkbox" name="AbstractInput_' . $name . '" value="1" ' . $selected . ' />';
                break;

            case 'imgfile':
                return '<input type="file" name="AbstractInput_' . $name . '"/>';
                break;

            case 'file':
                $fields = $object->getFields();
                $img = $object->{$attr['frontend']};
                if ($img) {
                    return '<input type="file" name="AbstractInput_' . $name . '"/><br/>' . $img . '<br/><input type="checkbox" name="AbstractInput_' . $name . '_delete" value="1" /> Delete Image';
                } else {
                    return '<input type="file" name="AbstractInput_' . $name . '"/>';
                }
                break;

            case 'filebinary':
                $fields = $object->getFields();
                $img = $object->file_url;
                if ($img) {
                    return '<input type="file" name="AbstractInput_' . $name . '"/><br/><br/>' . $img . '<br/><br/><input type="checkbox" name="AbstractInput_' . $name . '_delete" value="1" /> Delete File';
                } else {
                    return '<input type="file" name="AbstractInput_' . $name . '"/>';
                }
                break;

            case 'combobox':

                $onchange = isset($attr['on_change']) ? $attr['on_change'] : '';
                $return = '<select class="form-control" name="AbstractInput_' . $name . '"' . $onchange . '>';

                if (!isset($attr['hide_optional']) || $attr['hide_optional'] == false) {
                    $return .= '<option value="0">Choose option</option>';
                }

                $items = call_user_func($attr['source'], $attr['params_call']);

                if (isset($attr['main_attr']) && !empty($attr['main_attr'])) {
                    if (isset($object->{$attr['main_attr']}[$name])) {
                        $value = $object->{$attr['main_attr']}[$name];
                    } else {
                        $value = '';
                    }
                } else {
                    $value = $object->$name;
                }

                foreach ($items as $item) {
                    $selected = $value == $item->id ? 'selected="selected"' : '';
                    $nameAttr = isset($attr['name_attr']) ? $item->{$attr['name_attr']} : ((string)$item);

                    $return .= '<option value="' . $item->id . '" ' . $selected . '>' . ((string)$nameAttr) . '</option>';
                }
                $return .= "</select>";

                if (isset($attr['div_id'])) {
                    $return = '<div id="' . $attr['div_id'] . '">' . $return . '</div>';
                }

                return $return;
                break;

            case 'combobox_multi':
                $return = '<select class="form-control" name="AbstractInput_' . $name . '[]" multiple size="5">';
                $items = call_user_func($attr['source'], $attr['params_call']);
                foreach ($items as $item) {
                    $selected = in_array($item->id, $object->$name) ? 'selected="selected"' : '';
                    $nameAttr = isset($attr['name_attr']) ? $item->{$attr['name_attr']} : ((string)$item);
                    $return .= '<option value="' . $item->id . '" ' . $selected . '>' . ((string)$nameAttr) . '</option>';
                }
                $return .= "</select>";
                return $return;
                break;

            case 'checkbox_multi':

                $return = '';
                $items = call_user_func($attr['source'], $attr['params_call']);
                foreach ($items as $item) {
                    $selected = in_array($item->id, $object->$name) ? 'checked="checked"' : '';
                    $nameAttr = isset($attr['name_attr']) ? $item->{$attr['name_attr']} : ((string)$item);
                    $return .= '<div class="col-' . $attr['col_size'] . '"><label><input type="checkbox" name="AbstractInput_' . $name . '[]" ' . $selected . ' value="' . $item->id . '">' . htmlspecialchars($nameAttr) . '</label></div>';

                    /*$nameAttr = isset($attr['name_attr']) ? $item->{$attr['name_attr']} : ((string)$item);
                    $return .= '<option value="'.$item->id.'" '.$selected.'>'.((string)$nameAttr).'</option>';*/
                }
                $return .= "</select>";
                return $return;
                break;

            case 'title':
                return '<h3>' . $attr['trans'] . '</h3>';
                break;
                
            case 'text_display':
                return '<p>' . htmlspecialchars($object->$name) . '</p>';
                break;

            case 'text_pre':
                return '<pre>' . htmlspecialchars($object->$name) . '</pre>';
                break;

            default:
                break;
        }
    }

    public static function validateInput(& $object)
    {
        $definition = array();
        $fields = $object->getFields();
        foreach ($fields as $key => $field) {

            if (isset($field['multilanguage']) && $field['multilanguage'] == true) {
                foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_locales') as $locale) {
                    $definition['AbstractInput_' . $key . '_' . $locale] = $field['validation_definition'];
                }
            } elseif (isset($field['validation_definition'])) {

                $definition['AbstractInput_' . $key] = $field['validation_definition'];

                if (isset($field['translatable']) && $field['translatable'] == true) {
                    $definition['AbstractInput_' . $key . '_languages'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY);
                    $definition['AbstractInput_' . $key . '_content'] = new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY);
                }
            }
        }

        $form = new ezcInputForm(INPUT_POST, $definition);
        $Errors = array();

        foreach ($fields as $key => $field) {
            if ($field['type'] == 'checkbox') {

                if (isset($field['main_attr']) && !empty($field['main_attr'])) {
                    $botConfiguration = $object->{$field['main_attr']};
                    $botConfiguration[$key] = ($form->hasValidData('AbstractInput_' . $key) && $form->{'AbstractInput_' . $key}) ? 1 : 0;
                    $object->{$field['main_attr']} = $botConfiguration;
                } else {
                    if ($form->hasValidData('AbstractInput_' . $key) && $form->{'AbstractInput_' . $key} == 1) {
                        $object->$key = 1;
                    } else {
                        $object->$key = 0;
                    }
                }

            } elseif ($field['type'] == 'location') {

                if ($form->hasValidData('AbstractInput_' . $key)) {
                    $object->$key = $form->{'AbstractInput_' . $key};
                    $object->lat = $_POST['AbstractInput_' . $key . '_lat'];
                    $object->lon = $_POST['AbstractInput_' . $key . '_lon'];
                }

            } elseif ($field['type'] == 'combobox_multi' || $field['type'] == 'checkbox_multi') {

                if ($form->hasValidData('AbstractInput_' . $key)) {
                    $object->$key = $form->{'AbstractInput_' . $key};
                } else {
                    $object->$key = array();
                }

                if (isset($field['required']) && $field['required'] === true && empty($object->$key)) {
                    $Errors[$key] = $field['trans'] . ' is required';
                }

            } elseif ($field['type'] == 'file' || $field['type'] == 'filebinary') {
                if (erLhcoreClassSearchHandler::isFile('AbstractInput_' . $key)) {
                    if (isset($field['backend_call_param'])) {
                        $object->{$field['backend_call']}($field['backend_call_param']);
                    } else {
                        $object->{$field['backend_call']}();
                    }
                }

                if (isset($_POST['AbstractInput_' . $key . '_delete']) && $_POST['AbstractInput_' . $key . '_delete'] == 1) {

                    if (isset($field['delete_call_param'])) {
                        $object->{$field['delete_call']}($field['delete_call_param']);
                    } else {
                        $object->{$field['delete_call']}();
                    }
                }

            } elseif ($field['type'] == 'imgfile') {
                if (erLhcoreClassSearchHandler::isFile('AbstractInput_' . $key)) {
                    $object->{$field['backend_call']}();
                }

            } elseif ($field['type'] == 'textarea' || $field['type'] == 'number') {

                if (isset($field['multilanguage']) && $field['multilanguage'] == true) {
                    foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_locales') as $locale) {
                        $object->{$key . '_' . strtolower($locale)} = $form->{'AbstractInput_' . $key . '_' . $locale};
                    }
                } else {

                    if ($form->hasValidData('AbstractInput_' . $key)) {

                        if (isset($field['main_attr']) && !empty($field['main_attr'])) {
                            $mainAttr = 'main_attr';
                        } else if (isset($field['main_attr_lang']) && !empty($field['main_attr_lang'])) {
                            $mainAttr = 'main_attr_lang';
                        } else {
                            $mainAttr = null;
                        }

                        if ($mainAttr !== null) {
                            $botConfiguration = $object->{$field[$mainAttr]};

                            if ($mainAttr == 'main_attr') {
                                $botConfiguration[$key] = $form->{'AbstractInput_' . $key};
                                $object->{$field[$mainAttr]} = $botConfiguration;
                            } else {
                                $object->{$key} = $form->{'AbstractInput_' . $key};
                            }

                            // Multi language support
                            if (isset($field['translatable']) && $field['translatable'] == true) {
                                $languagesData = array();

                                if ( $form->hasValidData( 'AbstractInput_' . $key . '_languages' ) && !empty($form->{'AbstractInput_' . $key . '_languages'}) )
                                {
                                    foreach ($form->{'AbstractInput_' . $key . '_languages'} as $index => $languages) {
                                        $languagesData[] = array(
                                            'content' => $form->{'AbstractInput_' . $key . '_content'}[$index],
                                            'languages' => $form->{'AbstractInput_' . $key . '_languages'}[$index],
                                        );
                                    }
                                    $botConfiguration[$key . '_lang'] = $languagesData;
                                    $object->{$field[$mainAttr]} = $botConfiguration;
                                } elseif (isset($botConfiguration[$key . '_lang'])) {
                                    unset($botConfiguration[$key . '_lang']);
                                    $object->{$field[$mainAttr]} = $botConfiguration;
                                }
                            }

                        } else {
                            $object->$key = $form->{'AbstractInput_' . $key};
                        }
                    }
                }

            } elseif ($field['type'] == 'text' && isset($field['multilanguage']) && $field['multilanguage'] == true) {

                foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_locales') as $locale) {
                    $object->{$key . '_' . strtolower($locale)} = $form->{'AbstractInput_' . $key . '_' . $locale};
                }

            } elseif ($field['type'] == 'colorpicker') {

                if (isset($field['main_attr']) && !empty($field['main_attr'])) {
                    $botConfiguration = $object->{$field['main_attr']};
                    $botConfiguration[$key] = $form->{'AbstractInput_' . $key};
                    $object->{$field['main_attr']} = $botConfiguration;
                } else {
                    $object->$key = $form->{'AbstractInput_' . $key};
                }

            } elseif ($form->hasValidData('AbstractInput_' . $key) && (($field['required'] == false) || ($field['type'] == 'combobox') || ($field['required'] == true && ($field['type'] == 'text' || $field['type'] == 'number') && $form->{'AbstractInput_' . $key} != ''))) {

                if (isset($field['multilanguage']) && $field['multilanguage'] == true) {

                    $partsTranslated = array();
                    foreach (erConfigClassLhConfig::getInstance()->getSetting('site', 'available_locales') as $locale) {
                        $partsTranslated[$locale] = $form->{'AbstractInput_' . $key . '_' . $locale};
                    }

                    $object->$key = serialize($partsTranslated);

                } else {

                    if (isset($field['main_attr']) && !empty($field['main_attr'])) {
                        $mainAttr = 'main_attr';
                    } else if (isset($field['main_attr_lang']) && !empty($field['main_attr_lang'])) {
                        $mainAttr = 'main_attr_lang';
                    } else {
                        $mainAttr = null;
                    }

                    if ($mainAttr !== null) {
                        $botConfiguration = $object->{$field[$mainAttr]};

                        if ($mainAttr == 'main_attr') {
                            $botConfiguration[$key] = $form->{'AbstractInput_' . $key};
                            $object->{$field[$mainAttr]} = $botConfiguration;
                        } else {
                            $object->{$key} = $form->{'AbstractInput_' . $key};
                        }

                        // Multi language support
                        if (isset($field['translatable']) && $field['translatable'] == true) {
                            $languagesData = array();

                            if ( $form->hasValidData( 'AbstractInput_' . $key . '_languages' ) && !empty($form->{'AbstractInput_' . $key . '_languages'}) )
                            {
                                foreach ($form->{'AbstractInput_' . $key . '_languages'} as $index => $languages) {
                                    $languagesData[] = array(
                                        'content' => $form->{'AbstractInput_' . $key . '_content'}[$index],
                                        'languages' => $form->{'AbstractInput_' . $key . '_languages'}[$index],
                                    );
                                }
                                $botConfiguration[$key . '_lang'] = $languagesData;
                                $object->{$field[$mainAttr]} = $botConfiguration;
                            } elseif (isset($botConfiguration[$key . '_lang'])) {
                                unset($botConfiguration[$key . '_lang']);
                                $object->{$field[$mainAttr]} = $botConfiguration;
                            }
                        }

                    } else {
                        $object->$key = $form->{'AbstractInput_' . $key};
                    }
                }

            } elseif (isset($field['required']) && $field['required'] == true) {
                $Errors[$key] = $field['trans'] . ' is required';
            }
        }

        if (method_exists($object, 'validateInput')) {
            $object->validateInput(array(
                'errors' => & $Errors
            ));
        }

        return $Errors;
    }

    public static function getSession()
    {
        if (!isset(self::$persistentSession)) {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager('./pos/lhabstract')
            );
        }
        return self::$persistentSession;
    }

    private static $persistentSession;
    private static $instance = null;
}

?>