<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Please login');?></h1>
<?php if (isset($error)) : ?><h2 class="error-h2"><?php echo $error;?></h2><?php endif;?>
<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('/user/login/')?>">
<table>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Username');?></td>
        <td><input type="text" class="inputfield" name="Username" value="" /></td>
    </tr>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password');?></td>
        <td><input type="password" class="inputfield" name="Password" value="" /></td>
    </tr>
    <tr>
        <td><input type="submit" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?>" name="Login" /></td>
        <td><a href="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password remind')?></a></td>
    </tr>   
</table>
</form>