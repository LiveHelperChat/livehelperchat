<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill out this form to start a chat');?></h1>
<?php if (isset($errArr)) : ?>
    <?php foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?php echo $error;?></div>
    <?php endforeach; ?>
<?php endif;?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>">
<table>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?></td>
        <td><input type="text" name="Username" value="" /></td>
    </tr>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?></td>
        <td><input type="text" name="Email" value="" /></td>
    </tr>
    <tr>
        <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department');?></td>
        <td>
        <select name="DepartamentID" style="width:100%">
            <?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
                <option value="<?php echo $departament['id']?>"><?php echo $departament['name']?></option>
            <?php endforeach; ?>
        </select>
        </td>
    </tr>
    <tr>
        <td><input type="submit" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChat" /></td>
    </tr>
</table>
<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>
</form>