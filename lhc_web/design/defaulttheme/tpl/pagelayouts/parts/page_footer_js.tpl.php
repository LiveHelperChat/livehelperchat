<?php /*<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/angular.min.js;js/checklist-model.min.js;js/angular.lhc.min.js;vendor/metisMenu/metisMenu.min.js;js/sidebar.js');?>"></script>*/ ?>

<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('vendor/metisMenu/metisMenu.min.js;js/sidebar.js');?>"></script>
<script type="module" src="<?php echo erLhcoreClassDesign::design('js/svelte/public/build/main.js');?>"></script>
<?php echo isset($Result['additional_footer_js']) ? $Result['additional_footer_js'] : ''?>