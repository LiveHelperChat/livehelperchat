<?php

class erLhcoreClassSearchHandler
{

    public static function getParams($params = array())
    {
        $uparams = isset($params['uparams']) ? $params['uparams'] : array();
        
        if (isset($params['customfilterfile'])) {
            $fieldsObjects = include ($params['customfilterfile']);
        } else {
            $fieldsObjects = include ('lib/core/lh' . $params['module'] . '/searchattr/' . $params['module_file'] . '.php');
        }
        
        $fields = $fieldsObjects['filterAttributes'];
        $orderOptions = $fieldsObjects['sortAttributes'];
        
        foreach ($fields as $key => $field) {
            $definition[$key] = $field['validation_definition'];
        }
        
        foreach ($uparams as $key => &$value) {
            if (! is_array($value))
                $value = urldecode($value);
        }
        
        $inputParams = new stdClass();
        $inputFrom = new stdClass();
        
        $form = new erLhcoreClassInputForm(INPUT_GET, $definition, null, $uparams, isset($params['use_override']) ? $params['use_override'] : false);
        $Errors = array();
        
        foreach ($fields as $key => $field) {
            
            $inputParams->$key = null;
            $inputFrom->$key = null;
            
            if ($form->hasValidData($key) && (($field['required'] == false && $field['valid_if_filled'] == false) || ($field['type'] == 'combobox') || ($field['required'] == true && $field['type'] == 'text' && $form->{$key} != ''))) {
                
                if (isset($field['datatype']) && $field['datatype'] == 'date_ymd') {
                    
                    $dateTemp = $form->{$key};
                    
                    if (self::isValidDateFormat($dateTemp) == false) {
                        continue;
                    }
                }
                
                $inputParams->$key = $form->{$key};
                $inputFrom->$key = $form->{$key};
                
                if (isset($field['depend_fields'])) {
                    foreach ($field['depend_fields'] as $depend) {
                        if (! $form->hasValidData($depend) && ! key_exists($depend, $Errors)) {
                            $Errors[$depend] = $fields[$depend]['trans'] . ' is required';
                        }
                    }
                }
            } elseif ($field['required'] == true) {
                $Errors[$key] = $field['trans'] . ' is required';
            } elseif (isset($field['valid_if_filled']) && $field['valid_if_filled'] == true && $form->hasValidData($key) && $form->{$key} != '') {
                $inputFrom->$key = $form->{$key};
                $inputParams->$key = $form->{$key};
                
                if (isset($field['depend_fields'])) {
                    foreach ($field['depend_fields'] as $depend) {
                        
                        if (! $form->hasValidData($depend) && ! key_exists($depend, $Errors)) {
                            $Errors[$depend] = $fields[$depend]['trans'] . ' is required';
                        }
                    }
                }
            } elseif (isset($field['valid_if_filled']) && $field['valid_if_filled'] == true && isset($_GET[$key]) && $_GET[$key] != '') {
                $Errors[$key] = $field['trans'] . ' is filled incorrectly!';
                $inputFrom->$key = $_GET[$key];
            } elseif (isset($field['depend_fields'])) { // No value, we can clean dependence fields
                
                foreach ($field['depend_fields'] as $depend) {
                    $inputFrom->$depend = null;
                    $inputParams->$depend = null;
                }
            }
        }
        
        $filter = array();
        
        if (isset($params['format_filter']) && count($Errors) == 0) {
            
            foreach ($fields as $key => $field) {
                
                if (($field['filter_type'] !== false && $inputParams->$key != '') || $inputParams->$key === 0) {
                    
                    if ($field['filter_type'] == 'filter') {
                        
                        if (is_bool($inputParams->$key) && $inputParams->$key == true) {
                            $filter[$field['filter_type']][$field['filter_table_field']] = 1;
                        } else {
                            $filter[$field['filter_type']][$field['filter_table_field']] = $inputParams->$key;
                        }
                    } elseif ($field['filter_type'] == 'filterin_remote') {
                        
                        $args = array();
                        foreach ($field['filter_in_args'] as $fieldInput) {
                            $args[] = $inputParams->$fieldInput;
                        }
                        $filter['filterin'][$key] = call_user_func_array($field['filter_in_generator'], $args);
                        
                        if (count($filter['filterin'][$key]) == 0) {
                            $filter['filterin'][$key] = array(
                                - 1
                            );
                        }
                        
                        if (isset($field['depend_fields'])) {
                            foreach ($field['depend_fields'] as $depend) {
                                if ($inputFrom->$depend == - 1) {
                                    unset($filter['filterin'][$key]);
                                }
                            }
                        }
                    } elseif ($field['filter_type'] == 'filtergte' || $field['filter_type'] == 'filtergt') {

                        $filterType = $field['filter_type'];

                        if (isset($field['datatype']) && $field['datatype'] == 'date') {
                            
                            $dateFormated = self::formatDateToTimestamp($inputParams->$key);
                            if ($dateFormated != false) {
                                $filter[$filterType][$field['filter_table_field']] = $dateFormated;
                            }
                        } elseif (isset($field['datatype']) && $field['datatype'] == 'date_ymd') {
                            
                            $dateFormated = self::formatDateToDateYmd($inputParams->$key);
                            
                            if ($dateFormated != false) {
                                $filter[$filterType][$field['filter_table_field']] = $dateFormated;
                            }
                        } elseif (isset($field['datatype']) && $field['datatype'] == 'datetime') {
                            
                            $dateFormated = self::formatDateToTimestamp($inputParams->$key);
                            
                            if ($dateFormated != false) {
                                                                                                
                                if (isset($_GET[$key.'_hours'])){
                                    $hours = isset($_GET[$key.'_hours']) && is_numeric($_GET[$key.'_hours']) ? (int)$_GET[$key.'_hours']*3600 : 0;
                                    $inputFrom->{$key.'_hours'} = (isset($_GET[$key.'_hours']) && is_numeric($_GET[$key.'_hours'])) ? (int)$_GET[$key.'_hours'] : null;
                                } elseif (isset($uparams[$key.'_hours']) && is_numeric($uparams[$key.'_hours'])) {
                                    $hours = $uparams[$key.'_hours'] * 3600;
                                    $inputFrom->{$key.'_hours'} = (int)$uparams[$key.'_hours'];
                                } else {
                                    $inputFrom->{$key.'_hours'} = null;
                                    $hours = 0;
                                }
                                
                                if (isset($_GET[$key.'_minutes'])){
                                    $minutes = isset($_GET[$key.'_minutes']) && is_numeric($_GET[$key.'_minutes']) ? (int)$_GET[$key.'_minutes']*60 : 0;
                                    $inputFrom->{$key.'_minutes'} = (isset($_GET[$key.'_minutes']) && is_numeric($_GET[$key.'_minutes'])) ? (int)$_GET[$key.'_minutes'] : null;
                                } elseif (isset($uparams[$key.'_minutes']) && is_numeric($uparams[$key.'_minutes'])) {
                                    $minutes = $uparams[$key.'_minutes'] * 60;
                                    $inputFrom->{$key.'_minutes'} = (int)$uparams[$key.'_minutes'];
                                } else {
                                    $inputFrom->{$key.'_minutes'} = null;
                                    $minutes = 0;
                                }
                                 
                                $filter[$filterType][$field['filter_table_field']] = $dateFormated + $hours + $minutes;
                            }
                            
                        } else {
                            $filter[$filterType][$field['filter_table_field']] = $inputParams->$key;
                        }

                    } elseif ($field['filter_type'] == 'filterlte' || $field['filter_type'] == 'filterlt') {

                        $filterType = $field['filter_type'];

                        if (isset($field['range_from']) && isset($filter['filtergte'][$fields[$field['range_from']]['filter_table_field']]) && $filter['filtergte'][$fields[$field['range_from']]['filter_table_field']] == $inputParams->$key) {
                            unset($filter['filtergte'][$fields[$field['range_from']]['filter_table_field']]);
                            $filter['filter'][$field['filter_table_field']] = $inputParams->$key;
                        } else {
                            
                            if (isset($field['datatype']) && $field['datatype'] == 'date') {
                                
                                $dateFormated = self::formatDateToTimestamp($inputParams->$key);
                                if ($dateFormated != false) {
                                    $filter[$filterType][$field['filter_table_field']] = $dateFormated;
                                }
                            } elseif (isset($field['datatype']) && $field['datatype'] == 'date_ymd') {
                                
                                $dateFormated = self::formatDateToDateYmd($inputParams->$key);
                                if ($dateFormated != false) {
                                    $filter[$filterType][$field['filter_table_field']] = $dateFormated;
                                }
                            } elseif (isset($field['datatype']) && $field['datatype'] == 'datetime') {
                                
                                $dateFormated = self::formatDateToTimestamp($inputParams->$key);
                                                                                                
                                if ($dateFormated != false) {
                                                                        
                                    if (isset($_GET[$key.'_hours'])){
                                        $hours = isset($_GET[$key.'_hours']) && is_numeric($_GET[$key.'_hours']) ? (int)$_GET[$key.'_hours']*3600 : 0;
                                        $inputFrom->{$key.'_hours'} = (isset($_GET[$key.'_hours']) && is_numeric($_GET[$key.'_hours'])) ? (int)$_GET[$key.'_hours'] : null;
                                    } elseif (isset($uparams[$key.'_hours']) && is_numeric($uparams[$key.'_hours'])) {
                                        $hours = $uparams[$key.'_hours'] * 3600;
                                        $inputFrom->{$key.'_hours'} = (int)$uparams[$key.'_hours'];
                                    } else {
                                        $inputFrom->{$key.'_hours'} = null;
                                        $hours = 0;
                                    }
                                                                        
                                    if (isset($_GET[$key.'_minutes'])){
                                        $minutes = isset($_GET[$key.'_minutes']) && is_numeric($_GET[$key.'_minutes']) ? (int)$_GET[$key.'_minutes']*60 : 0;
                                        $inputFrom->{$key.'_minutes'} = (isset($_GET[$key.'_minutes']) && is_numeric($_GET[$key.'_minutes'])) ? (int)$_GET[$key.'_minutes'] : null;
                                    } elseif (isset($uparams[$key.'_minutes']) && is_numeric($uparams[$key.'_minutes'])) {
                                        $minutes = $uparams[$key.'_minutes'] * 60;
                                        $inputFrom->{$key.'_minutes'} = (int)$uparams[$key.'_minutes'];
                                    } else {
                                        $inputFrom->{$key.'_minutes'} = null;
                                        $minutes = 0;
                                    }
                                    
                                    $filter[$filterType][$field['filter_table_field']] = $dateFormated+$hours+$minutes;
                                }
                            } else {
                                $filter[$filterType][$field['filter_table_field']] = $inputParams->$key;
                            }
                        }

                    } elseif ($field['filter_type'] == 'filter_join') {
                        $filter['filterin'][$field['filter_table_field']] = $inputParams->$key;
                        $filter['filter_join'][$field['join_table_name']] = $field['join_attributes'];
                        $filter['filter_having'][] = 'COUNT(*) = ' . count($inputParams->$key);
                        $filter['filter_group'][] = $field['group_by_field'];
                    } elseif ($field['filter_type'] == 'filter_map') {
                        
                        $mapObject = call_user_func($field['class'] . '::fetch', $inputParams->$key);
                        $filter['filter'][$mapObject->field] = $mapObject->status;
                    } elseif ($field['filter_type'] == 'like') {
                        $filter['filterlike'][$field['filter_table_field']] = $inputParams->$key;
                    } elseif ($field['filter_type'] == 'filterkeyword') {
                        
                        if (isset($field['filter_transform_to_search']) && $field['filter_transform_to_search'] == true) {
                            $filter['filterkeyword'][$field['filter_table_field']] = erLhcoreClassCharTransform::transformToSearch($inputParams->$key);
                        } else {
                            $filter['filterkeyword'][$field['filter_table_field']] = $inputParams->$key;
                        }
                    } elseif ($field['filter_type'] == 'filterin') {
                        if (!empty($inputParams->$key)) {
                            $filter['filterin'][$field['filter_table_field']] = $inputParams->$key;
                        }
                    } elseif ($field['filter_type'] == 'filter_work_types') {
                        
                        $filter['filter_keywords'][] = 'work_type_' . $inputParams->$key;
                    } elseif ($field['filter_type'] == 'filter_topic_types') {
                        
                        $filter['filter_keywords'][] = 'topic_type_' . $inputParams->$key;
                    } elseif ($field['filter_type'] == 'filterbetween') {
                        
                        $parts = explode('_', $inputParams->$key);
                        
                        if (is_numeric($parts[0])) {
                            $filter['filtergte'][$field['filter_table_field']] = (int) $parts[0];
                        }
                        
                        if (is_numeric($parts[1])) {
                            $filter['filterlte'][$field['filter_table_field']] = (int) $parts[1];
                        }
                    } elseif ($field['filter_type'] == 'filter_languages') {
                        
                        $filter['filter_keywords'][] = 'language_' . $inputParams->$key;
                    } elseif ($field['filter_type'] == 'user_expert_type') {
                        
                        if ($inputParams->$key == erLhcoreClassModelUser::USER_EXPERT_TYPE_BOTH) {
                            $filter['filter']['is_worker'] = 1;
                            $filter['filter']['is_employer'] = 1;
                        } elseif ($inputParams->$key == erLhcoreClassModelUser::USER_EXPERT_TYPE_WORKER) {
                            $filter['filter']['is_worker'] = 1;
                            $filter['filter']['is_employer'] = 0;
                        } elseif ($inputParams->$key == erLhcoreClassModelUser::USER_EXPERT_TYPE_EMPLOYER) {
                            $filter['filter']['is_employer'] = 1;
                            $filter['filter']['is_worker'] = 0;
                        }
                    } elseif ($field['filter_type'] == 'user_status') {
                        
                        if ($inputParams->$key == 1) {
                            $filter['filter'][$field['filter_table_field']] = 1;
                        } elseif ($inputParams->$key == 2) {
                            $filter['filter'][$field['filter_table_field']] = 0;
                        }
                    } elseif ($field['filter_type'] == 'filterkeyword_blogpost') {
                        
                        $locale = strtolower(erLhcoreClassSystem::instance()->Language);
                        
                        $tableFields = explode(',', $field['filter_table_fields']);
                        
                        foreach ($tableFields as $tableField) {
                            $filter['filterseqor'][$tableField . '_' . $locale] = $inputParams->$key;
                        }
                    } elseif ($field['filter_type'] == 'filtertag_blogpost') {
                        
                        $locale = strtolower(erLhcoreClassSystem::instance()->Language);
                        
                        $tableField = $field['filter_table_field'] . '_' . $locale;
                        
                        $filter['filterseq'][$tableField] = $inputParams->$key;
                    } elseif ($field['filter_type'] == 'filterseq') {
                        
                        $filter[$field['filter_type']][$field['filter_table_field']] = $inputParams->$key;
                    }
                }
            }
            
            if (isset($currentOrder['as_append'])) {
                foreach ($currentOrder['as_append'] as $key => $appendSelect) {
                    
                    if (isset($currentOrder['replace_params'])) {
                        
                        $returnObj = call_user_func($currentOrder['param_call_func'], $inputParams->{$currentOrder['param_call_name_attr']});
                        
                        foreach ($currentOrder['replace_params'] as $attrObj => $targetString) {
                            $appendSelect = str_replace($targetString, $returnObj->$attrObj, $appendSelect);
                        }
                    }
                    
                    $filter['as_append'] = $appendSelect . ' AS ' . $key;
                }
            }
            
            if (! isset($orderOptions['disabled'])) {
                $keySort = key_exists($inputParams->{$orderOptions['field']}, $orderOptions['options']) ? $inputParams->{$orderOptions['field']} : $orderOptions['default'];
                $currentOrder = $orderOptions['options'][$keySort];
                $filter['sort'] = $currentOrder['sort_column'];
                $inputFrom->sortby = $keySort;
                
                if (key_exists($inputParams->{$orderOptions['field']}, $orderOptions['options']) && $orderOptions['default'] != $inputParams->{$orderOptions['field']}) {
                    $inputParams->sortby = $keySort;
                } else {
                    // Standard sort mode does not need any append in URL
                    if (isset($inputParams->sortby)) {
                        unset($inputParams->sortby);
                    }
                }
            }
        }
        
        return array(
            'errors' => $Errors,
            'input_form' => $inputFrom,
            'input' => $inputParams,
            'filter' => $filter
        );
    }

