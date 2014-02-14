<?php if ($chat->screenshot !== false) : ?>      
<a href="#" class="screnshot-container"><img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>" alt="" /></a>
<script>
$('.screnshot-container').zoom({callback: function(){
        $(this).colorbox({'width':'95%','height':'95%',html: $('.screnshot-container').html()});
}});
</script>		
<?php else : ?>
<p>Perhaps screenshot is under way, click refresh to check for a new screenshot</p>
<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Refresh')?>" class="button tiny round" onclick="lhinst.updateScreenshot('<?php echo $chat->id?>')" />
<?php endif;?>