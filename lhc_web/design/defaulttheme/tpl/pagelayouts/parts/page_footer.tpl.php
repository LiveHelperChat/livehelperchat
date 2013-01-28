<div class="mt10">
    <div class="row mt10 footer-row">
        <div class="columns twelve">
            <p class="right"><a href="mailto:remdex@gmail.com">Contact</a></p>
            <p><a target="_blank" href="http://livehelperchat.com">Live Helper Chat &copy; <?php echo date('Y')?></a></p>
        </div>       
    </div>
</div>

<script type="text/javascript" language="javascript" src="<?=erLhcoreClassDesign::designJS('js/app.js');?>"></script>


<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance(); 
		echo $debug->generateOutput(); 
} ?>