    public static function getURLAppendFromInput($inputParams, $skipSort = false)
    {
        $URLappend = '';
        $sortByAppend = '';
        
        foreach ($inputParams as $key => $value) {
            if (is_numeric($value) || $value != '') {
                $value = is_array($value) ? implode('/', $value) : urlencode($value);
                if ($key != 'sortby') {
                    $URLappend .= "/({$key})/" . $value;
                } else {
                    $sortByAppend = "/({$key})/" . $value;
                }
            }
        }
        
        if ($skipSort == false) {
            return $URLappend . $sortByAppend;
        } else {
            return $URLappend;
        }
    }

    public static function formatDateToTimestamp($date)
    {
        $dateFormat = DateTime::createFromFormat('Y-m-d', $date);
        
        if ($dateFormat != false) {
            $return = intval(self::formatTimeToYearMontDate($dateFormat->getTimestamp()));
        } else {
            $return = false;
        }
        
        return $return;
    }

    public static function formatTimeToYearMontDate($timestamp)
    {
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        return mktime(0, 0, 0, $month, $day, $year);
    }

    public static function formatDateToDateYmd($date)
    {
        $dateFormat = DateTime::createFromFormat('Y-m-d', $date);
        
        if ($dateFormat != false) {
            $return = intval(date("Ymd", $dateFormat->getTimestamp()));
        } else {
            $return = false;
        }
        
        return $return;
    }

