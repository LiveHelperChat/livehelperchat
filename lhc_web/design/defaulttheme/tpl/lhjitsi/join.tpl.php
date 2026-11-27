<?php if (empty($jitsi_ready)) : ?>
<div class="lhc-jitsi-shell">
    <div class="lhc-jitsi-state">
        <h2><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Unable to join call'); ?></h2>
        <p><?php echo (string)($error_message ?? erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'This call session is no longer available.')); ?></p>
        <div class="lhc-jitsi-actions">
            <button type="button" class="lhc-jitsi-btn lhc-jitsi-btn-secondary" onclick="<?php if (!empty($is_embedded)) : ?>if(window.parent&&window.parent!==window){window.parent.postMessage({type:'lhc_jitsi_call_ended'},'*');}<?php else : ?>window.close();<?php endif; ?>">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Close window'); ?>
            </button>
        </div>
    </div>
</div>
<?php else : ?>
<?php $isWaitingForOperator = !empty($waiting_for_operator); ?>
<div class="lhc-jitsi-shell">
    <div class="lhc-jitsi-topbar">
        <div class="lhc-jitsi-title">
            <span><?php echo htmlspecialchars((string)$display_name); ?></span>
            <span id="lhc-jitsi-status-badge" class="lhc-jitsi-badge">
                <?php if ($isWaitingForOperator) : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Waiting for operator...'); ?>
                <?php else : ?>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Connecting...'); ?>
                <?php endif; ?>
            </span>
            <span id="lhc-jitsi-timer" class="lhc-jitsi-timer" style="display:none;">00:00</span>
        </div>
        <div>
            <button type="button" id="lhc-jitsi-end-btn" class="lhc-jitsi-btn lhc-jitsi-btn-danger">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'End call'); ?>
            </button>
        </div>
    </div>
    <div id="lhc-jitsi-container" class="lhc-jitsi-stage">
        <div id="lhc-jitsi-loading" class="lhc-jitsi-state">
            <div id="lhc-jitsi-spinner" class="lhc-jitsi-spinner"></div>
            <?php if ($isWaitingForOperator) : ?>
                <h2 id="lhc-jitsi-loading-title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Waiting for operator to join...'); ?></h2>
                <p id="lhc-jitsi-loading-desc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'You will be prompted to continue as soon as the operator joins the call.'); ?></p>
            <?php else : ?>
                <h2 id="lhc-jitsi-loading-title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Connecting to call...'); ?></h2>
                <p id="lhc-jitsi-loading-desc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Please allow microphone or camera access if prompted by your browser.'); ?></p>
            <?php endif; ?>
            <div id="lhc-jitsi-continue-wrap" class="lhc-jitsi-actions" style="display:none;">
                <button type="button" id="lhc-jitsi-continue-btn" class="lhc-jitsi-btn lhc-jitsi-btn-success">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Continue'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var config = <?php echo json_encode([
        'isOperator' => !empty($is_operator),
        'isEmbedded' => !empty($is_embedded),
        'isMinimal' => !empty($is_minimal),
        'chatId' => isset($chat) ? (int)$chat->id : 0,
        'msgId' => isset($msg) ? (int)$msg->id : 0,
        'waitingForOperator' => $isWaitingForOperator,
        'prewarmAudio' => !empty($prewarm_audio),
        'prewarmVideo' => !empty($prewarm_video),
        'domain' => $domain,
        'externalApiUrl' => $external_api_url,
        'directRoomUrl' => $direct_room_url,
        'roomName' => $full_room_name,
        'jwt' => $jwt,
        'displayName' => (string)$display_name,
        'email' => (string)$user_email,
        'configOverwrite' => $client_config['configOverwrite'],
        'interfaceConfigOverwrite' => $client_config['interfaceConfigOverwrite'],
        'statusUrlBase' => $status_url_base,
        'csrfToken' => (string)$csrf_token,
        't' => [
            'operatorJoinedBadge' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Operator joined'),
            'operatorJoinedTitle' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Operator has joined the call'),
            'operatorJoinedDesc' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Click Continue to join the call and enable your microphone.'),
            'connecting' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Connecting...'),
            'connectingTitle' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Connecting to call...'),
            'connectingDesc' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Please allow microphone or camera access if prompted by your browser.'),
            'connected' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Connected'),
            'endedTitle' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Call ended'),
            'endedDesc' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'You can safely close this window and return to the chat.'),
            'closeWindow' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Close window'),
            'loadErrorTitle' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Could not load Jitsi Meet'),
            'loadErrorDesc' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'The embedded Jitsi script could not be loaded. You can try opening the call directly instead.'),
            'openDirectly' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Open call directly'),
        ],
    ], JSON_UNESCAPED_SLASHES); ?>;

    var container = document.getElementById('lhc-jitsi-container');
    var loadingEl = document.getElementById('lhc-jitsi-loading');
    var spinnerEl = document.getElementById('lhc-jitsi-spinner');
    var loadingTitle = document.getElementById('lhc-jitsi-loading-title');
    var loadingDesc = document.getElementById('lhc-jitsi-loading-desc');
    var continueWrap = document.getElementById('lhc-jitsi-continue-wrap');
    var continueBtn = document.getElementById('lhc-jitsi-continue-btn');
    var statusBadge = document.getElementById('lhc-jitsi-status-badge');
    var timerEl = document.getElementById('lhc-jitsi-timer');
    var endBtn = document.getElementById('lhc-jitsi-end-btn');

    var api = null;
    var localParticipantId = null;
    var hasJoined = false;
    var hasEnded = false;
    var hasStartedJitsi = false;
    var operatorReady = false;
    var externalApiScriptLoading = false;
    var timerInterval = null;
    var pollTimer = null;
    var activePollTimer = null;
    var joinedAt = 0;

    function formatTimer(seconds) {
        var mins = Math.floor(seconds / 60);
        var secs = seconds % 60;
        return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
    }

    function startTimer() {
        if (timerInterval) return;
        joinedAt = Math.floor(Date.now() / 1000);
        timerEl.style.display = 'inline';
        timerEl.textContent = '00:00';
        timerInterval = setInterval(function() {
            var elapsed = Math.max(0, Math.floor(Date.now() / 1000) - joinedAt);
            timerEl.textContent = formatTimer(elapsed);
        }, 1000);
    }

    function stopTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    function stopPolling() {
        if (pollTimer) {
            clearTimeout(pollTimer);
            pollTimer = null;
        }
        if (activePollTimer) {
            clearTimeout(activePollTimer);
            activePollTimer = null;
        }
    }

    function notifyParentEnded() {
        try {
            if (config.isEmbedded && window.parent && window.parent !== window) {
                window.parent.postMessage({
                    type: 'lhc_jitsi_call_ended',
                    msg_id: config.msgId,
                    chat_id: config.chatId
                }, '*');
            }
        } catch (e) {}
    }

    function notifyParentState(state, muted) {
        try {
            if (config.isEmbedded && window.parent && window.parent !== window) {
                window.parent.postMessage({
                    type: 'lhc_jitsi_state',
                    msg_id: config.msgId,
                    chat_id: config.chatId,
                    state: state,
                    muted: !!muted
                }, '*');
            }
        } catch (e) {}
    }

    function pollActiveCallStatus() {
        if (hasEnded) return;
        activePollTimer = setTimeout(function() {
            activePollTimer = null;
            if (hasEnded) return;

            fetch(config.statusUrlBase + '/(action)/poll', {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function(res) {
                return res.json();
            }).then(function(data) {
                if (hasEnded) return;
                if (data && (data.ended || data.terminal)) {
                    hasEnded = true;
                    if (api) {
                        try { api.executeCommand('hangup'); } catch (e) {}
                    }
                    setTimeout(function() {
                        showEndedState();
                        notifyParentEnded();
                        try { window.close(); } catch (e) {}
                    }, 150);
                    return;
                }
                pollActiveCallStatus();
            }).catch(function() {
                if (!hasEnded) {
                    pollActiveCallStatus();
                }
            });
        }, 3000);
    }

    function refreshOpenerAdminRow() {
        try {
            if (config.isOperator && config.chatId && config.msgId && window.opener && !window.opener.closed && window.opener.lhinst && typeof window.opener.lhinst.updateMessageRowAdmin === 'function') {
                window.opener.lhinst.updateMessageRowAdmin(config.chatId, config.msgId);
            }
        } catch (e) {}
    }

    function notifyStatus(action, useBeacon, onDone) {
        var url = config.statusUrlBase + '/(action)/' + encodeURIComponent(action);
        var body = new URLSearchParams();
        if (config.csrfToken) {
            body.append('csfr_token', config.csrfToken);
        }

        if (useBeacon && navigator.sendBeacon) {
            try {
                navigator.sendBeacon(url, body);
                setTimeout(refreshOpenerAdminRow, 250);
                if (typeof onDone === 'function') onDone();
                return;
            } catch (e) {}
        }

        var headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
        if (config.csrfToken) {
            headers['X-CSRFToken'] = config.csrfToken;
        }

        try {
            fetch(url, {
                method: 'POST',
                headers: headers,
                body: body.toString(),
                keepalive: true,
                credentials: 'same-origin'
            }).then(function() {
                refreshOpenerAdminRow();
                if (typeof onDone === 'function') onDone();
            }).catch(function() {
                if (typeof onDone === 'function') onDone();
            });
        } catch (e) {
            if (typeof onDone === 'function') onDone();
        }
    }

    function showEndedState() {
        stopPolling();
        stopTimer();
        statusBadge.textContent = config.t.endedTitle;
        statusBadge.classList.remove('active');
        endBtn.style.display = 'none';

        if (api) {
            try { api.dispose(); } catch (e) {}
            api = null;
        }

        container.innerHTML = '';
        var wrap = document.createElement('div');
        wrap.className = 'lhc-jitsi-state';

        var h2 = document.createElement('h2');
        h2.textContent = config.t.endedTitle;
        wrap.appendChild(h2);

        var p = document.createElement('p');
        p.textContent = config.t.endedDesc;
        wrap.appendChild(p);

        var actions = document.createElement('div');
        actions.className = 'lhc-jitsi-actions';
        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'lhc-jitsi-btn lhc-jitsi-btn-secondary';
        closeBtn.textContent = config.t.closeWindow;
        closeBtn.onclick = function() {
            notifyParentEnded();
            try { window.close(); } catch (e) {}
        };
        actions.appendChild(closeBtn);

        wrap.appendChild(actions);
        container.appendChild(wrap);
    }

    function showLoadError() {
        container.innerHTML = '';
        var wrap = document.createElement('div');
        wrap.className = 'lhc-jitsi-state';

        var h2 = document.createElement('h2');
        h2.textContent = config.t.loadErrorTitle;
        wrap.appendChild(h2);

        var p = document.createElement('p');
        p.textContent = config.t.loadErrorDesc;
        wrap.appendChild(p);

        var actions = document.createElement('div');
        actions.className = 'lhc-jitsi-actions';

        if (config.directRoomUrl) {
            var link = document.createElement('a');
            link.href = config.directRoomUrl;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.className = 'lhc-jitsi-btn lhc-jitsi-btn-danger';
            link.textContent = config.t.openDirectly;
            actions.appendChild(link);
        }

        wrap.appendChild(actions);
        container.appendChild(wrap);
    }

    function handleEnd(action) {
        if (hasEnded) return;
        hasEnded = true;
        stopPolling();
        showEndedState();
        notifyStatus(action, false, function() {
            notifyParentEnded();
            try { window.close(); } catch (e) {}
        });
    }

    endBtn.addEventListener('click', function() {
        if (hasEnded) return;
        hasEnded = true;
        endBtn.disabled = true;
        stopPolling();

        if (api) {
            // Operator ends the conference for everyone; visitor hangs up locally
            if (config.isOperator) {
                try { api.executeCommand('endConference'); } catch (e) {}
            }
            try { api.executeCommand('hangup'); } catch (e) {}
        }

        // Allow Jitsi iframe a brief moment to send the endConference/hangup signal before disposing it
        var disposed = false;
        function finalizeClose() {
            if (disposed) return;
            disposed = true;
            showEndedState();
            notifyParentEnded();
            try { window.close(); } catch (e) {}
        }

        var minDelayDone = false;
        var notifyDone = false;
        setTimeout(function() {
            minDelayDone = true;
            if (notifyDone) finalizeClose();
        }, api ? 350 : 0);

        notifyStatus('end', false, function() {
            notifyDone = true;
            if (minDelayDone) finalizeClose();
        });
    });

    window.addEventListener('pagehide', function() {
        stopPolling();
        if (!hasEnded && hasJoined) {
            hasEnded = true;
            notifyStatus('left', true);
        }
    });

    function initJitsi() {
        if (hasEnded) return;
        if (typeof window.JitsiMeetExternalAPI !== 'function') {
            showLoadError();
            return;
        }

        if (loadingEl) {
            loadingEl.remove();
        }

        var options = {
            roomName: config.roomName,
            parentNode: container,
            width: '100%',
            height: '100%',
            userInfo: {
                displayName: config.displayName
            },
            configOverwrite: config.configOverwrite,
            interfaceConfigOverwrite: config.interfaceConfigOverwrite
        };

        if (config.email) {
            options.userInfo.email = config.email;
        }
        if (config.jwt) {
            options.jwt = config.jwt;
        }

        try {
            api = new window.JitsiMeetExternalAPI(config.domain, options);
        } catch (e) {
            showLoadError();
            return;
        }

        api.addListener('videoConferenceJoined', function(evt) {
            hasJoined = true;
            localParticipantId = evt && evt.id ? evt.id : null;
            statusBadge.textContent = config.t.connected;
            statusBadge.classList.add('active');
            startTimer();
            notifyStatus('joined', false);
            pollActiveCallStatus();
            notifyParentState('connected', !!(config.configOverwrite && config.configOverwrite.startWithAudioMuted));

            // Ensure mic/camera are unmuted when config expects them active
            if (config.configOverwrite && config.configOverwrite.startWithAudioMuted === false && typeof api.isAudioMuted === 'function') {
                api.isAudioMuted().then(function(muted) {
                    notifyParentState('connected', !!muted);
                    if (muted && api && !hasEnded) {
                        api.executeCommand('toggleAudio');
                    }
                }).catch(function() {});
            }
            if (config.configOverwrite && config.configOverwrite.startAudioOnly === false && config.configOverwrite.startWithVideoMuted === false && typeof api.isVideoMuted === 'function') {
                api.isVideoMuted().then(function(muted) {
                    if (muted && api && !hasEnded) {
                        api.executeCommand('toggleVideo');
                    }
                }).catch(function() {});
            }
        });

        api.addListener('audioMuteStatusChanged', function(evt) {
            if (hasJoined && !hasEnded) {
                notifyParentState('connected', !!(evt && evt.muted));
            }
        });

        var currentSpeakerId = null;

        api.addListener('dominantSpeakerChanged', function(evt) {
            if (hasJoined && !hasEnded && config.isEmbedded && window.parent && window.parent !== window) {
                var speakerId = evt && evt.id ? evt.id : null;
                currentSpeakerId = speakerId;
                var who = null;
                var speakerName = '';
                if (speakerId) {
                    var isVisitor = !!(localParticipantId && speakerId === localParticipantId);
                    who = isVisitor ? 'visitor' : 'operator';
                    if (api && typeof api.getDisplayName === 'function') {
                        try {
                            speakerName = api.getDisplayName(speakerId) || '';
                        } catch (e) {}
                    }
                }
                try {
                    window.parent.postMessage({
                        type: 'lhc_jitsi_speaking',
                        who: who,
                        speaker_name: speakerName,
                        msg_id: config.msgId,
                        chat_id: config.chatId
                    }, '*');
                } catch (e) {}
            }
        });

        api.addListener('participantLeft', function(evt) {
            if (hasJoined && !hasEnded && config.isEmbedded && window.parent && window.parent !== window) {
                var leftId = evt && evt.id ? evt.id : null;
                if (leftId && currentSpeakerId && leftId === currentSpeakerId) {
                    currentSpeakerId = null;
                    try {
                        window.parent.postMessage({
                            type: 'lhc_jitsi_speaking',
                            who: null,
                            speaker_name: '',
                            msg_id: config.msgId,
                            chat_id: config.chatId
                        }, '*');
                    } catch (e) {}
                }
            }
        });

        api.addListener('videoConferenceLeft', function() {
            handleEnd('left');
        });

        api.addListener('readyToClose', function() {
            handleEnd('left');
        });
    }

    function preloadExternalApiScript() {
        if (typeof window.JitsiMeetExternalAPI === 'function' || externalApiScriptLoading || !config.externalApiUrl) {
            return;
        }
        externalApiScriptLoading = true;
        var script = document.createElement('script');
        script.src = config.externalApiUrl;
        script.async = true;
        script.onload = function() {
            externalApiScriptLoading = false;
            if (hasStartedJitsi && !api && !hasEnded) {
                initJitsi();
            }
        };
        script.onerror = function() {
            externalApiScriptLoading = false;
            if (hasStartedJitsi && !hasEnded) {
                showLoadError();
            }
        };
        document.head.appendChild(script);
    }

    function loadExternalApi() {
        if (hasStartedJitsi || hasEnded) return;
        hasStartedJitsi = true;

        statusBadge.textContent = config.t.connecting;
        if (spinnerEl) spinnerEl.style.display = '';
        if (continueWrap) continueWrap.style.display = 'none';
        if (loadingTitle) loadingTitle.textContent = config.t.connectingTitle;
        if (loadingDesc) loadingDesc.textContent = config.t.connectingDesc;

        if (typeof window.JitsiMeetExternalAPI === 'function') {
            initJitsi();
            return;
        }

        preloadExternalApiScript();
    }

    function showOperatorReadyPrompt() {
        operatorReady = true;
        statusBadge.textContent = config.t.operatorJoinedBadge;
        statusBadge.classList.add('active');
        if (spinnerEl) spinnerEl.style.display = 'none';
        if (loadingTitle) loadingTitle.textContent = config.t.operatorJoinedTitle;
        if (loadingDesc) loadingDesc.textContent = config.t.operatorJoinedDesc;
        if (continueWrap) continueWrap.style.display = 'flex';
        document.title = config.t.operatorJoinedTitle;

        // Preload external_api.js now so clicking Continue mounts Jitsi synchronously inside the click gesture
        preloadExternalApiScript();
    }

    function requestMediaPermissionOnGesture() {
        if ((!config.prewarmAudio && !config.prewarmVideo) || !navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            return Promise.resolve();
        }
        var constraints = {};
        if (config.prewarmAudio) constraints.audio = true;
        if (config.prewarmVideo) constraints.video = true;

        return navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
            try {
                stream.getTracks().forEach(function(track) { track.stop(); });
            } catch (e) {}
        }).catch(function() {});
    }

    if (continueBtn) {
        continueBtn.addEventListener('click', function() {
            if (!operatorReady || hasEnded || hasStartedJitsi) return;
            continueBtn.disabled = true;
            stopPolling();
            // Request mic/camera synchronously inside the click gesture, then mount Jitsi immediately
            requestMediaPermissionOnGesture().then(function() {
                loadExternalApi();
            });
        });
    }

    function pollForOperator() {
        if (hasEnded || hasStartedJitsi) return;

        fetch(config.statusUrlBase + '/(action)/poll', {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store'
        }).then(function(res) {
            return res.json();
        }).then(function(data) {
            if (hasEnded || hasStartedJitsi) return;
            if (data && data.ended) {
                hasEnded = true;
                showEndedState();
                notifyParentEnded();
                return;
            }
            if (data && data.ready && data.jitsi) {
                config.domain = data.jitsi.domain;
                config.externalApiUrl = data.jitsi.externalApiUrl;
                config.directRoomUrl = data.jitsi.directRoomUrl;
                config.roomName = data.jitsi.roomName;
                config.jwt = data.jitsi.jwt;
                config.displayName = data.jitsi.displayName;
                config.email = data.jitsi.email;
                config.configOverwrite = data.jitsi.configOverwrite;
                config.interfaceConfigOverwrite = data.jitsi.interfaceConfigOverwrite;
                if (!operatorReady) {
                    showOperatorReadyPrompt();
                }
                // Keep polling lightly while waiting for Continue click in case operator ends the call
                pollTimer = setTimeout(pollForOperator, 3000);
                return;
            }
            pollTimer = setTimeout(pollForOperator, 2000);
        }).catch(function() {
            if (!hasEnded && !hasStartedJitsi) {
                pollTimer = setTimeout(pollForOperator, 2500);
            }
        });
    }

    window.addEventListener('message', function(event) {
        if (!config.isEmbedded || !event || !event.data || event.data.type !== 'lhc_jitsi_cmd') {
            return;
        }
        if (event.data.msg_id && Number(event.data.msg_id) !== Number(config.msgId)) {
            return;
        }
        if (event.data.cmd === 'toggle_mute' && api && !hasEnded) {
            try { api.executeCommand('toggleAudio'); } catch (e) {}
        } else if (event.data.cmd === 'end' && !hasEnded) {
            endBtn.click();
        }
    });

    if (config.waitingForOperator) {
        pollForOperator();
    } else {
        loadExternalApi();
    }
})();
</script>
<?php endif; ?>
