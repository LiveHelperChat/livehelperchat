<?php if ($ext == 'jitsi-call') : $jitsiCfg = erLhcoreClassJitsi::getConfig(); ?>
(function () {
    if (window.lhcJitsiWidgetInitialized) {
        return;
    }
    window.lhcJitsiWidgetInitialized = true;

    var baseOrigin = <?php echo json_encode(erLhcoreClassSystem::getHost()); ?>;
    var joinBase = baseOrigin + <?php echo json_encode(erLhcoreClassDesign::baseurl('jitsi/join')); ?>;
    var acceptBase = baseOrigin + <?php echo json_encode(erLhcoreClassDesign::baseurl('jitsi/accept')); ?>;
    var denyBase = baseOrigin + <?php echo json_encode(erLhcoreClassDesign::baseurl('jitsi/deny')); ?>;
    var statusBase = baseOrigin + <?php echo json_encode(erLhcoreClassDesign::baseurl('jitsi/status')); ?>;

    var embedCfg = <?php echo json_encode([
        'visitorCallMode' => (string)($jitsiCfg['visitor_call_mode'] ?? erLhcoreClassJitsi::VISITOR_CALL_MODE_POPUP),
        'videoEnabled' => !empty($jitsiCfg['video_enabled']),
        'audioMinimal' => !empty($jitsiCfg['widget_audio_minimal']),
        'audioHeight' => (int)($jitsiCfg['widget_audio_height'] ?? 220),
        'videoWidth' => (int)($jitsiCfg['widget_video_width'] ?? 680),
        'videoHeight' => (int)($jitsiCfg['widget_video_height'] ?? 620),
        't' => [
            'voiceCall' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Voice Call'),
            'videoCall' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Video Call'),
            'minimize' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Minimize'),
            'expand' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Expand'),
            'chat' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Chat'),
            'popOut' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Pop out'),
            'connecting' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Connecting...'),
            'inCall' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'In call'),
            'mute' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Mute'),
            'unmute' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Unmute'),
            'muted' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Muted'),
            'endCall' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'End call'),
            'speaking' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Speaking'),
            'you' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'You'),
            'operator' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/visitor', 'Operator'),
        ],
    ], JSON_UNESCAPED_SLASHES); ?>;

    var monitors = {};
    var activeEmbed = null;
    var activeMinimal = null;
    var widgetExpanded = false;
    var lastRefreshCtx = null;

    function isMobileBrowser() {
        return (window.innerWidth && window.innerWidth <= 768) || /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent || '');
    }

    function normalizeCallMode(mode) {
        if (!embedCfg.videoEnabled) {
            return 'audio';
        }
        return mode === 'audio' ? 'audio' : 'video';
    }

    function shouldEmbedInWidget(mode) {
        var effectiveMode = normalizeCallMode(mode);
        if (embedCfg.visitorCallMode === 'widget') {
            return true;
        }
        if (embedCfg.visitorCallMode === 'widget_audio') {
            return effectiveMode === 'audio';
        }
        return false;
    }

    function shouldUseMinimalAudio(mode) {
        return shouldEmbedInWidget(mode) && normalizeCallMode(mode) === 'audio' && embedCfg.audioMinimal === true;
    }

    function formatTimer(seconds) {
        var mins = Math.floor(seconds / 60);
        var secs = seconds % 60;
        return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
    }

    function openPopupWindow(joinUrl, chatId) {
        if (isMobileBrowser()) {
            window.open(joinUrl, '_blank');
        } else {
            window.open(joinUrl, 'lhc_jitsi_call_' + chatId, 'scrollbars=yes,menubar=0,resizable=1,width=960,height=680');
        }
    }

    function ensureEmbedStyles() {
        if (document.getElementById('lhc-jitsi-embed-style')) {
            return;
        }
        var style = document.createElement('style');
        style.id = 'lhc-jitsi-embed-style';
        style.textContent =
            'body.lhc-jitsi-body-docked { display: flex !important; flex-direction: column !important; height: 100% !important; overflow: hidden !important; }' +
            'body.lhc-jitsi-body-docked #root { min-height: 0 !important; flex: 1 1 auto !important; overflow: hidden !important; }' +
            '#lhc-jitsi-embed-panel { background: #111827; color: #f9fafb; display: flex; flex-direction: column; overflow: hidden; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }' +
            '#lhc-jitsi-embed-panel.lhc-jitsi-embed-docked { position: relative; width: 100%; height: ' + embedCfg.audioHeight + 'px; flex: 0 0 auto; border-bottom: 1px solid #374151; z-index: 20; }' +
            '#lhc-jitsi-embed-panel.lhc-jitsi-embed-overlay { position: fixed; inset: 0; width: 100%; height: 100%; z-index: 99999; }' +
            '#lhc-jitsi-embed-panel.lhc-jitsi-embed-video-chat { position: relative; width: 100%; height: 165px; flex: 0 0 auto; border-bottom: 1px solid #374151; z-index: 20; }' +
            '#lhc-jitsi-embed-panel.lhc-jitsi-embed-minimized { height: 34px !important; }' +
            '#lhc-jitsi-embed-panel.lhc-jitsi-embed-minimized .lhc-jitsi-embed-frame-wrap { height: 0 !important; opacity: 0; pointer-events: none; overflow: hidden; flex: 0 0 0; }' +
            '.lhc-jitsi-embed-bar { display: flex; align-items: center; justify-content: space-between; gap: 6px; padding: 4px 8px; height: 34px; background: #0f172a; border-bottom: 1px solid #1e293b; flex-shrink: 0; font-size: 12px; }' +
            '.lhc-jitsi-embed-bar-title { font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }' +
            '.lhc-jitsi-embed-bar-actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }' +
            '.lhc-jitsi-embed-bar-btn { border: 1px solid #334155; background: #1e293b; color: #f8fafc; border-radius: 4px; padding: 2px 7px; font-size: 11px; line-height: 1.4; cursor: pointer; }' +
            '.lhc-jitsi-embed-bar-btn:hover { background: #334155; }' +
            '.lhc-jitsi-embed-frame-wrap { flex: 1 1 auto; min-height: 0; width: 100%; position: relative; background: #000; }' +
            '.lhc-jitsi-embed-frame-wrap iframe { width: 100%; height: 100%; border: 0; display: block; }' +
            '#lhc-jitsi-minimal-host { position: fixed; left: -10px; top: -10px; width: 1px; height: 1px; opacity: 0; pointer-events: none; overflow: hidden; }';
        document.head.appendChild(style);
    }

    function sendWidgetResize(payload) {
        try {
            if (window.lhcHelperfunctions && typeof window.lhcHelperfunctions.sendMessageParent === 'function') {
                window.lhcHelperfunctions.sendMessageParent('widgetHeight', [payload]);
            }
        } catch (e) {}
    }

    function expandWidgetForVideo() {
        if (isMobileBrowser() || (window.lhcChat && window.lhcChat['mode'] && window.lhcChat['mode'] !== 'widget')) {
            return;
        }
        var dims = {};
        if (embedCfg.videoWidth > 0) dims.force_width = embedCfg.videoWidth;
        if (embedCfg.videoHeight > 0) dims.force_height = embedCfg.videoHeight;
        if (dims.force_width || dims.force_height) {
            sendWidgetResize(dims);
            widgetExpanded = true;
        }
    }

    function restoreWidgetSize() {
        if (widgetExpanded) {
            widgetExpanded = false;
            sendWidgetResize({reset_height: true});
        }
    }

    function closeEmbeddedCall(restoreSize) {
        var panel = document.getElementById('lhc-jitsi-embed-panel');
        if (panel && panel.parentNode) {
            panel.parentNode.removeChild(panel);
        }
        document.body.classList.remove('lhc-jitsi-body-docked');
        activeEmbed = null;
        if (restoreSize !== false) {
            restoreWidgetSize();
        }
    }

    // -------------------------------------------------------------------------
    // Minimalistic In-Card Voice Call Engine (hidden 1x1 iframe outside #root)
    // -------------------------------------------------------------------------
    function clearMinimalLiveDom(msgId) {
        if (!msgId) return;
        var titleLive = document.getElementById('jitsi-minimal-title-live-' + msgId);
        if (titleLive) {
            titleLive.textContent = '';
            titleLive.style.display = 'none';
        }
        var titleServer = document.getElementById('jitsi-minimal-title-server-' + msgId);
        if (titleServer) {
            titleServer.style.display = '';
        }
        var liveEl = document.getElementById('jitsi-minimal-live-' + msgId);
        if (liveEl) {
            liveEl.innerHTML = '';
        }
        var serverEl = document.getElementById('jitsi-minimal-server-' + msgId);
        if (serverEl) {
            serverEl.style.display = '';
        }
        var inviteActions = document.getElementById('jitsi-invite-actions-' + msgId);
        if (inviteActions) {
            inviteActions.style.display = '';
        }
    }

    function closeMinimalAudio() {
        var prevMsgId = activeMinimal ? activeMinimal.msgId : null;
        if (activeMinimal && activeMinimal.timerInterval) {
            clearInterval(activeMinimal.timerInterval);
        }
        var host = document.getElementById('lhc-jitsi-minimal-host');
        if (host && host.parentNode) {
            host.parentNode.removeChild(host);
        }
        activeMinimal = null;
        clearMinimalLiveDom(prevMsgId);
    }

    function updateMinimalSpeakerUI() {
        if (!activeMinimal) return;
        var titleLive = document.getElementById('jitsi-minimal-title-live-' + activeMinimal.msgId);
        var titleServer = document.getElementById('jitsi-minimal-title-server-' + activeMinimal.msgId);
        if (!titleLive || !titleServer) return;

        if (activeMinimal.state === 'connected' && activeMinimal.dominantSpeaker) {
            titleLive.innerHTML = '';
            var spkIcon = document.createElement('i');
            spkIcon.className = 'material-icons fs14 me-1 align-text-bottom text-success';
            spkIcon.innerHTML = '&#xf102;';
            titleLive.appendChild(spkIcon);
            titleLive.appendChild(document.createTextNode(embedCfg.t.speaking + ': ' + activeMinimal.dominantSpeaker));
            titleLive.style.display = '';
            titleServer.style.display = 'none';
        } else {
            titleLive.textContent = '';
            titleLive.style.display = 'none';
            titleServer.style.display = '';
        }
    }

    function sendMinimalCmd(cmd) {
        if (!activeMinimal || !activeMinimal.iframe || !activeMinimal.iframe.contentWindow) {
            return;
        }
        try {
            activeMinimal.iframe.contentWindow.postMessage({
                type: 'lhc_jitsi_cmd',
                msg_id: activeMinimal.msgId,
                cmd: cmd
            }, '*');
        } catch (e) {}
    }

    function renderMinimalCard() {
        if (!activeMinimal) return;
        var liveEl = document.getElementById('jitsi-minimal-live-' + activeMinimal.msgId);
        if (!liveEl) return;

        // Hide React-owned markup via CSS only (never remove React child nodes!)
        var serverEl = document.getElementById('jitsi-minimal-server-' + activeMinimal.msgId);
        if (serverEl) {
            serverEl.style.display = 'none';
        }
        var inviteActions = document.getElementById('jitsi-invite-actions-' + activeMinimal.msgId);
        if (inviteActions) {
            inviteActions.style.display = 'none';
        }

        liveEl.innerHTML = '';

        var statusBox = document.createElement('div');
        statusBox.className = (activeMinimal.state === 'connected' ? 'bg-success text-white' : 'bg-primary text-white') + ' rounded p-2 text-center fs13 mb-2';

        if (activeMinimal.state === 'connected') {
            var statusIcon = document.createElement('i');
            statusIcon.className = 'material-icons fs14 me-1';
            statusIcon.innerHTML = activeMinimal.muted ? '&#xf101;' : '&#xf10b;';
            statusBox.appendChild(statusIcon);

            var labelSpan = document.createElement('span');
            labelSpan.textContent = (activeMinimal.muted ? embedCfg.t.muted : embedCfg.t.inCall) + ' · ';
            statusBox.appendChild(labelSpan);

            var timerSpan = document.createElement('span');
            timerSpan.id = 'jitsi-minimal-timer-' + activeMinimal.msgId;
            var elapsed = activeMinimal.connectedAt > 0 ? Math.max(0, Math.floor(Date.now() / 1000) - activeMinimal.connectedAt) : 0;
            timerSpan.textContent = formatTimer(elapsed);
            statusBox.appendChild(timerSpan);
        } else {
            var spinIcon = document.createElement('i');
            spinIcon.className = 'material-icons lhc-spin fs14 me-1';
            spinIcon.innerHTML = '&#xf113;';
            statusBox.appendChild(spinIcon);
            statusBox.appendChild(document.createTextNode(embedCfg.t.connecting));
        }

        var btnRow = document.createElement('div');
        btnRow.className = 'd-flex gap-2 justify-content-center';

        if (activeMinimal.state === 'connected') {
            var muteBtn = document.createElement('button');
            muteBtn.type = 'button';
            muteBtn.className = activeMinimal.muted ? 'btn btn-sm btn-warning flex-fill' : 'btn btn-sm btn-outline-secondary flex-fill';
            var muteIcon = document.createElement('i');
            muteIcon.className = 'material-icons fs14 me-1';
            muteIcon.innerHTML = activeMinimal.muted ? '&#xf101;' : '&#xf10b;';
            muteBtn.appendChild(muteIcon);
            muteBtn.appendChild(document.createTextNode(activeMinimal.muted ? embedCfg.t.unmute : embedCfg.t.mute));
            muteBtn.addEventListener('click', function () {
                sendMinimalCmd('toggle_mute');
            });
            btnRow.appendChild(muteBtn);
        }

        var endBtn = document.createElement('button');
        endBtn.type = 'button';
        endBtn.className = 'btn btn-sm btn-danger flex-fill';
        var endIcon = document.createElement('i');
        endIcon.className = 'material-icons fs14 me-1';
        endIcon.innerHTML = '&#xf10a;';
        endBtn.appendChild(endIcon);
        endBtn.appendChild(document.createTextNode(embedCfg.t.endCall));
        endBtn.addEventListener('click', function () {
            endBtn.disabled = true;
            var endedMsgId = activeMinimal ? activeMinimal.msgId : null;
            var endedChatId = activeMinimal ? activeMinimal.chatId : null;
            var endedHash = activeMinimal ? activeMinimal.hash : null;
            stopMonitor(endedMsgId);
            sendMinimalCmd('end');
            window.lhcAxios.post(
                statusBase + '/' + endedChatId + '/' + encodeURIComponent(endedHash) + '/' + endedMsgId + '/(action)/end',
                {},
                {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
            ).then(function () {
                closeMinimalAudio();
                if (lastRefreshCtx) {
                    refreshMessage(endedMsgId, endedChatId, endedHash, lastRefreshCtx.dispatch, lastRefreshCtx.getState, lastRefreshCtx.updateMessage);
                }
            }).catch(function () {
                closeMinimalAudio();
                if (lastRefreshCtx) {
                    refreshMessage(endedMsgId, endedChatId, endedHash, lastRefreshCtx.dispatch, lastRefreshCtx.getState, lastRefreshCtx.updateMessage);
                }
            });
        });
        btnRow.appendChild(endBtn);

        liveEl.appendChild(statusBox);
        liveEl.appendChild(btnRow);
        updateMinimalSpeakerUI();
    }

    function requestMicPermissionGesture() {
        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            return Promise.resolve();
        }
        return navigator.mediaDevices.getUserMedia({audio: true}).then(function (stream) {
            try {
                stream.getTracks().forEach(function (track) { track.stop(); });
            } catch (e) {}
        }).catch(function () {});
    }

    function startMinimalAudio(chatId, hash, msgId) {
        if (activeMinimal && String(activeMinimal.msgId) === String(msgId)) {
            renderMinimalCard();
            return;
        }

        closeEmbeddedCall(true);
        closeMinimalAudio();
        ensureEmbedStyles();

        var minimalUrl = joinBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId + '/(embed)/1/(minimal)/1';

        activeMinimal = {
            chatId: chatId,
            hash: hash,
            msgId: msgId,
            state: 'connecting',
            muted: false,
            dominantSpeaker: '',
            dominantSpeakerWho: '',
            connectedAt: 0,
            iframe: null,
            timerInterval: null
        };

        renderMinimalCard();

        // Keep monitoring call status in the widget so operator-initiated end updates the card reliably
        if (lastRefreshCtx) {
            startMonitor(msgId, chatId, hash, '', lastRefreshCtx.dispatch, lastRefreshCtx.getState, lastRefreshCtx.updateMessage);
        }

        requestMicPermissionGesture().then(function () {
            if (!activeMinimal || String(activeMinimal.msgId) !== String(msgId)) {
                return;
            }
            var host = document.createElement('div');
            host.id = 'lhc-jitsi-minimal-host';

            var iframe = document.createElement('iframe');
            iframe.src = minimalUrl;
            iframe.setAttribute('allow', 'microphone; autoplay');
            host.appendChild(iframe);
            document.body.appendChild(host);

            activeMinimal.iframe = iframe;
            activeMinimal.timerInterval = setInterval(function () {
                if (!activeMinimal) return;
                if (activeMinimal.state === 'connected') {
                    var timerEl = document.getElementById('jitsi-minimal-timer-' + activeMinimal.msgId);
                    if (!timerEl) {
                        renderMinimalCard();
                    } else {
                        var elapsed = activeMinimal.connectedAt > 0 ? Math.max(0, Math.floor(Date.now() / 1000) - activeMinimal.connectedAt) : 0;
                        timerEl.textContent = formatTimer(elapsed);
                    }
                } else {
                    var liveEl = document.getElementById('jitsi-minimal-live-' + activeMinimal.msgId);
                    if (liveEl && liveEl.childNodes.length === 0) {
                        renderMinimalCard();
                    }
                }
            }, 1000);
        });
    }

    // -------------------------------------------------------------------------
    // Full/Docked Embedded Panel (Video overlay or non-minimal Voice panel)
    // -------------------------------------------------------------------------
    function openEmbeddedCall(chatId, hash, msgId, mode) {
        var effectiveMode = normalizeCallMode(mode);
        var joinUrl = joinBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId;
        var embedUrl = joinUrl + '/(embed)/1';

        if (activeEmbed && String(activeEmbed.msgId) === String(msgId)) {
            var existing = document.getElementById('lhc-jitsi-embed-panel');
            if (existing) {
                if (effectiveMode === 'video') {
                    existing.className = 'lhc-jitsi-embed-overlay';
                    document.body.classList.remove('lhc-jitsi-body-docked');
                    if (activeEmbed.toggleBtn) activeEmbed.toggleBtn.textContent = embedCfg.t.chat;
                    activeEmbed.minimized = false;
                } else {
                    existing.classList.remove('lhc-jitsi-embed-minimized');
                    if (activeEmbed.toggleBtn) activeEmbed.toggleBtn.textContent = embedCfg.t.minimize;
                    activeEmbed.minimized = false;
                }
                return;
            }
        }

        closeMinimalAudio();
        closeEmbeddedCall(true);
        ensureEmbedStyles();

        var panel = document.createElement('div');
        panel.id = 'lhc-jitsi-embed-panel';

        var bar = document.createElement('div');
        bar.className = 'lhc-jitsi-embed-bar';

        var title = document.createElement('span');
        title.className = 'lhc-jitsi-embed-bar-title';
        title.textContent = effectiveMode === 'video' ? embedCfg.t.videoCall : embedCfg.t.voiceCall;

        var actions = document.createElement('div');
        actions.className = 'lhc-jitsi-embed-bar-actions';

        var toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'lhc-jitsi-embed-bar-btn';
        toggleBtn.textContent = effectiveMode === 'video' ? embedCfg.t.chat : embedCfg.t.minimize;

        var popOutBtn = document.createElement('button');
        popOutBtn.type = 'button';
        popOutBtn.className = 'lhc-jitsi-embed-bar-btn';
        popOutBtn.textContent = embedCfg.t.popOut;

        actions.appendChild(toggleBtn);
        actions.appendChild(popOutBtn);
        bar.appendChild(title);
        bar.appendChild(actions);

        var frameWrap = document.createElement('div');
        frameWrap.className = 'lhc-jitsi-embed-frame-wrap';

        var iframe = document.createElement('iframe');
        iframe.src = embedUrl;
        iframe.setAttribute('allow', 'camera; microphone; display-capture; fullscreen; autoplay');
        iframe.setAttribute('allowfullscreen', 'true');
        frameWrap.appendChild(iframe);

        panel.appendChild(bar);
        panel.appendChild(frameWrap);

        if (effectiveMode === 'video') {
            panel.className = 'lhc-jitsi-embed-overlay';
            document.body.appendChild(panel);
            expandWidgetForVideo();
        } else {
            panel.className = 'lhc-jitsi-embed-docked';
            document.body.classList.add('lhc-jitsi-body-docked');
            var rootEl = document.getElementById('root');
            if (rootEl && rootEl.parentNode === document.body) {
                document.body.insertBefore(panel, rootEl);
            } else {
                document.body.insertBefore(panel, document.body.firstChild);
            }
        }

        activeEmbed = {
            chatId: chatId,
            hash: hash,
            msgId: msgId,
            mode: effectiveMode,
            minimized: false,
            toggleBtn: toggleBtn
        };

        toggleBtn.addEventListener('click', function () {
            if (!activeEmbed) return;
            if (activeEmbed.mode === 'video') {
                if (!activeEmbed.minimized) {
                    activeEmbed.minimized = true;
                    panel.className = 'lhc-jitsi-embed-video-chat';
                    document.body.classList.add('lhc-jitsi-body-docked');
                    var rootEl2 = document.getElementById('root');
                    if (rootEl2 && rootEl2.parentNode === document.body) {
                        document.body.insertBefore(panel, rootEl2);
                    }
                    toggleBtn.textContent = embedCfg.t.expand;
                } else {
                    activeEmbed.minimized = false;
                    document.body.classList.remove('lhc-jitsi-body-docked');
                    panel.className = 'lhc-jitsi-embed-overlay';
                    document.body.appendChild(panel);
                    toggleBtn.textContent = embedCfg.t.chat;
                }
            } else {
                activeEmbed.minimized = !activeEmbed.minimized;
                if (activeEmbed.minimized) {
                    panel.classList.add('lhc-jitsi-embed-minimized');
                    toggleBtn.textContent = embedCfg.t.expand;
                } else {
                    panel.classList.remove('lhc-jitsi-embed-minimized');
                    toggleBtn.textContent = embedCfg.t.minimize;
                }
            }
        });

        popOutBtn.addEventListener('click', function () {
            openPopupWindow(joinUrl, chatId);
            closeEmbeddedCall(true);
        });
    }

    function disableInviteButtons(msgId) {
        var acceptBtn = document.getElementById('jitsi-accept-btn-' + msgId);
        var denyBtn = document.getElementById('jitsi-deny-btn-' + msgId);
        if (acceptBtn) acceptBtn.disabled = true;
        if (denyBtn) denyBtn.disabled = true;
    }

    function refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage) {
        if (typeof updateMessage === 'function') {
            updateMessage({'msg_id': msgId, 'id': chatId, 'hash': hash})(dispatch, getState);
        }
    }

    function stopMonitor(msgId) {
        if (monitors[msgId]) {
            clearTimeout(monitors[msgId]);
            delete monitors[msgId];
        }
    }

    function startMonitor(msgId, chatId, hash, stateKey, dispatch, getState, updateMessage) {
        stopMonitor(msgId);
        monitors[msgId] = setTimeout(function () {
            delete monitors[msgId];
            var el = document.getElementById('jitsi-widget-status-' + msgId);
            if (!el) {
                return;
            }
            var currentKey = el.getAttribute('data-state-key') || stateKey || '';

            window.lhcAxios.get(
                statusBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId + '/(action)/poll'
            ).then(function (response) {
                if (!document.getElementById('jitsi-widget-status-' + msgId)) {
                    return;
                }
                var data = response && response.data ? response.data : null;
                if (data && !data.error) {
                    if (data.ended) {
                        if (activeEmbed && String(activeEmbed.msgId) === String(msgId)) {
                            closeEmbeddedCall(true);
                        }
                        if (activeMinimal && String(activeMinimal.msgId) === String(msgId)) {
                            closeMinimalAudio();
                        }
                    }
                    if (data.state_key && data.state_key !== currentKey) {
                        // Avoid clobbering live minimal controls unless call ended
                        if (!data.ended && activeMinimal && String(activeMinimal.msgId) === String(msgId)) {
                            el.setAttribute('data-state-key', data.state_key);
                            startMonitor(msgId, chatId, hash, data.state_key, dispatch, getState, updateMessage);
                            return;
                        }
                        refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
                        return;
                    }
                    if (!data.ended) {
                        startMonitor(msgId, chatId, hash, currentKey, dispatch, getState, updateMessage);
                    }
                }
            }).catch(function () {
                if (document.getElementById('jitsi-widget-status-' + msgId)) {
                    startMonitor(msgId, chatId, hash, currentKey, dispatch, getState, updateMessage);
                }
            });
        }, 2500);
    }

    window.addEventListener('message', function (event) {
        if (!event || !event.data) {
            return;
        }
        if (event.data.type === 'lhc_jitsi_state') {
            if (activeMinimal && (!event.data.msg_id || String(activeMinimal.msgId) === String(event.data.msg_id))) {
                var wasConnected = activeMinimal.state === 'connected';
                activeMinimal.state = event.data.state || 'connected';
                activeMinimal.muted = !!event.data.muted;
                if (activeMinimal.muted && activeMinimal.dominantSpeakerWho === 'visitor') {
                    activeMinimal.dominantSpeaker = '';
                    activeMinimal.dominantSpeakerWho = '';
                }
                if (activeMinimal.state === 'connected' && !wasConnected) {
                    activeMinimal.connectedAt = Math.floor(Date.now() / 1000);
                }
                renderMinimalCard();
            }
            return;
        }
        if (event.data.type === 'lhc_jitsi_speaking') {
            if (activeMinimal && (!event.data.msg_id || String(activeMinimal.msgId) === String(event.data.msg_id))) {
                if (!event.data.who) {
                    activeMinimal.dominantSpeaker = '';
                    activeMinimal.dominantSpeakerWho = '';
                } else {
                    activeMinimal.dominantSpeakerWho = event.data.who;
                    activeMinimal.dominantSpeaker = event.data.who === 'visitor'
                        ? embedCfg.t.you
                        : (event.data.speaker_name || embedCfg.t.operator);
                }
                updateMinimalSpeakerUI();
            }
            return;
        }
        if (event.data.type === 'lhc_jitsi_call_ended') {
            var endedId = event.data.msg_id || (activeMinimal ? activeMinimal.msgId : (activeEmbed ? activeEmbed.msgId : null));
            closeEmbeddedCall(true);
            closeMinimalAudio();
            if (lastRefreshCtx) {
                var msgId = endedId || lastRefreshCtx.msgId;
                refreshMessage(msgId, lastRefreshCtx.chatId, lastRefreshCtx.hash, lastRefreshCtx.dispatch, lastRefreshCtx.getState, lastRefreshCtx.updateMessage);
            }
        }
    });

    window.lhcHelperfunctions.eventEmitter.addListener('jitsi-call.init', function (params, dispatch, getState, updateMessage) {
        if (!params || !params.method || params.method === 'preload') {
            return;
        }

        var state = getState();
        var chatId = state.chatwidget.getIn(['chatData', 'id']);
        var hash = state.chatwidget.getIn(['chatData', 'hash']);
        var msgId = params.msg_id;

        if (!chatId || !hash || !msgId) {
            return;
        }

        lastRefreshCtx = {
            chatId: chatId,
            hash: hash,
            msgId: msgId,
            dispatch: dispatch,
            getState: getState,
            updateMessage: updateMessage
        };

        if (params.method === 'monitor') {
            if (activeMinimal && String(activeMinimal.msgId) === String(msgId)) {
                renderMinimalCard();
            }
            startMonitor(msgId, chatId, hash, params.state_key || '', dispatch, getState, updateMessage);
            return;
        }

        if (params.method === 'continue_minimal') {
            startMinimalAudio(chatId, hash, msgId);
            return;
        }

        if (params.method === 'open') {
            var openMode = params.mode || 'video';
            if (shouldUseMinimalAudio(openMode)) {
                startMinimalAudio(chatId, hash, msgId);
            } else if (shouldEmbedInWidget(openMode)) {
                openEmbeddedCall(chatId, hash, msgId, openMode);
            } else {
                var directUrl = joinBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId;
                openPopupWindow(directUrl, chatId);
            }
            return;
        }

        if (params.method === 'accept') {
            stopMonitor(msgId);
            disableInviteButtons(msgId);
            var callMode = params.mode || 'video';

            if (shouldUseMinimalAudio(callMode)) {
                var opAlreadyJoined = params.op_joined === true;
                if (opAlreadyJoined) {
                    // Operator already in room: request mic inside Accept click gesture and start immediately
                    startMinimalAudio(chatId, hash, msgId);
                }
                window.lhcAxios.post(
                    acceptBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId,
                    {},
                    {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
                ).then(function () {
                    if (!opAlreadyJoined) {
                        refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
                    }
                }).catch(function () {
                    if (!opAlreadyJoined) {
                        refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
                    }
                });
                return;
            }

            if (shouldEmbedInWidget(callMode)) {
                openEmbeddedCall(chatId, hash, msgId, callMode);
            } else {
                var joinUrl = joinBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId;
                openPopupWindow(joinUrl, chatId);
            }

            window.lhcAxios.post(
                acceptBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId,
                {},
                {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
            ).then(function () {
                refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
            }).catch(function () {
                refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
            });
            return;
        }

        if (params.method === 'deny') {
            stopMonitor(msgId);
            disableInviteButtons(msgId);
            window.lhcAxios.post(
                denyBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId,
                {},
                {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
            ).then(function () {
                refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
            }).catch(function () {
                refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
            });
            return;
        }

        if (params.method === 'end') {
            stopMonitor(msgId);
            if (activeEmbed && String(activeEmbed.msgId) === String(msgId)) {
                closeEmbeddedCall(true);
            }
            if (activeMinimal && String(activeMinimal.msgId) === String(msgId)) {
                sendMinimalCmd('end');
                closeMinimalAudio();
            }
            window.lhcAxios.post(
                statusBase + '/' + chatId + '/' + encodeURIComponent(hash) + '/' + msgId + '/(action)/end',
                {},
                {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}
            ).then(function () {
                refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
            }).catch(function () {
                refreshMessage(msgId, chatId, hash, dispatch, getState, updateMessage);
            });
        }
    });
})();
<?php $extHandled = true; endif; ?>
