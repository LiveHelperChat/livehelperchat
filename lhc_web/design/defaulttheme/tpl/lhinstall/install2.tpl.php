<form action="<?=erLhcoreClassDesign::baseurl('install/install/2')?>" method="POST">
<fieldset><legend>Installation step 2</legend> 
<? if (isset($errors)) : ?>
<div class="error-list">
<ul>
    <? foreach ($errors as $error) : ?>
        <li><?=$error?></li>
    <? endforeach; ?> 
</ul>
</div>
<? endif; ?>

        
<fieldset><legend>Database settings</legend> 
<table>
    <tr>
        <td>Username</td>
        <td><input type="text" name="DatabaseUsername" value="<?=isset($db_username) ? $db_username : ''?>" /></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><input type="password" name="DatabasePassword" value="<?=isset($db_password) ? $db_password : ''?>" /></td>
    </tr>
    <tr>
        <td>Host</td>
        <td><input type="text" name="DatabaseHost" value="<?=isset($db_host) ? $db_host : ''?>"></td>
    </tr>
    <tr>
        <td>Port</td>
        <td><input type="text" name="DatabasePort" value="<?=isset($db_port) ? $db_port : '3306'?>"></td>
    </tr>
    <tr>
        <td>Database name</td>
        <td><input type="text" name="DatabaseDatabaseName" value="<?=isset($db_name) ? $db_name : ''?>"></td>
    </tr>
</table>
</fieldset>
<br>

<div class="action-row">
<input type="submit" value="Next" name="Install">
</div>

</fieldset>
</form>