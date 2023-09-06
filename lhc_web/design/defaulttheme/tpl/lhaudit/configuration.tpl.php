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
    );
    ?>

    <div class="row">
    <?php foreach ($objectsLog as $objectToLog) : ?>
        <div class="col-3"><label><input <?php if (isset($audit_options['log_objects']) && in_array($objectToLog['class'],$audit_options['log_objects'])) :?>checked="checked"<?php endif;?> type="checkbox" name="log_objects[]" value="<?php echo $objectToLog['class']?>"> <?php echo htmlspecialchars($objectToLog['name'])?></label></div>
    <?php endforeach; ?>
    </div>

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

</form>

<?php if (!class_exists('erLhcoreClassInstance') && erLhcoreClassUser::instance()->hasAccessTo('lhaudit','see_system')) : ?>

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
