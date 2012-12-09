<form action="<?php echo erLhcoreClassDesign::baseurl('install/install/3')?>" method="POST">
<fieldset><legend>Installation step 3</legend> 
<?php if (isset($errors)) : ?>
<div class="error-list">
<ul>
    <?php foreach ($errors as $error) : ?>
        <li><?php echo $error?></li>
    <?php endforeach; ?> 
</ul>
</div>
<?php endif; ?>

        
<fieldset><legend>Initial application settings</legend> 
<table>
    <tr>
        <td>Admin username*</td>
        <td><input type="text" name="AdminUsername" value="<?php echo isset($admin_username) ? $admin_username : ''?>" /></td>
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
        <td><input type="text" name="AdminEmail" value="<?php echo isset($admin_email) ? $admin_email : ''?>"></td>
    </tr>
    <tr>
        <td>Your name</td>
        <td><input type="text" name="AdminName" value="<?php echo isset($admin_name) ? $admin_name : ''?>"></td>
    </tr>
    <tr>
        <td>Your surname</td>
        <td><input type="text" name="AdminSurname" value="<?php echo isset($admin_surname) ? $admin_surname : ''?>"></td>
    </tr>
    <tr>
        <td>Default department*</td>
        <td><input type="text" name="DefaultDepartament" value="<?php echo isset($admin_departament) ? $admin_departament : ''?>"></td>
    </tr>
</table>
</fieldset>
<br>

<div class="action-row">
<input type="submit" value="Finish installation" name="Install">
</div>

</fieldset>
</form>