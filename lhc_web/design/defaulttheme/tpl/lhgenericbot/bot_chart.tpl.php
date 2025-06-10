<?php
function generateMermaidFromUseCases($botId) {
    $limitString = 100;
    $allowedCharsPattern = '/[^_a-zA-Z0-9\s\.]/'; // Pattern for allowed characters in node names
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
            $triggerName = htmlspecialchars(preg_replace($allowedCharsPattern, '', trim($trigger->name)));
            $triggerName = strlen($triggerName) > $limitString ? substr($triggerName, 0, $limitString) . '...' : $triggerName;
            
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
                                $referencedTriggerName = htmlspecialchars(preg_replace($allowedCharsPattern, '', trim($referencedTrigger->name)));
                                $referencedTriggerName = strlen($referencedTriggerName) > $limitString ? substr($referencedTriggerName, 0, $limitString) . '...' : $referencedTriggerName;
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
                        $patternText = substr($pattern, 0, $limitString);
                        $patternText = preg_replace($allowedCharsPattern, '', $patternText);
                        $patternNode = 'TE' . $trigger->id . '_' . $event->id;
                        
                        if (!isset($addedNodes[$patternNode])) {
                            $mermaidLines[] = "    {$patternNode}(\"🔑{$patternText}\")";
                            $addedNodes[$patternNode] = true;
                        }
                        $connections[] = "    {$patternNode} --> {$triggerNode}";
                    }                    
                }
            }

            // Check for webhooks that reference this trigger
            $webhooks = erLhcoreClassModelChatWebhook::getList(array(
                'filterlor' => array(
                    'trigger_id' => array($trigger->id),
                    'trigger_id_alt' => array($trigger->id)
                ),
                'limit' => false
            ));

            foreach ($webhooks as $webhook) {
                $webhookName = preg_replace($allowedCharsPattern, '', trim($webhook->name));
                if (!empty($webhook->event)) {
                    $webhookName .= ' [' . preg_replace($allowedCharsPattern, '', trim($webhook->event)) . ']';
                }
                $webhookName = strlen($webhookName) > $limitString ? substr($webhookName, 0, $limitString) . '...' : $webhookName;
                $webhookName = htmlspecialchars($webhookName);
                $webhookNode = 'W' . $webhook->id;
                
                if (!isset($addedNodes[$webhookNode])) {
                    $mermaidLines[] = "    {$webhookNode}[\"🔗 {$webhookName}\"]";
                    $addedNodes[$webhookNode] = true;
                }
                
                // Connect webhook to trigger based on which field references it
                if ($webhook->trigger_id == $trigger->id) {
                    $connections[] = "    {$webhookNode} --> {$triggerNode}";
                }
                if ($webhook->trigger_id_alt == $trigger->id) {
                    $connections[] = "    {$webhookNode} -.-> {$triggerNode}";
                }
            }

            // Check for widget themes that reference this trigger in bot_configuration
            $widgetThemes = erLhAbstractModelWidgetTheme::getList(array(
                'filterlike' => array('bot_configuration' => '"trigger_id":"' . $trigger->id . '"'),
                'limit' => false
            ));

            foreach ($widgetThemes as $theme) {
                $themeName = preg_replace($allowedCharsPattern, '', trim($theme->name));
                $themeName = strlen($themeName) > $limitString ? substr($themeName, 0, $limitString) . '...' : $themeName;
                $themeName = htmlspecialchars($themeName);
                $themeNode = 'TH' . $theme->id;
                
                if (!isset($addedNodes[$themeNode])) {
                    $mermaidLines[] = "    {$themeNode}[\"🎨 {$themeName}\"]";
                    $addedNodes[$themeNode] = true;
                }
                
                $connections[] = "    {$themeNode} --> {$triggerNode}";
            }

            // Check for proactive chat invitations that reference this trigger
            $proactiveInvitations = erLhAbstractModelProactiveChatInvitation::getList(array(
                'filter' => array('trigger_id' => $trigger->id),
                'limit' => false
            ));

            foreach ($proactiveInvitations as $invitation) {
                $invitationName = preg_replace($allowedCharsPattern, '', trim($invitation->name));
                $invitationName = strlen($invitationName) > $limitString ? substr($invitationName, 0, $limitString) . '...' : $invitationName;
                $invitationName = htmlspecialchars($invitationName);
                $invitationNode = 'PI' . $invitation->id;
                
                if (!isset($addedNodes[$invitationNode])) {
                    $mermaidLines[] = "    {$invitationNode}[\"💬 {$invitationName}\"]";
                    $addedNodes[$invitationNode] = true;
                }
                
                $connections[] = "    {$invitationNode} --> {$triggerNode}";
            }

            // Check for bot commands that reference this trigger
            $botCommands = erLhcoreClassModelGenericBotCommand::getList(array(
                'filter' => array('trigger_id' => $trigger->id),
                'limit' => false
            ));

            foreach ($botCommands as $command) {
                $commandName = trim($command->name);
                if (empty($commandName)) {
                    $commandName = trim($command->command);
                }
                $commandName = preg_replace('/[^_a-zA-Z0-9\s\.]/', '', $commandName);
                $commandName = strlen($commandName) > $limitString ? substr($commandName, 0, $limitString) . '...' : $commandName;
                $commandName = htmlspecialchars($commandName);
                $commandNode = 'CMD' . $command->id;
                
                if (!isset($addedNodes[$commandNode])) {
                    $mermaidLines[] = "    {$commandNode}[\"⌨️ {$commandName}\"]";
                    $addedNodes[$commandNode] = true;
                }
                
                $connections[] = "    {$commandNode} --> {$triggerNode}";
            }

            // Check for auto responders that reference this trigger in bot_configuration
            $autoResponders = erLhAbstractModelAutoResponder::getList(array(
                'filterlike' => array('bot_configuration' => 'trigger_id":' . $trigger->id  ),
                'limit' => false
            ));

            foreach ($autoResponders as $autoResponder) {
                $responderName = preg_replace($allowedCharsPattern, '', trim($autoResponder->name));
                $responderName = strlen($responderName) > $limitString ? substr($responderName, 0, $limitString) . '...' : $responderName;
                $responderName = htmlspecialchars($responderName);
                $responderNode = 'AR' . $autoResponder->id;
                
                if (!isset($addedNodes[$responderNode])) {
                    $mermaidLines[] = "    {$responderNode}[\"🤖 {$responderName}\"]";
                    $addedNodes[$responderNode] = true;
                }
                
                $connections[] = "    {$responderNode} --> {$triggerNode}";
            }
        }
        
        // Check for departments that reference this bot (not specific triggers)
        $departments = erLhcoreClassModelDepartament::getList(array(
            'filterlike' => array('bot_configuration' => '"bot_id":' . $botId . ''),
            'limit' => false
        ));

        foreach ($departments as $department) {
            $departmentName = preg_replace($allowedCharsPattern, '', trim($department->name));
            $departmentName = strlen($departmentName) > $limitString ? substr($departmentName, 0, $limitString) . '...' : $departmentName;
            $departmentName = htmlspecialchars($departmentName);
            $departmentNode = 'DEP' . $department->id;
            
            if (!isset($addedNodes[$departmentNode])) {
                $mermaidLines[] = "    {$departmentNode}[\" 🏢 {$departmentName}\"]";
                $addedNodes[$departmentNode] = true;
            }
            
            $connections[] = "    {$departmentNode} --> Start";
        }
        
        // Optimize connections by grouping multiple targets from same source
        $optimizedConnections = array();
        $connectionMap = array();
        
        // Parse connections and group by source
        foreach (array_unique($connections) as $connection) {
            if (preg_match('/^\s*(\w+)\s+(-->|-.->)\s+(\w+)$/', trim($connection), $matches)) {
                $source = $matches[1];
                $arrow = $matches[2];
                $target = $matches[3];
                
                if (!isset($connectionMap[$source])) {
                    $connectionMap[$source] = array();
                }
                if (!isset($connectionMap[$source][$arrow])) {
                    $connectionMap[$source][$arrow] = array();
                }
                $connectionMap[$source][$arrow][] = $target;
            }
        }
        
        // Generate optimized connection strings
        foreach ($connectionMap as $source => $arrowTypes) {
            foreach ($arrowTypes as $arrow => $targets) {
                if (count($targets) > 1) {
                    $optimizedConnections[] = "    {$source} {$arrow} " . implode(' & ', $targets);
                } else {
                    $optimizedConnections[] = "    {$source} {$arrow} {$targets[0]}";
                }
            }
        }
        
        $mermaidLines = array_merge($mermaidLines, $optimizedConnections);
        $mermaidLines[] = '';
        $mermaidLines[] = '    classDef startNode fill:#e8f5e8,stroke:#4caf50,stroke-width:3px';
        $mermaidLines[] = '    classDef firstLevelNode fill:#e3f2fd,stroke:#1976d2,stroke-width:3px,color:#0d47a1';
        $mermaidLines[] = '    classDef secondLevelNode fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px,color:#4a148c';
        $mermaidLines[] = '    classDef patternNode fill:#fff3e0,stroke:#ff9800,stroke-width:2px,color:#e65100';
        $mermaidLines[] = '    classDef webhookNode fill:#e8f5e8,stroke:#4caf50,stroke-width:2px,color:#2e7d32';
        $mermaidLines[] = '    classDef themeNode fill:#fce4ec,stroke:#e91e63,stroke-width:2px,color:#ad1457';
        $mermaidLines[] = '    classDef invitationNode fill:#fff8e1,stroke:#ff8f00,stroke-width:2px,color:#e65100';
        $mermaidLines[] = '    classDef commandNode fill:#f1f8e9,stroke:#689f38,stroke-width:2px,color:#33691e';
        $mermaidLines[] = '    classDef autoResponderNode fill:#fafafa,stroke:#616161,stroke-width:2px,color:#212121';
        $mermaidLines[] = '    classDef departmentNode fill:#f3e5f5,stroke:#9c27b0,stroke-width:2px,color:#6a1b99';
        $mermaidLines[] = '    class Start startNode';
        
        // Apply first level styling to first level triggers
        foreach ($firstLevelTriggers as $triggerNode) {
            $mermaidLines[] = "    class {$triggerNode} firstLevelNode";
        }
        
        // Apply second level styling to second level triggers
        foreach ($secondLevelTriggers as $triggerNode) {
            $mermaidLines[] = "    class {$triggerNode} secondLevelNode";
        }
        
        // Apply styling to all nodes by type
        foreach ($addedNodes as $nodeId => $added) {
            if (strpos($nodeId, 'TE') === 0) {
                $mermaidLines[] = "    class {$nodeId} patternNode";
            } elseif (strpos($nodeId, 'W') === 0) {
                $mermaidLines[] = "    class {$nodeId} webhookNode";
            } elseif (strpos($nodeId, 'TH') === 0) {
                $mermaidLines[] = "    class {$nodeId} themeNode";
            } elseif (strpos($nodeId, 'PI') === 0) {
                $mermaidLines[] = "    class {$nodeId} invitationNode";
            } elseif (strpos($nodeId, 'CMD') === 0) {
                $mermaidLines[] = "    class {$nodeId} commandNode";
            } elseif (strpos($nodeId, 'AR') === 0) {
                $mermaidLines[] = "    class {$nodeId} autoResponderNode";
            } elseif (strpos($nodeId, 'DEP') === 0) {
                $mermaidLines[] = "    class {$nodeId} departmentNode";
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

<script src="<?php echo erLhcoreClassDesign::designJSStatic('js/mermaid.min.js');?>"></script>

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
        startOnLoad: false,
        maxEdges: 5000,
        maxTextSize: 100000
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
                    // Check if this is a trigger event node (contains 'TE' followed by trigger_id_event_id)
                    const triggerEventMatch = nodeId.match(/TE(\d+)_\d+/);
                    // Check if this is a webhook node (contains 'W' followed by number)
                    const webhookMatch = nodeId.match(/W(\d+)/);
                    // Check if this is a theme node (contains 'TH' followed by number)
                    const themeMatch = nodeId.match(/TH(\d+)/);
                    // Check if this is a proactive invitation node (contains 'PI' followed by number)
                    const invitationMatch = nodeId.match(/PI(\d+)/);
                    // Check if this is a command node (contains 'CMD' followed by number)
                    const commandMatch = nodeId.match(/CMD(\d+)/);
                    // Check if this is an auto responder node (contains 'AR' followed by number)
                    const autoResponderMatch = nodeId.match(/AR(\d+)/);
                    // Check if this is a department node (contains 'DEP' followed by number)
                    const departmentMatch = nodeId.match(/DEP(\d+)/);

                    if (triggerMatch) {
                        const triggerId = triggerMatch[1];
                        document.location = WWW_DIR_JAVASCRIPT + `genericbot/bot/<?php echo $bot->id; ?>#!#/trigger-${triggerId}`;
                    } else if (triggerEventMatch) {
                        // For trigger event nodes, extract the trigger ID directly from the node ID
                        const triggerId = triggerEventMatch[1];
                        document.location = WWW_DIR_JAVASCRIPT + `genericbot/bot/<?php echo $bot->id; ?>#!#/trigger-${triggerId}`;
                    } else if (webhookMatch) {
                        const webhookId = webhookMatch[1];
                        window.open(WWW_DIR_JAVASCRIPT + `webhooks/edit/${webhookId}`, '_blank');
                    } else if (themeMatch) {
                        const themeId = themeMatch[1];
                        window.open(WWW_DIR_JAVASCRIPT + `abstract/edit/WidgetTheme/${themeId}`, '_blank');
                    } else if (invitationMatch) {
                        const invitationId = invitationMatch[1];
                        window.open(WWW_DIR_JAVASCRIPT + `abstract/edit/ProactiveChatInvitation/${invitationId}`, '_blank');
                    } else if (commandMatch) {
                        const commandId = commandMatch[1];
                        window.open(WWW_DIR_JAVASCRIPT + `genericbot/editcommand/${commandId}`, '_blank');
                    } else if (autoResponderMatch) {
                        const autoResponderId = autoResponderMatch[1];
                        window.open(WWW_DIR_JAVASCRIPT + `abstract/edit/AutoResponder/${autoResponderId}`, '_blank');
                    } else if (departmentMatch) {
                        const departmentId = departmentMatch[1];
                        window.open(WWW_DIR_JAVASCRIPT + `department/edit/${departmentId}`, '_blank');
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



