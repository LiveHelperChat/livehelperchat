<?php if (is_array($department_array) && !empty($department_array)) : ?>
    <?php $jsVars = array(); foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','var_identifier','type'), 'customfilter' => array('dep_id = 0 OR dep_id IN (' . implode(',',$department_array) .')'))) as $jsVar) { $jsVars[] = array('id' => $jsVar->id,'var' => $jsVar->js_variable); } ?>
<?php else : ?>
    <?php $jsVars = array(); foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','var_identifier','type'), 'filter' => array('dep_id' => 0))) as $jsVar) { $jsVars[] = array('id' => $jsVar->id, 'var' => $jsVar->js_variable); } ?>
<?php endif; ?>