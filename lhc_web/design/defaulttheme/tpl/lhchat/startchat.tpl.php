<h1><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill out this form to start a chat');?></h1>
<? if (isset($errArr)) : ?>
    <? foreach ((array)$errArr as $error) : ?>
    	<div class="error">*&nbsp;<?=$error;?></div>
    <? endforeach; ?>
<? endif;?>

<form method="post" action="<?=erLhcoreClassDesign::baseurl('chat/startchat')?>">
<table>
    <tr>
        <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?></td>
        <td><input type="text" name="Username" value="" /></td>
    </tr>
    <tr>
        <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?></td>
        <td><input type="text" name="Email" value="" /></td>
    </tr>
    <tr>
        <td><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department');?></td>
        <td>
        <select name="DepartamentID" style="width:100%">
            <? foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
                <option value="<?=$departament['id']?>"><?=$departament['name']?></option>
            <? endforeach; ?>
        </select>
        </td>
    </tr>
    <tr>
        <td><input type="submit" value="<?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChat" /></td>
    </tr>
</table>
<input type="hidden" value="<?=htmlspecialchars($referer);?>" name="URLRefer"/>
</form>