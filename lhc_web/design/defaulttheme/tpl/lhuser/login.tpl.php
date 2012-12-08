<h1><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Please login');?></h1>
<? if (isset($error)) : ?><h2 class="error-h2"><?=$error;?></h2><? endif;?>
<form method="post" action="<?=erLhcoreClassDesign::baseurl('/user/login/')?>">
<table>
    <tr>
        <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Username');?></td>
        <td><input type="text" name="Username" value="" /></td>
    </tr>
    <tr>
        <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password');?></td>
        <td><input type="password" name="Password" value="" /></td>
    </tr>
    <tr>
        <td><input type="submit" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?>" name="Login" /></td>
    </tr>
</table>
</form>