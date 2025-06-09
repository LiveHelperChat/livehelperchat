<?php
function generateMermaidFromUseCases($botId) {
    try {
        $bot = erLhcoreClassModelGenericBotBot::fetch($botId);
        if (!($bot instanceof erLhcoreClassModelGenericBotBot)) {
            return "graph TD\n    Error[\"" . erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Bot not found') . "\"]";
        }

        $triggers = erLhcoreClassModelGenericBotTrigger::getList(array(
            'filter' => array('bot_id' => $botId),
            'sort' => 'pos ASC, name ASC',
            'limit' => false
        ));

        if (empty($triggers)) {
            return "graph TD\n    Empty[\"" . erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','No triggers found for this bot') . "\"]";
        }

        $mermaidLines = array('graph LR');
        $addedNodes = array();
        $connections = array();
        $firstLevelTriggers = array();
        $secondLevelTriggers = array();
        
        $mermaidLines[] = '    Start([🚀 ' . erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Chat Start') . '])';
        $addedNodes['Start'] = true;

        foreach ($triggers as $trigger) {
            $triggerNode = 'T' . $trigger->id;
            $triggerName = htmlspecialchars(trim($trigger->name));
            $triggerName = strlen($triggerName) > 50 ? substr($triggerName, 0, 50) . '...' : $triggerName;
            
            if (!isset($addedNodes[$triggerNode])) {
                if ($trigger->default == 1) {
                    $mermaidLines[] = "    {$triggerNode}[\"📍 {$triggerName}\"]";
                    $connections[] = "    Start --> {$triggerNode}";
                    $firstLevelTriggers[] = $triggerNode;
                } elseif ($trigger->default_unknown == 1) {
                    $mermaidLines[] = "    {$triggerNode}{\"❓ {$triggerName}\"}";
                } elseif ($trigger->default_always == 1) {
                    $mermaidLines[] = "    {$triggerNode}((\"🔄 {$triggerName}\"))";
                } else {
                    $mermaidLines[] = "    {$triggerNode}[\"⚡ {$triggerName}\"]";
                }
                $addedNodes[$triggerNode] = true;
            }

            if (!empty($trigger->actions)) {
                preg_match_all('/":\s*"?(\d+)"?/', $trigger->actions, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $referencedTriggerId) {

                        if (!isset($addedNodes['T' . $referencedTriggerId])) {
                            $referencedTrigger = erLhcoreClassModelGenericBotTrigger::fetch($referencedTriggerId);
                            if ($referencedTrigger instanceof erLhcoreClassModelGenericBotTrigger && $referencedTrigger->bot_id == $botId) {
                                $referencedTriggerName = htmlspecialchars(trim($referencedTrigger->name));
                                $referencedTriggerName = strlen($referencedTriggerName) > 50 ? substr($referencedTriggerName, 0, 50) . '...' : $referencedTriggerName;
                                $nextTriggerNode = 'T' . $referencedTriggerId;
                                $mermaidLines[] = "    {$nextTriggerNode}[\"⚡{$referencedTriggerName}\"]";
                                $addedNodes[$nextTriggerNode] = true;
                                
                                // Check if this is a second level trigger (connected from first level)
                                if (in_array($triggerNode, $firstLevelTriggers)) {
                                    $secondLevelTriggers[] = $nextTriggerNode;
                                }
                            } else {
                                continue; // Skip if the referenced trigger does not exist
                            }
                        }

                        $nextTriggerNode = 'T' . $referencedTriggerId;
                        $connections[] = "    {$triggerNode} --> {$nextTriggerNode}";
                        
                        // Also track second level triggers for existing nodes
                        if (in_array($triggerNode, $firstLevelTriggers) && !in_array($nextTriggerNode, $secondLevelTriggers)) {
                            $secondLevelTriggers[] = $nextTriggerNode;
                        }

                    }
                }
            }

            $triggerEvents = erLhcoreClassModelGenericBotTriggerEvent::getList(array(
                'filter' => array('trigger_id' => $trigger->id),
                'limit' => false
            ));
            
            foreach ($triggerEvents as $event) {
                if (!empty($event->pattern)) {
                    $pattern = trim($event->pattern);
                    if (!empty($pattern)) {
                        $patternText = substr($pattern, 0, 50);
                        $patternText = preg_replace('/[^_a-zA-Z0-9\s]/', '', $patternText);
                        $patternNode = 'P' . md5($pattern);
                        
                        if (!isset($addedNodes[$patternNode])) {
                            $mermaidLines[] = "    {$patternNode}(\"🔑{$patternText}\")";
                            $addedNodes[$patternNode] = true;
                        }
                        $connections[] = "    {$patternNode} --> {$triggerNode}";
                    }                    
                }
            }
        }
        
        $mermaidLines = array_merge($mermaidLines, array_unique($connections));
        $mermaidLines[] = '';
        $mermaidLines[] = '    classDef startNode fill:#e8f5e8,stroke:#4caf50,stroke-width:3px';
        $mermaidLines[] = '    classDef firstLevelNode fill:#e3f2fd,stroke:#1976d2,stroke-width:3px,color:#0d47a1';
        $mermaidLines[] = '    classDef secondLevelNode fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,color:#4a148c';
        $mermaidLines[] = '    classDef patternNode fill:#fff3e0,stroke:#ff9800,stroke-width:2px,color:#e65100';
        $mermaidLines[] = '    class Start startNode';
        
        // Apply first level styling to first level triggers
        foreach ($firstLevelTriggers as $triggerNode) {
            $mermaidLines[] = "    class {$triggerNode} firstLevelNode";
        }
        
        // Apply second level styling to second level triggers
        foreach ($secondLevelTriggers as $triggerNode) {
            $mermaidLines[] = "    class {$triggerNode} secondLevelNode";
        }
        
        // Apply pattern styling to all pattern nodes
        foreach ($addedNodes as $nodeId => $added) {
            if (strpos($nodeId, 'P') === 0 && $nodeId !== 'Start') {
                $mermaidLines[] = "    class {$nodeId} patternNode";
            }
        }
        
        return implode("\n", $mermaidLines);
        
    } catch (Exception $e) {
        return "graph TD\n    Error[\"" . erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Error') . ": " . htmlspecialchars($e->getMessage()) . "\"]";
    }
}

