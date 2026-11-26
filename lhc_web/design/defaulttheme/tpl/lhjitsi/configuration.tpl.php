<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Jitsi Calls Configuration'); ?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php')); ?>
<?php endif; ?>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Settings updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php')); ?>
<?php endif; ?>

<form action="" method="post" ng-non-bindable>

    <div class="card mb-3">
        <div class="card-header fw-bold">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'General'); ?>
        </div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="jitsi-enabled" name="enabled" value="on" <?php if (!empty($jitsi_data['enabled'])) : ?>checked="checked"<?php endif; ?> />
                <label class="form-check-label fw-bold" for="jitsi-enabled">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Enable Jitsi calling'); ?>
                </label>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="jitsi-domain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Jitsi server domain'); ?></label>
                    <input type="text" class="form-control form-control-sm" id="jitsi-domain" name="domain" placeholder="meet.jit.si" value="<?php echo htmlspecialchars((string)($jitsi_data['domain'] ?? 'meet.jit.si')); ?>" />
                    <div class="form-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hostname without https:// (e.g. meet.jit.si, jitsi.yourdomain.com, or 8x8.vc for JaaS).'); ?></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="jitsi-room-prefix"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Room name prefix'); ?></label>
                    <input type="text" class="form-control form-control-sm" id="jitsi-room-prefix" name="room_prefix" placeholder="lhc_" value="<?php echo htmlspecialchars((string)($jitsi_data['room_prefix'] ?? 'lhc_')); ?>" />
                    <div class="form-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Prefix prepended to randomly generated room names (alphanumeric, _ and - only).'); ?></div>
                </div>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="jitsi-confirm-invite" name="confirm_before_invite" value="on" <?php if (!empty($jitsi_data['confirm_before_invite'])) : ?>checked="checked"<?php endif; ?> />
                <label class="form-check-label" for="jitsi-confirm-invite">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Ask operator for confirmation before sending call invitation'); ?>
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="jitsi-auto-join-op" name="auto_join_operator" value="on" <?php if (!empty($jitsi_data['auto_join_operator'])) : ?>checked="checked"<?php endif; ?> />
                <label class="form-check-label" for="jitsi-auto-join-op">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Automatically open Jitsi call window for operator when sending invitation'); ?>
                </label>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-bold">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Call Features & Defaults'); ?>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="jitsi-default-mode"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Default call mode'); ?></label>
                    <select class="form-select form-select-sm" id="jitsi-default-mode" name="default_mode">
                        <option value="video" <?php if (($jitsi_data['default_mode'] ?? 'video') === 'video') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Video call'); ?></option>
                        <option value="audio" <?php if (($jitsi_data['default_mode'] ?? 'video') === 'audio') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Voice call (audio only)'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="jitsi-video-enabled" name="video_enabled" value="on" <?php if (!empty($jitsi_data['video_enabled'])) : ?>checked="checked"<?php endif; ?> />
                <label class="form-check-label" for="jitsi-video-enabled">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Allow video calls (when disabled, all calls are audio-only)'); ?>
                </label>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="jitsi-screenshare-enabled" name="screenshare_enabled" value="on" <?php if (!empty($jitsi_data['screenshare_enabled'])) : ?>checked="checked"<?php endif; ?> />
                <label class="form-check-label" for="jitsi-screenshare-enabled">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Allow screen sharing in Jitsi toolbar'); ?>
                </label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="jitsi-disable-deeplink" name="disable_deep_linking" value="on" <?php if (!empty($jitsi_data['disable_deep_linking'])) : ?>checked="checked"<?php endif; ?> />
                <label class="form-check-label" for="jitsi-disable-deeplink">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Keep mobile users in browser (disable Jitsi native app prompt)'); ?>
                </label>
            </div>

            <hr class="my-3" />

            <div class="row mb-2">
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-semibold" for="jitsi-visitor-call-mode"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Visitor call display mode'); ?></label>
                    <select class="form-select form-select-sm" id="jitsi-visitor-call-mode" name="visitor_call_mode">
                        <option value="popup" <?php if (($jitsi_data['visitor_call_mode'] ?? 'popup') === 'popup') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Popup window / new tab (recommended for multi-page sites)'); ?></option>
                        <option value="widget_audio" <?php if (($jitsi_data['visitor_call_mode'] ?? 'popup') === 'widget_audio') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'In-widget for voice calls, popup for video calls'); ?></option>
                        <option value="widget" <?php if (($jitsi_data['visitor_call_mode'] ?? 'popup') === 'widget') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'In-widget for both voice and video calls'); ?></option>
                    </select>
                    <div class="form-text">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'In-widget calls embed Jitsi inside the chat widget (with a Pop out button). Note: navigating to another page on a multi-page website reloads the widget, and host sites with strict Permissions-Policy headers may block embedded mic/camera.'); ?>
                    </div>
                </div>
            </div>

            <div id="jitsi-widget-embed-dimensions">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="jitsi-widget-audio-minimal" name="widget_audio_minimal" value="on" <?php if (!empty($jitsi_data['widget_audio_minimal'])) : ?>checked="checked"<?php endif; ?> />
                    <label class="form-check-label fw-semibold" for="jitsi-widget-audio-minimal">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Use minimalistic in-card UI for in-widget voice calls'); ?>
                    </label>
                    <div class="form-text mt-0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hides the Jitsi interface completely for voice calls and shows live call status, timer, Mute, and End controls directly inside the chat invitation card.'); ?></div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2" id="jitsi-widget-audio-height-group">
                        <label class="form-label" for="jitsi-widget-audio-height"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Docked voice panel height (px)'); ?></label>
                        <input type="number" min="160" max="420" class="form-control form-control-sm" id="jitsi-widget-audio-height" name="widget_audio_height" value="<?php echo (int)($jitsi_data['widget_audio_height'] ?? 220); ?>" />
                        <div class="form-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Height of the docked top voice call panel when minimalistic in-card UI is disabled.'); ?></div>
                    </div>
                    <div class="col-md-4 mb-2 jitsi-widget-video-dim-group">
                        <label class="form-label" for="jitsi-widget-video-width"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Video call desktop widget width (px)'); ?></label>
                        <input type="number" min="0" max="1600" class="form-control form-control-sm" id="jitsi-widget-video-width" name="widget_video_width" value="<?php echo (int)($jitsi_data['widget_video_width'] ?? 680); ?>" />
                        <div class="form-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Desktop widget auto-expand width during video calls (0 keeps default width).'); ?></div>
                    </div>
                    <div class="col-md-4 mb-2 jitsi-widget-video-dim-group">
                        <label class="form-label" for="jitsi-widget-video-height"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Video call desktop widget height (px)'); ?></label>
                        <input type="number" min="0" max="1200" class="form-control form-control-sm" id="jitsi-widget-video-height" name="widget_video_height" value="<?php echo (int)($jitsi_data['widget_video_height'] ?? 620); ?>" />
                        <div class="form-text"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Desktop widget auto-expand height during video calls (0 keeps default height).'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $availableButtons = erLhcoreClassJitsi::getAvailableToolbarButtons();
    $availableSections = erLhcoreClassJitsi::getAvailableSettingsSections();
    $roleDefinitions = [
        'operator_options' => [
            'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Operator Join Options'),
            'badge' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Moderator'),
            'badge_class' => 'bg-primary',
        ],
        'visitor_options' => [
            'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Visitor Join Options'),
            'badge' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Guest'),
            'badge_class' => 'bg-secondary',
        ],
    ];
    $roleToggleLabels = [
        'skip_prejoin' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Skip pre-join lobby screen (join immediately)'),
        'start_with_audio_muted' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Start with microphone muted'),
        'start_with_video_muted' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Start with camera off'),
        'disable_shortcuts' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Disable keyboard shortcuts'),
        'disable_profile' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Disable profile editing & lock display name'),
        'disable_remote_mute' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Disable muting/kicking other participants'),
        'disable_virtual_background' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Disable virtual backgrounds'),
        'disable_reactions' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Disable reactions and polls'),
        'disable_participants_pane' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hide participants pane and moderator settings tab'),
        'hide_conference_subject' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hide conference title (room name)'),
        'hide_conference_timer' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hide Jitsi built-in call timer'),
        'hide_jitsi_hangup' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hide Jitsi toolbar end call button (use top bar End call only)'),
        'hide_jitsi_logo' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Hide Jitsi logo / watermark (self-hosted Jitsi may also require SHOW_JITSI_WATERMARK=false in server interface_config.js)'),
        'hide_profile_pic' => erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Do not show profile picture (disable Gravatar & hide email)'),
    ];
    ?>

    <div class="card mb-3">
        <div class="card-header fw-bold">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Jitsi UI & Permissions per Participant'); ?>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($roleDefinitions as $roleKey => $roleInfo) : $roleOpts = $jitsi_data[$roleKey] ?? erLhcoreClassJitsi::getDefaultRoleOptions($roleKey === 'operator_options'); ?>
                <div class="col-lg-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h6 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                            <span><?php echo $roleInfo['title']; ?></span>
                            <span class="badge <?php echo $roleInfo['badge_class']; ?>"><?php echo $roleInfo['badge']; ?></span>
                        </h6>

                        <div class="mb-3">
                            <div class="fw-semibold small text-uppercase text-muted mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Toolbar buttons'); ?></div>
                            <div class="row">
                                <?php foreach ($availableButtons as $btnKey => $btnLabel) : $btnId = 'jitsi-' . $roleKey . '-btn-' . $btnKey; ?>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="<?php echo $btnId; ?>" name="<?php echo $roleKey; ?>[toolbar_buttons][]" value="<?php echo htmlspecialchars($btnKey); ?>" <?php if (in_array($btnKey, (array)($roleOpts['toolbar_buttons'] ?? []), true)) : ?>checked="checked"<?php endif; ?> />
                                        <label class="form-check-label small" for="<?php echo $btnId; ?>"><?php echo htmlspecialchars($btnLabel); ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold small text-uppercase text-muted mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Settings dialog tabs'); ?></div>
                            <div class="row">
                                <?php foreach ($availableSections as $secKey => $secLabel) : $secId = 'jitsi-' . $roleKey . '-sec-' . $secKey; ?>
                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="<?php echo $secId; ?>" name="<?php echo $roleKey; ?>[settings_sections][]" value="<?php echo htmlspecialchars($secKey); ?>" <?php if (in_array($secKey, (array)($roleOpts['settings_sections'] ?? []), true)) : ?>checked="checked"<?php endif; ?> />
                                        <label class="form-check-label small" for="<?php echo $secId; ?>"><?php echo htmlspecialchars($secLabel); ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="fw-semibold small text-uppercase text-muted mb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Behavior & restrictions'); ?></div>
                            <?php foreach ($roleToggleLabels as $flagKey => $flagLabel) : $flagId = 'jitsi-' . $roleKey . '-flag-' . $flagKey; ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="<?php echo $flagId; ?>" name="<?php echo $roleKey; ?>[<?php echo $flagKey; ?>]" value="1" <?php if (!empty($roleOpts[$flagKey])) : ?>checked="checked"<?php endif; ?> />
                                <label class="form-check-label small" for="<?php echo $flagId; ?>"><?php echo $flagLabel; ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div>
                            <label class="form-label small fw-semibold text-uppercase text-muted mb-1" for="jitsi-<?php echo $roleKey; ?>-custom-json">
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Custom configOverwrite JSON (optional)'); ?>
                            </label>
                            <textarea rows="3" class="form-control form-control-sm font-monospace" id="jitsi-<?php echo $roleKey; ?>-custom-json" name="<?php echo $roleKey; ?>[custom_config_json]" placeholder='{"hideConferenceSubject": true}'><?php echo htmlspecialchars((string)($roleOpts['custom_config_json'] ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-bold">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Authentication (JWT)'); ?>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label" for="jitsi-jwt-mode"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JWT Authentication Mode'); ?></label>
                    <select class="form-select form-select-sm" id="jitsi-jwt-mode" name="jwt_mode">
                        <option value="none" <?php if (($jitsi_data['jwt_mode'] ?? 'none') === 'none') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'None (public or open Jitsi server)'); ?></option>
                        <option value="hs256" <?php if (($jitsi_data['jwt_mode'] ?? 'none') === 'hs256') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Self-hosted Jitsi JWT (HS256)'); ?></option>
                        <option value="jaas_rs256" <?php if (($jitsi_data['jwt_mode'] ?? 'none') === 'jaas_rs256') : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', '8x8 JaaS (RS256)'); ?></option>
                    </select>
                </div>
                <div class="col-md-3" id="jitsi-jwt-ttl-group">
                    <label class="form-label" for="jitsi-jwt-ttl"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Token TTL (seconds)'); ?></label>
                    <input type="number" min="60" max="86400" class="form-control form-control-sm" id="jitsi-jwt-ttl" name="jwt_ttl" value="<?php echo (int)($jitsi_data['jwt_ttl'] ?? 3600); ?>" />
                </div>
            </div>

            <div id="jitsi-jwt-hs256-fields">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="jitsi-jwt-app-id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JWT App ID'); ?></label>
                        <input type="text" class="form-control form-control-sm" id="jitsi-jwt-app-id" name="jwt_app_id" value="<?php echo htmlspecialchars((string)($jitsi_data['jwt_app_id'] ?? '')); ?>" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="jitsi-jwt-app-secret">
                            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JWT App Secret'); ?>
                            <?php if (!empty($jitsi_data['jwt_app_secret'])) : ?>
                                <span class="badge bg-success ms-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Configured'); ?></span>
                            <?php endif; ?>
                        </label>
                        <input type="password" autocomplete="new-password" class="form-control form-control-sm" id="jitsi-jwt-app-secret" name="jwt_app_secret" placeholder="<?php echo !empty($jitsi_data['jwt_app_secret']) ? erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Leave blank to keep current secret') : ''; ?>" value="" />
                        <?php if (!empty($jitsi_data['jwt_app_secret'])) : ?>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="jitsi-clear-secret" name="clear_jwt_app_secret" value="on" />
                                <label class="form-check-label small text-muted" for="jitsi-clear-secret"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Clear stored secret'); ?></label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="jitsi-jwt-jaas-fields">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="jitsi-jaas-app-id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JaaS App ID (Tenant)'); ?></label>
                        <input type="text" class="form-control form-control-sm" id="jitsi-jaas-app-id" name="jaas_app_id" placeholder="vpaas-magic-cookie-..." value="<?php echo htmlspecialchars((string)($jitsi_data['jaas_app_id'] ?? '')); ?>" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="jitsi-jaas-kid"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JaaS API Key ID (kid)'); ?></label>
                        <input type="text" class="form-control form-control-sm" id="jitsi-jaas-kid" name="jaas_kid" placeholder="vpaas-magic-cookie-.../key-id" value="<?php echo htmlspecialchars((string)($jitsi_data['jaas_kid'] ?? '')); ?>" />
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="jitsi-jaas-private-key">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'JaaS RSA Private Key (PEM)'); ?>
                        <?php if (!empty($jitsi_data['jaas_private_key'])) : ?>
                            <span class="badge bg-success ms-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Configured'); ?></span>
                        <?php endif; ?>
                    </label>
                    <textarea rows="5" class="form-control form-control-sm font-monospace" id="jitsi-jaas-private-key" name="jaas_private_key" placeholder="<?php echo !empty($jitsi_data['jaas_private_key']) ? erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Leave blank to keep current private key') : '-----BEGIN PRIVATE KEY-----'; ?>"></textarea>
                    <?php if (!empty($jitsi_data['jaas_private_key'])) : ?>
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="jitsi-clear-jaas-key" name="clear_jaas_private_key" value="on" />
                            <label class="form-check-label small text-muted" for="jitsi-clear-jaas-key"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/configuration', 'Clear stored private key'); ?></label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php')); ?>

    <input type="submit" class="btn btn-primary btn-sm" name="StoreJitsiConfiguration" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons', 'Save'); ?>" />

