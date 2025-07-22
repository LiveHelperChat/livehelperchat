<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Audit Configuration')?></h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','How many days keep log?')?></label>
        <input type="text" class="form-control" name="days_log" value="<?php isset($audit_options['days_log']) ? print $audit_options['days_log'] : print '90'?>" />
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="log_js" <?php if (isset($audit_options['log_js']) && $audit_options['log_js'] == true) : ?>checked<?php endif;?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Log javascript errors')?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="log_routing" <?php if (isset($audit_options['log_routing']) && $audit_options['log_routing'] == true) : ?>checked<?php endif;?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Log applied routing/chat priority rules')?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="log_block" <?php if (isset($audit_options['log_block']) && $audit_options['log_block'] == true) : ?>checked<?php endif;?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Log applied blocks')?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="log_user" <?php if (isset($audit_options['log_user']) && $audit_options['log_user'] == true) : ?>checked<?php endif;?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Log users changes')?></label>
    </div>

    <div class="form-group">
        <label><input type="checkbox" name="log_files" <?php if (isset($audit_options['log_files']) && $audit_options['log_files'] == true) : ?>checked<?php endif;?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','Log failed files uploads')?></label>
    </div>

    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('audit/options','What objects changes log?')?></h5>
    <?php $objectsLog = array(
        array('class' => 'AutoResponder' ,'name' => 'Auto Responder'),
        array('class' => 'CannedMsg' ,'name' => 'Canned Message'),
        array('class' => 'Subject' ,'name' => 'Subject'),
        array('class' => 'Departament' ,'name' => 'Department'),
        array('class' => 'ChatConfig' ,'name' => 'Chat configuration'),
        array('class' => 'erLhcoreClassModelRole' ,'name' => 'Role changes'),
        array('class' => 'erLhcoreClassModelGroup' ,'name' => 'Group changes'),
        array('class' => 'erLhcoreClassModelChatFileReveal' ,'name' => 'Chat file reveal'),
        array('class' => 'MailconvMessageFileReveal' ,'name' => 'Mail message file reveal'),
    );
    ?>

    <div class="row">
    <?php foreach ($objectsLog as $objectToLog) : ?>
        <div class="col-3"><label><input <?php if (isset($audit_options['log_objects']) && in_array($objectToLog['class'],$audit_options['log_objects'])) :?>checked="checked"<?php endif;?> type="checkbox" name="log_objects[]" value="<?php echo $objectToLog['class']?>"> <?php echo htmlspecialchars($objectToLog['name'])?></label></div>
    <?php endforeach; ?>
    </div>

    <input type="submit" class="btn btn-sm btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />&nbsp;<input type="submit" class="btn btn-secondary btn-sm" name="ReloadOperatorsBackOffice" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Reload back office for operators. Requires NodeJS'); ?>" />

</form>

<hr>

<h4>Time Zone</h4>

<ul>
<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','These hours will be using');?> <b><?php print date_default_timezone_get()?></b> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','time zone');?> <b>[<?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s') ?>]</b></li>
<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time in database server');?> <b>[<?php
        $db = ezcDbInstance::get();
        $stmt = $db->prepare("SELECT NOW()");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $data['now()'];
        ?>]</b></li>
<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Time zone used for the visitor will be');?> <b>[<?php
        if (erConfigClassLhConfig::getInstance()->getSetting('site','time_zone', false) != '') : ?>
            <?php echo erConfigClassLhConfig::getInstance()->getSetting('site','time_zone', false).' ' . (new DateTime('now', new DateTimeZone(erConfigClassLhConfig::getInstance()->getSetting('site','time_zone', false))))->format('Y-m-d H:i:s')?>
        <?php else : ?>
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit', 'Server default timezone.')?> <?php echo (new DateTime('now', new DateTimeZone(date_default_timezone_get())))->format('Y-m-d H:i:s')?>
            <span class="text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','If you have set your time zone in account. Make sure you set it in default settings file also.');?></span>
        <?php endif; ?>
        ]</b></li>
</ul>

<?php if (!class_exists('erLhcoreClassInstance') && erLhcoreClassUser::instance()->hasAccessTo('lhaudit','see_system')) : ?>

<hr>

<h4>Database variables</h4>

<?php
$db = ezcDbInstance::get();
$rows = [];
try {
    $stmt = $db->prepare('SHOW variables');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo '<div class="text-danger">' . $e->getMessage() . '</div>';
}
?>

<b>Important variables</b>
<ul>
    <li>thread_pool_size - how many threads there are for database</li>
    <li>innodb_buffer_pool_size - database pool size</li>
</ul>

<div class="mx550">

