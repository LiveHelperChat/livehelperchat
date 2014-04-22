<div class="mt10">
    <div class="row mt10 footer-row">
        <div class="columns twelve">
            <p class="right"><a target="_blank" href="http://livehelperchat.com">Live Helper Chat &copy; <?php echo date('Y')?></a></p>
            <p><a href="<?php echo erLhcoreClassModelChatConfig::fetch('customer_site_url')->current_value?>"><?php echo htmlspecialchars(erLhcoreClassModelChatConfig::fetch('customer_company_name')->current_value)?></a></p>
        </div>
    </div>
</div>

<script type="text/javascript" language="javascript" src="<?php echo erLhcoreClassDesign::designJS('js/app.js;js/angular.min.js;js/angular.lhc.js');?>"></script>
<?php echo isset($Result['additional_footer_js']) ? $Result['additional_footer_js'] : ''?>