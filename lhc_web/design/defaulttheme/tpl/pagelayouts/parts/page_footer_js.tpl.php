<?php if (isset($Result['require_angular']) || (isset($Result['additional_footer_js']) && strpos($Result['additional_footer_js'],'angular') !== false)) : ?>
    <script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/angular.min.js;js/checklist-model.min.js;js/angular.lhc.legacy.js');?>"></script>
<?php endif; ?>

<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('vendor/metisMenu/metisMenu.min.js;js/sidebar.js');?>"></script>
<script type="module" src="<?php echo erLhcoreClassDesign::designJSStatic('js/svelte/public/build/main.js');?>"></script>
<?php echo isset($Result['additional_footer_js']) ? $Result['additional_footer_js'] : ''?>