</form>

<script>
(function() {
    var modeSelect = document.getElementById('jitsi-jwt-mode');
    var ttlGroup = document.getElementById('jitsi-jwt-ttl-group');
    var hs256Fields = document.getElementById('jitsi-jwt-hs256-fields');
    var jaasFields = document.getElementById('jitsi-jwt-jaas-fields');
    var domainInput = document.getElementById('jitsi-domain');

    function syncJwtVisibility() {
        var mode = modeSelect.value;
        ttlGroup.style.display = (mode === 'none') ? 'none' : '';
        hs256Fields.style.display = (mode === 'hs256') ? '' : 'none';
        jaasFields.style.display = (mode === 'jaas_rs256') ? '' : 'none';
        if (mode === 'jaas_rs256' && (domainInput.value === '' || domainInput.value === 'meet.jit.si')) {
            domainInput.value = '8x8.vc';
        }
    }

    modeSelect.addEventListener('change', syncJwtVisibility);
    syncJwtVisibility();

    var visitorModeSelect = document.getElementById('jitsi-visitor-call-mode');
    var embedDimWrap = document.getElementById('jitsi-widget-embed-dimensions');
    var audioMinimalCheck = document.getElementById('jitsi-widget-audio-minimal');
    var audioHeightGroup = document.getElementById('jitsi-widget-audio-height-group');
    var videoDimGroups = document.querySelectorAll('.jitsi-widget-video-dim-group');

    function syncVisitorModeVisibility() {
        if (!visitorModeSelect || !embedDimWrap) return;
        var mode = visitorModeSelect.value;
        embedDimWrap.style.display = (mode === 'popup') ? 'none' : '';
        if (audioHeightGroup && audioMinimalCheck) {
            audioHeightGroup.style.display = audioMinimalCheck.checked ? 'none' : '';
        }
        videoDimGroups.forEach(function(el) {
            el.style.display = (mode === 'widget') ? '' : 'none';
        });
    }

    if (visitorModeSelect) {
        visitorModeSelect.addEventListener('change', syncVisitorModeVisibility);
        if (audioMinimalCheck) {
            audioMinimalCheck.addEventListener('change', syncVisitorModeVisibility);
        }
        syncVisitorModeVisibility();
    }
})();
</script>