// Generate Mermaid content
$mermaidContent = '';
$mermaidContent = generateMermaidFromUseCases($bot->id);
?>

<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Bot preview');
$modalSize = 'xl';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<script src="<?php echo erLhcoreClassDesign::designJS('js/mermaid.min.js');?>"></script>

<div class="mb-3">
    <div class="btn-group btn-group-sm" role="group" aria-label="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Zoom controls')?>">
        <button type="button" class="btn btn-outline-secondary btn-sm" id="zoom-reset" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Reset Zoom')?>">
            <i class="material-icons me-0">center_focus_weak</i>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="zoom-out" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Zoom Out')?>">
            <i class="material-icons me-0">zoom_out</i>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="zoom-in" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Zoom In')?>">
            <i class="material-icons me-0">zoom_in</i>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="reload-chart" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/bot','Reload Chart')?>">
            <i class="material-icons me-0">refresh</i>
        </button>
    </div>
</div>

<div style="min-height: 600px">
    <div class="mermaid" id="mermaid-container" style="transform-origin: top left;">
        <?php echo $mermaidContent; ?>
    </div>
</div>

<script>
(function() {
    // Check if already initialized to prevent conflicts when modal reopens
    if (window.botChartInitialized) {
        return;
    }
    window.botChartInitialized = true;
    
    mermaid.initialize({
        theme: 'default',
        themeVariables: {
            primaryColor: '#e1f5fe',
            primaryTextColor: '#0d47a1',
            primaryBorderColor: '#1976d2',
            lineColor: '#1565c0',
            secondaryColor: '#bbdefb',
            tertiaryColor: '#e3f2fd'
        },
        startOnLoad: false
    });
    
    // Force initialization for modal window
    mermaid.init();
    
    // Zoom functionality
    let currentScale = 1;
    const scaleStep = 0.2;
    const minScale = 0.5;
    const maxScale = 3;
    
    function updateTransform() {
        const container = document.getElementById('mermaid-container');
        if (container) {
            container.style.transform = `scale(${currentScale})`;
        }
    }
    
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const zoomResetBtn = document.getElementById('zoom-reset');
    const reloadBtn = document.getElementById('reload-chart');
    
    if (zoomInBtn && !zoomInBtn.hasAttribute('data-listener-added')) {
        zoomInBtn.setAttribute('data-listener-added', 'true');
        zoomInBtn.addEventListener('click', function() {
            if (currentScale < maxScale) {
                currentScale += scaleStep;
                updateTransform();
            }
        });
    }
    
    if (zoomOutBtn && !zoomOutBtn.hasAttribute('data-listener-added')) {
        zoomOutBtn.setAttribute('data-listener-added', 'true');
        zoomOutBtn.addEventListener('click', function() {
            if (currentScale > minScale) {
                currentScale -= scaleStep;
                updateTransform();
            }
        });
    }
    
    if (zoomResetBtn && !zoomResetBtn.hasAttribute('data-listener-added')) {
        zoomResetBtn.setAttribute('data-listener-added', 'true');
        zoomResetBtn.addEventListener('click', function() {
            currentScale = 1;
            updateTransform();
        });
    }
    
    if (reloadBtn && !reloadBtn.hasAttribute('data-listener-added')) {
        reloadBtn.setAttribute('data-listener-added', 'true');
        reloadBtn.addEventListener('click', function() {
            lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + '/genericbot/bot/' +<?php echo $bot->id; ?> + '/(type)/chart'});
        });
    }
    
    // Add click event handlers for nodes - run immediately
    setTimeout(function() {
        const nodes = document.querySelectorAll('.mermaid g.node');
        nodes.forEach(function(node, index) {
            if (!node.hasAttribute('data-click-added')) {
                node.setAttribute('data-click-added', 'true');
                node.style.cursor = 'pointer';
                node.addEventListener('click', function() {
                    const nodeId = node.id || `node-${index}`;
                    // Check if this is a trigger node (contains 'T' followed by number)
                    const triggerMatch = nodeId.match(/T(\d+)/);

                    if (triggerMatch) {
                        const triggerId = triggerMatch[1];
                        document.location = WWW_DIR_JAVASCRIPT + `genericbot/bot/<?php echo $bot->id; ?>#!#/trigger-${triggerId}`;
                    }
                });
            }
        });
    }, 500);
    
    // Reset flag when modal is closed
    $(document).on('hidden.bs.modal', function() {
        window.botChartInitialized = false;
    });
})();
</script>


<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>



