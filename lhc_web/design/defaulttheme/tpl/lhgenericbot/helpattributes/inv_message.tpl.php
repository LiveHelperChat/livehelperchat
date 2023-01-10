<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Sample how to use uploaded image from Design section');?>
<div class="alert alert-success" role="alert">
    <?php echo nl2br(htmlspecialchars('[html]
<img src="{proactive_img_1}" alt="" />
[/html]')); ?>
</div>

<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'Supported replaceable variables. They are taken from online visitor passed variables.');?>
<ul>
    <li><span class="badge bg-secondary">{nick}</span> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'visitor nick if we know it from previous chats');?></li>
    <li><span class="badge bg-secondary">{lhc.var.&lt;variable key&gt;}</span> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'invisible by operator');?></li>
    <li><span class="badge bg-secondary">{lhc.add.&lt;variable key&gt;}</span> - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/helpattributes', 'visible by operator');?></li>
</ul>
