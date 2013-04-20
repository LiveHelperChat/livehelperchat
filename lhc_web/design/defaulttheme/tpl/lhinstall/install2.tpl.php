<form action="<?php echo erLhcoreClassDesign::baseurl('install/install')?>/2" method="POST">
<fieldset><legend>Installation step 2</legend>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<fieldset><legend>Database settings</legend>
<table>
    <tr>
        <td>Username</td>
        <td><input type="text" name="DatabaseUsername" value="<?php echo isset($db_username) ? $db_username : ''?>" /></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><input type="password" name="DatabasePassword" value="<?php echo isset($db_password) ? $db_password : ''?>" /></td>
    </tr>
    <tr>
        <td>Host</td>
        <td><input type="text" name="DatabaseHost" value="<?php echo isset($db_host) ? $db_host : '127.0.0.1' ?>"></td>
    </tr>
    <tr>
        <td>Port</td>
        <td><input type="text" name="DatabasePort" value="<?php echo isset($db_port) ? $db_port : '3306'?>"></td>
    </tr>
    <tr>
        <td>Database name</td>
        <td><input type="text" name="DatabaseDatabaseName" value="<?php echo isset($db_name) ? $db_name : ''?>"></td>
    </tr>
</table>
</fieldset>
<br>

<input type="submit" value="Next" class="small button" name="Install">
<br /><br />

</fieldset>
</form>