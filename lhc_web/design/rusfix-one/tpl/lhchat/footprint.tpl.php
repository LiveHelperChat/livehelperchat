<div>
<a class="tiny round button" rel="<?php echo $chat->id?>" onclick="lhinst.refreshFootPrint($(this))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Refresh')?></a>
<ul class="foot-print-content circle" id="footprint-<?php echo $chat->id?>">
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/footprint_list.tpl.php'));?>
</ul>
</div>