    public static function isValidDateFormat($date)
    {
        if (DateTime::createFromFormat('Y-m-d', $date) != false) {
            $return = true;
        } else {
            $return = false;
        }
        
        return $return;
    }

    public static $lastError = null;

    public static function isFile($fileName, $supportedExtensions = array ('zip','doc','docx','pdf','xls','gif','xlsx','jpg','jpeg','png','bmp','rar','7z'), $maxFileSize = false)
    {
        if (isset($_FILES[$fileName]) && is_uploaded_file($_FILES[$fileName]["tmp_name"]) && $_FILES[$fileName]["error"] == 0) {
            $fileNameAray = explode('.', $_FILES[$fileName]['name']);
            end($fileNameAray);
            $extension = strtolower(current($fileNameAray));
            
            if (is_array($supportedExtensions) && ! in_array($extension, $supportedExtensions)) {
                self::$lastError = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Invalid file extension');
                return false;
            }
            
            if (is_string($supportedExtensions)) {
                if (! preg_match($supportedExtensions, $_FILES[$fileName]['name'])) {
                    self::$lastError = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'Invalid file extension!');
                    return false;
                }
            }
            
            if ($maxFileSize !== false && $maxFileSize > 0 && $_FILES[$fileName]['size'] > $maxFileSize) {
                self::$lastError = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat', 'To big file!');
                return false;
            }
            
            return true;
        }
        
