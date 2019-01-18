<img src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="Live Helper Chat" title="Live Helper Chat" />

<h1>Installation step 2</h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('install/install')?>/2" method="POST" autocomplete="off">

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<h2>Database settings</h2>

<table class="table">
    <tr>
        <td>Username</td>
        <td><input class="form-control" type="text" name="DatabaseUsername" value="<?php echo isset($db_username) ? htmlspecialchars($db_username) : ''?>" /></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><input class="form-control" type="password" name="DatabasePassword" value="<?php echo isset($db_password) ? htmlspecialchars($db_password) : ''?>" /></td>
    </tr>
    <tr>
        <td>Host</td>
        <td><input class="form-control" type="text" name="DatabaseHost" value="<?php echo isset($db_host) ? htmlspecialchars($db_host) : '127.0.0.1' ?>"></td>
    </tr>
    <tr>
        <td>Port</td>
        <td><input class="form-control" type="text" name="DatabasePort" value="<?php echo isset($db_port) ? htmlspecialchars($db_port) : '3306'?>"></td>
    </tr>
    <tr>
        <td>Database name</td>
        <td><input class="form-control" type="text" name="DatabaseDatabaseName" value="<?php echo isset($db_name) ? htmlspecialchars($db_name) : ''?>"></td>
    </tr>
</table>
<br>

<input type="submit" value="Next" class="btn btn-default" name="Install">
<br /><br />

</form>