<div class="mt10">
    <div class="row mt10 footer-row">
        <div class="columns twelve">
            <p><a target="_blank" href="http://livehelperchat.com">Live Helper Chat &copy; <?php echo date('Y')?></a></p>
        </div>       
    </div>
</div>
<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
		$debug = ezcDebug::getInstance(); 
		echo $debug->generateOutput(); 
} ?>