<table class="table table-sm table-hover">
    <tr>
        <th>Variables</th>
        <th>Value</th>
    </tr>
    <?php foreach ($rows as $row) : ?>
        <tr>
            <td><?php echo htmlspecialchars($row['variable_name'])?></td>
            <td><?php echo htmlspecialchars($row['value'])?></td>
        </tr>
    <?php endforeach;?>
</table>

</div>

<hr>

<h4>Running queries</h4>

<?php
$db = ezcDbInstance::get();
$rows = [];
try {
    $stmt = $db->prepare('SHOW FULL PROCESSLIST');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo '<div class="text-danger">' . $e->getMessage() . '</div>';
}

?>

<table class="table table-sm table-hover table-xs">
    <tr>
        <th>Id</th>
        <th>User</th>
        <th>Host</th>
        <th>db</th>
        <th>Command</th>
        <th>Time</th>
        <th>State</th>
        <th>Info</th>
        <th>Progress</th>
        <th>&nbsp;</th>
    </tr>
    <?php foreach ($rows as $row) : ?>
    <tr>
        <td><?php echo htmlspecialchars($row['id'])?></td>
        <td><?php echo htmlspecialchars((string)$row['user'])?></td>
        <td><?php echo htmlspecialchars((string)$row['host'])?></td>
        <td><?php echo htmlspecialchars((string)$row['db'])?></td>
        <td><?php echo htmlspecialchars((string)$row['command'])?></td>
        <td><?php echo htmlspecialchars((string)$row['time'])?></td>
        <td><?php echo htmlspecialchars((string)$row['state'])?></td>
        <td><?php echo htmlspecialchars((string)$row['info'])?></td>
        <td><?php echo htmlspecialchars((string)$row['progress'])?></td>
        <td><a class="btn btn-danger btn-xs csfr-required csfr-post" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('audit/configuration')?>/(action)/kill/(id)/<?php echo htmlspecialchars($row['id'])?>">Kill</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<h4>System load</h4>

<?php $sysLoad = sys_getloadavg(); ?>
<div class="row">
    <div class="col-6">
        <ul>
            <li>1 min - <?php echo $sysLoad[0] ?></li>
            <li>5 min - <?php echo $sysLoad[1] ?></li>
            <li>15 min - <?php echo $sysLoad[2] ?></li>
        </ul>
    </div>
</div>
<pre style="max-width: 1000px; overflow: auto">
<?php print_r(htmlspecialchars(json_encode($_SERVER, JSON_PRETTY_PRINT)))?>
</pre>

<h4>Extensions check</h4>
<ul>
    <li>Is the php_curl extension installed - <?php echo extension_loaded ('curl' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the mbstring extension installed - <?php echo extension_loaded ('mbstring' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the php-pdo extension installed - <?php echo extension_loaded ('pdo_mysql' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the gd extension installed - <?php echo extension_loaded ('gd' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the json extension detected - <?php echo function_exists('json_encode') ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the bcmath extension detected - <?php echo extension_loaded('bcmath') ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No, GEO detection will be disabled</span>'; ?></li>
    <li>Is the php-xml extension detected - <?php echo function_exists('simplexml_load_string') ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>'; ?></li>
    <li>Is the fileinfo extension detected - <?php echo extension_loaded ('fileinfo' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the ldap extension detected - <?php echo function_exists ('ldap_search' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No, required only if you use `lhldap` extension</span>'; ?></li>
    <li>Is the imap extension detected - <?php echo extension_loaded ('imap' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the redis or phpiredis extension detected - <?php echo extension_loaded ('phpiredis' ) || extension_loaded('redis') ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the soap extension detected - <?php echo extension_loaded ('soap' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the zlib extension detected - <?php echo extension_loaded ('zlib' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
    <li>Is the zip extension detected - <?php echo extension_loaded ('zip' ) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
</ul>

<h4>phpinfo</h4>
    <?php

    function embedded_phpinfo()
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();
        $phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);
        echo "
        <style type='text/css'>
            #phpinfo {}
            #phpinfo pre {margin: 0; }
            #phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
            #phpinfo a:hover {text-decoration: underline;}
            #phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
            #phpinfo .center {text-align: left;}
            #phpinfo .center table {margin: 1em auto; text-align: left;}
            #phpinfo .center th {text-align: left !important;}
            #phpinfo td, #phpinfo th {border: 1px solid #666; font-size: 12px; vertical-align: baseline; padding: 4px 5px;}
            #phpinfo h1 {font-size: 150%;text-align: center}
            #phpinfo h2 {font-size: 125%;text-align: center}
            #phpinfo .p {text-align: left;}
            #phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
            #phpinfo .h {background-color: #99c; font-weight: bold;}
            #phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
            #phpinfo .v i {color: #999;}
            #phpinfo img {float: right; border: 0;}
            #phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
        </style>
        <div id='phpinfo'>
            $phpinfo
        </div>
        ";
    }
    embedded_phpinfo();
    endif;
    ?>
