<fieldset><legend><?=erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','HTML code');?></legend>
<p>
<select id="LocaleID" class="default-select">
    <?php foreach ($locales as $locale ) : ?>
    <option value="<?=substr($locale,0,2)?>"><?=$locale?></option>
    <? endforeach; ?>    
</select>
</p>

<p class="explain"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Copy code from textarea to page where you want your status to appear');?></p>
<textarea style="width:100%;height:50px;font-size:11px;" id="HMLTContent" onclick="$(this).select()"><? echo htmlspecialchars('<script type="text/javascript" src="http://'.$_SERVER['HTTP_HOST'].erLhcoreClassDesign::baseurl('/chat/getstatus').'"></script>')?></textarea>
</fieldset>

<script type="text/javascript">

$('#LocaleID').change(function(){    
    $('#HMLTContent').html('&lt;script type=&quot;text/javascript&quot; src=&quot;http://<?=$_SERVER['HTTP_HOST'].erLhcoreClassSystem::instance()->WWWDir?>/index.php/'+$(this).val()+'/chat/getstatus'+'&quot;&gt;&lt;/script&gt;');
});

$('#HMLTContent').html('&lt;script type=&quot;text/javascript&quot; src=&quot;http://<?=$_SERVER['HTTP_HOST'].erLhcoreClassSystem::instance()->WWWDir?>/index.php/'+$('#LocaleID').val()+'/chat/getstatus'+'&quot;&gt;&lt;/script&gt;');
</script>