<?php if ($context == 'dropdownoptions') : ?>
    <ul>
        <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'If you are passing department make sure. This is highly recommended as chat might not be able to start if you have custom required fields by department.');?>
            <ul>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'You pass it for all options');?></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'All options have to be assigned to same start chat configuration');?></li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'You have chosen that department in department options.');?> <span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Apply this configuration also to these departments');?></span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'or');?> <span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Department');?></span></li>
            </ul>
        </li>
    </ul>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'JSON based options.');?></h5>
<textarea class="form-control form-control-sm mb-3" rows="3"><?php echo '{"name":"Please choose","value":""}
{"name":"Default","dep_id":20,"value":"def","subject_id":"8"}
{"name":"Other","dep_id":18,"value":"other"}';?></textarea>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Regular syntax. Depreciated.');?></h5>
    <ul>
        <li>Option name || &lt;department_id OR department_alias&gt;</li>
        <li>Internal Value => Option name || &lt;department_id OR department_alias&gt;</li>
    </ul>
<?php endif; ?>