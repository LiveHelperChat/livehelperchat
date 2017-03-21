<?php

class erLhcoreClassRenderHelper {

    public static function renderCombobox($params = array())
    {
        $onchange = (isset($params['on_change']) && $params['on_change'] != '') ? ' onchange="'.$params['on_change'].'" ' : '';

        $output = '';

        if (isset($params['optional_field'])){
           $defaultValue = isset($params['default_value']) ? $params['default_value'] : 0;
           $output .= "<option value=\"{$defaultValue}\">{$params['optional_field']}</option>";
        }

        $attrId = isset($params['attr_id']) ? $params['attr_id'] : 'id';

        if (isset($params['multi_call']) && $params['multi_call'] == true) {
            $items = call_user_func_array($params['list_function'],isset($params['list_function_params']) ? $params['list_function_params'] : array());
        }else {
            $items = call_user_func($params['list_function'],isset($params['list_function_params']) ? $params['list_function_params'] : array());
        }

        $nameSelect = isset($params['display_name']) ? $params['display_name'] : 'name';

        foreach ($items as $item)
        {
            $selected = ( (isset($params['is_editing']) && $params['is_editing'] == false && $item->{$params['use_default']} == 1 && (!is_array($params['selected_id']) && ($params['selected_id'] === null || $params['selected_id'] === '') )) || (is_array($params['selected_id']) && in_array($item->$attrId,$params['selected_id'])) || $params['selected_id'] == $item->$attrId) ? 'selected="selected"' : '';
            $valueItem = $item->$nameSelect;

            if (isset($params['number_format'])) {
                $valueItem = number_format($valueItem,0,'.',', ');
            }

            if (isset($params['append_option_value'])) {
                $valueItem = $params['append_option_value'].$valueItem;
            }

            if (isset($params['prepend_option_value'])) {
                $valueItem = $valueItem.$params['prepend_option_value'];
            }

            $output .= "<option value=\"{$item->$attrId}\" $selected >{$valueItem}</option>";
        }

        $disbled = '';
        if ((isset($params['disable_on_empty']) && count($items) == 0) || (isset($params['disabled_edit']) && $params['disabled_edit'] == true) ) {
            $disbled = ' disabled="disabled" ';
        }



        $classItems = array();
        $classItems[] = isset($params['is_error']) && $params['is_error'] == true ? 'error-inp' : null;
        $classItems[] = isset($params['css_class']) ? $params['css_class'] : null;

        $classItems = array_filter($classItems);

        $class = count($classItems) > 0 ? ' class="'.implode(' ',$classItems).'" ' : '';
        $title = isset($params['title_element']) ? ' title="'.$params['title_element'].'" ' : null;

        $ismultiple = isset($params['multiple']) ? 'multiple' : '';
        $ngmodel = isset($params['ng-model']) ? ' ng-model="'.$params['ng-model'].'" ' : '';

        $output = '<select '.$ismultiple.' id="id_'.$params['input_name'].'" name="'.$params['input_name'].'"'.$ngmodel.$onchange.$disbled.$class.$title.'>' . $output;

        if (isset($params['append_value'])) {
            $selected = $params['selected_id'] == $params['append_value'][0] ? 'selected="selected"' : '';
            $output .= "<option value=\"{$params['append_value'][0]}\" $selected >{$params['append_value'][1]}</option>";
        }

        $output .= '</select>';

        return $output;
    }

    public static function renderCheckbox($params = array())
    {
        $output  = '';

        foreach (call_user_func($params['list_function'],isset($params['list_function_params']) ? $params['list_function_params'] : array()) as $item)
        {
            $checked = in_array($item->id,$params['selected_id']) ? 'checked="checked"' : '';
            $output .= "<label><input type=\"checkbox\" name=\"{$params['input_name']}\" value=\"{$item->id}\" {$checked} />".htmlspecialchars($item->name)."</label>";
        }

        return $output;
    }

    public static function renderCheckboxColums($params = array())
    {
        $output  = '';

        $output  .= '<table width="100%">';

        $count = 0;

        foreach (call_user_func($params['list_function'],isset($params['list_function_params']) ? $params['list_function_params'] : array()) as $item)
        {
            $checked = in_array($item->id,$params['selected_id']) ? 'checked="checked"' : '';

            $output .= '<td>';

            $output .= "<label><input type=\"checkbox\" name=\"{$params['input_name']}\" value=\"{$item->id}\" {$checked} />".htmlspecialchars($item->name)."</label>";

            $output .= '</td>';



            $count++;

             if ($count == 5) {
            	$output .= '</tr><tr>';
            	$count = 0;
            }

       }

        $output  .= '</td></tr></table>';
        return $output;
    }

    public static function renderRangeCombobox($params)
    {
        $returnArray = array();
        $paramsRender = $params;

        $paramsRender['selected_id'] = $params['selected_from'];
        $paramsRender['input_name'] = $params['input_name'].'_from';

        if (isset($params['optional_from'])){
            $paramsRender['optional_field'] = $params['optional_from'];
        }
        $returnArray[0] = self::renderCombobox($paramsRender);

        $paramsRender['selected_id'] = $params['selected_to'];
        $paramsRender['input_name'] = $params['input_name'].'_to';
        if (isset($params['optional_to'])){
            $paramsRender['optional_field'] = $params['optional_to'];
        }
        $returnArray[1] = self::renderCombobox($paramsRender);

        return $returnArray;
    }

    public static function renderArray($params)
    {
    	$items = call_user_func($params['list_function'],isset($params['list_function_params']) ? $params['list_function_params'] : array());
    	$array = array();

    	foreach ($items as $item){

    		$itemsElement = array();
    		foreach ($params['elements_items'] as $identifier => $value){
    			$itemsElement[$identifier] = $item->{$value};
    		}

    		$array[$item->{$params['identifier']}] = $itemsElement;
    	}

    	return $array;
    }



}