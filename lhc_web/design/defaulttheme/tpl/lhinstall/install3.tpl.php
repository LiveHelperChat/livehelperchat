<form action="<?php echo erLhcoreClassDesign::baseurl('install/install')?>/3" method="post" autocomplete="off">
<fieldset><legend>Installation step 3</legend> 
<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

        
<fieldset><legend>Initial application settings</legend> 
<table>
    <tr>
        <td>Admin username*</td>
        <td><input type="text" name="AdminUsername" value="<?php isset($admin_username) ? print $admin_username : ''?>" /></td>
    </tr>
    <tr>
        <td>Admin password*</td>
        <td><input type="password" name="AdminPassword" value="" /></td>
    </tr>
    <tr>
        <td>Admin password repeat*</td>
        <td><input type="password" name="AdminPassword1" value="" /></td>
    </tr>
    <tr>
        <td>E-mail*</td>
        <td><input type="text" name="AdminEmail" value="<?php isset($admin_email) ? print $admin_email : ''?>"></td>
    </tr>
    <tr>
        <td>Your name</td>
        <td><input type="text" name="AdminName" value="<?php isset($admin_name) ? print $admin_name : ''?>"></td>
    </tr>
    <tr>
        <td>Your surname</td>
        <td><input type="text" name="AdminSurname" value="<?php isset($admin_surname) ? print $admin_surname : ''?>"></td>
    </tr>
    <tr>
        <td>Default department*</td>
        <td><input type="text" name="DefaultDepartament" value="<?php isset($admin_departament) ? print $admin_departament : ''?>"></td>
    </tr>
</table>
</fieldset>
<br>

<input type="submit" class="small button" value="Finish installation" name="Install">
<br /><br />

</fieldset>
</form>