<ul class="circle">
<?php foreach ($online_operators as $operator) : ?>
	<li><?php echo htmlspecialchars($operator->user->name).' '.htmlspecialchars($operator->user->surname); ?> | <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Last activity');?>: <?php echo $operator->user->lastactivity_ago ?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','ago');?>.<?php /* FOR Future send message to operator <a href="#" class="right tiny button round very-tiny"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Send message');?></a></li>*/ ?>
<?php endforeach; ?>
</ul>