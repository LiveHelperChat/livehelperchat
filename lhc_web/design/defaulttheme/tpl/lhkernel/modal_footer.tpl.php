<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
    $debug = ezcDebug::getInstance();
    echo "<div><pre class='bg-light text-dark m-2 p-2 border'>" . json_encode(erLhcoreClassUser::$permissionsChecks, JSON_PRETTY_PRINT) . "</pre></div>";
    echo "<div><pre class='bg-light text-dark m-2 p-2 border'>Max memomry usage: " . round(memory_get_peak_usage() / 1024 / 1024, 2). " MB </pre></div>";
    echo $debug->generateOutput();
} ?>
</div>
</div>
</div>