        return false;
    }

    public static function isImageFile($fileName)
    {
        $supportedExtensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif'
        );
        
        if (isset($_FILES[$fileName]) && is_uploaded_file($_FILES[$fileName]["tmp_name"]) && $_FILES[$fileName]["error"] == 0 && erLhcoreClassImageConverter::isPhoto($fileName)) {
            $fileNameAray = explode('.', $_FILES[$fileName]['name']);
            end($fileNameAray);
            $extension = strtolower(current($fileNameAray));
            return in_array($extension, $supportedExtensions);
        }
        
        return false;
    }

    public static function moveUploadedFile($fileName, $destination_dir, $extensionSeparator = '')
    {
        if (isset($_FILES[$fileName]) && is_uploaded_file($_FILES[$fileName]["tmp_name"]) && $_FILES[$fileName]["error"] == 0) {
            $fileNameAray = explode('.', $_FILES[$fileName]['name']);
            end($fileNameAray);
            $extension = current($fileNameAray);
            
            $fileNamePhysic = md5($_FILES[$fileName]['tmp_name'] . time()) . $extensionSeparator . strtolower($extension);
            
            move_uploaded_file($_FILES[$fileName]["tmp_name"], $destination_dir . $fileNamePhysic);
            chmod($destination_dir . $fileNamePhysic, 0644);
            
            return $fileNamePhysic;
        }
    }

    public static function moveLocalFile($fileName, $destination_dir, $extensionSeparator = '')
    {
        $fileNameAray = explode('.', $fileName);
        end($fileNameAray);
        $extension = current($fileNameAray);
        
        $fileNamePhysic = md5($fileName . time() . rand(0, 1000)) . $extensionSeparator . strtolower($extension);
        
        rename($fileName, $destination_dir . $fileNamePhysic);
        chmod($destination_dir . $fileNamePhysic, 0644);
        
        return $fileNamePhysic;
    }
}