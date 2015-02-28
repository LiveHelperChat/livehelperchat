<div class="mt10">
    <div class="row mt10 footer-row">
        <div class="columns twelve">
            <p class="pull-right"><a target="_blank" href="http://livehelperchat.com">Live Helper Chat &copy; <?php echo date('Y')?></a></p>
            <p><a href="<?php if (isset($Result['theme']) !== false && $Result['theme']->widget_copyright_url != '') : ?><?php echo htmlspecialchars($Result['theme']->widget_copyright_url) ?><?php else : ?><?php echo erLhcoreClassModelChatConfig::fetch('customer_site_url')->current_value?><?php endif;?>" target="_blank"><?php if (isset($Result['theme'])) : ?><?php echo htmlspecialchars($Result['theme']->name_company)?><?php else : ?><?php echo htmlspecialchars(erLhcoreClassModelChatConfig::fetch('customer_company_name')->current_value)?><?php endif;?></a></p>
        </div>
    </div>
</div>