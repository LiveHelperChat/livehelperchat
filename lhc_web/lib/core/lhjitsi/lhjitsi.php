<?php

class erLhcoreClassJitsi
{
    const STATUS_INVITED  = 'invited';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DENIED   = 'denied';
    const STATUS_CANCELED = 'canceled';
    const STATUS_ENDED    = 'ended';

    const MODE_AUDIO = 'audio';
    const MODE_VIDEO = 'video';

    const JWT_MODE_NONE       = 'none';
    const JWT_MODE_HS256      = 'hs256';
    const JWT_MODE_JAAS_RS256 = 'jaas_rs256';

    const VISITOR_CALL_MODE_POPUP        = 'popup';
    const VISITOR_CALL_MODE_WIDGET_AUDIO = 'widget_audio';
    const VISITOR_CALL_MODE_WIDGET       = 'widget';

    public static function getAvailableToolbarButtons(): array
    {
        return [
            'microphone' => 'Microphone (with audio device picker)',
            'camera' => 'Camera (with video device picker)',
            'desktop' => 'Screen share',
            'hangup' => 'Hang up',
            'fullscreen' => 'Fullscreen',
            'tileview' => 'Tile view',
            'settings' => 'Settings dialog',
            'raisehand' => 'Raise hand',
            'chat' => 'In-call Jitsi chat',
            'participants-pane' => 'Participants list',
            'select-background' => 'Virtual backgrounds',
            'recording' => 'Recording',
        ];
    }

    public static function getAvailableSettingsSections(): array
    {
        return [
            'devices' => 'Devices (Audio & Video)',
            'language' => 'Language / General',
            'profile' => 'Profile',
            'moderator' => 'Moderator',
            'sounds' => 'Sounds & Notifications',
            'more' => 'More / Experimental',
        ];
    }

    public static function getDefaultRoleOptions(bool $isModerator): array
    {
        if ($isModerator) {
            return [
                'toolbar_buttons' => ['microphone', 'camera', 'desktop', 'hangup', 'settings', 'raisehand', 'tileview', 'fullscreen'],
                'settings_sections' => ['devices', 'language', 'profile', 'moderator', 'sounds'],
                'skip_prejoin' => true,
                'start_with_audio_muted' => false,
                'start_with_video_muted' => false,
                'disable_shortcuts' => false,
                'disable_profile' => false,
                'disable_remote_mute' => false,
                'disable_virtual_background' => false,
                'disable_reactions' => false,
                'disable_participants_pane' => false,
                'hide_conference_subject' => true,
                'hide_conference_timer' => false,
                'hide_jitsi_hangup' => true,
                'hide_jitsi_logo' => true,
                'hide_profile_pic' => true,
                'custom_config_json' => '',
            ];
        }

        return [
            'toolbar_buttons' => ['microphone', 'camera', 'desktop', 'fullscreen', 'hangup'],
            'settings_sections' => ['devices'],
            'skip_prejoin' => true,
            'start_with_audio_muted' => false,
            'start_with_video_muted' => false,
            'disable_shortcuts' => true,
            'disable_profile' => true,
            'disable_remote_mute' => true,
            'disable_virtual_background' => true,
            'disable_reactions' => true,
            'disable_participants_pane' => true,
            'hide_conference_subject' => true,
            'hide_conference_timer' => false,
            'hide_jitsi_hangup' => true,
            'hide_jitsi_logo' => true,
            'hide_profile_pic' => true,
            'custom_config_json' => '',
        ];
    }

    public static function getDefaultConfig(): array
    {
        return [
            'enabled' => false,
            'domain' => 'meet.jit.si',
            'room_prefix' => 'lhc_',
            'default_mode' => self::MODE_VIDEO,
            'video_enabled' => true,
            'screenshare_enabled' => true,
            'skip_prejoin' => true,
            'disable_deep_linking' => true,
            'confirm_before_invite' => false,
            'auto_join_operator' => false,
            'visitor_call_mode' => self::VISITOR_CALL_MODE_POPUP,
            'widget_audio_minimal' => true,
            'widget_audio_height' => 220,
            'widget_video_width' => 680,
            'widget_video_height' => 620,
            'jwt_mode' => self::JWT_MODE_NONE,
            'jwt_ttl' => 3600,
            'jwt_app_id' => '',
            'jwt_app_secret' => '',
            'jaas_app_id' => '',
            'jaas_kid' => '',
            'jaas_private_key' => '',
            'operator_options' => self::getDefaultRoleOptions(true),
            'visitor_options' => self::getDefaultRoleOptions(false),
        ];
    }

    public static function getConfig(): array
    {
        $stored = [];
        try {
            $cfg = erLhcoreClassModelChatConfig::fetch('jitsi_configuration');
            if ($cfg instanceof erLhcoreClassModelChatConfig && is_array($cfg->data)) {
                $stored = $cfg->data;
            }
        } catch (Exception $e) {
            $stored = [];
        }

        $defaults = self::getDefaultConfig();
        $merged = array_merge($defaults, $stored);
        $merged['operator_options'] = array_merge(
            $defaults['operator_options'],
            isset($stored['operator_options']) && is_array($stored['operator_options']) ? $stored['operator_options'] : []
        );
        $merged['visitor_options'] = array_merge(
            $defaults['visitor_options'],
            isset($stored['visitor_options']) && is_array($stored['visitor_options']) ? $stored['visitor_options'] : []
        );

        return $merged;
    }

    public static function isEnabled(?array $config = null): bool
    {
        $config = $config ?? self::getConfig();

        if (empty($config['enabled'])) {
            return false;
        }

        $jwtMode = $config['jwt_mode'] ?? self::JWT_MODE_NONE;
        if ($jwtMode === self::JWT_MODE_JAAS_RS256) {
            return trim((string)($config['jaas_app_id'] ?? '')) !== '';
        }

        return self::normalizeDomain((string)($config['domain'] ?? '')) !== '';
    }

    /**
     * Normalizes and validates a Jitsi Meet server domain (optional :port).
     * Returns an empty string when the input is invalid or unsafe.
     */
    public static function normalizeDomain(string $domain): string
    {
        $domain = trim($domain);
        if ($domain === '') {
            return '';
        }

        // Strip scheme if user pasted a full URL
        $domain = preg_replace('#^https?://#i', '', $domain);

        // Reject characters that could break URL/script context or embed credentials/queries
        if (preg_match('/[\s\'"<>\\\\@#?]/', $domain)) {
            return '';
        }

        // Strip any trailing path if user pasted a URL with trailing slash/path
        $slashPos = strpos($domain, '/');
        if ($slashPos !== false) {
            $domain = substr($domain, 0, $slashPos);
        }

        $domain = strtolower(trim($domain));
        if ($domain === '') {
            return '';
        }

        // Strict hostname[:port] validation
        if (!preg_match('/^[a-z0-9](?:[a-z0-9\-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9\-]{0,61}[a-z0-9])?)*(?::([1-9][0-9]{0,4}))?$/', $domain, $matches)) {
            return '';
        }

        if (isset($matches[1]) && (int)$matches[1] > 65535) {
            return '';
        }

        return $domain;
    }

    public static function getEffectiveDomain(array $config): string
    {
        if (($config['jwt_mode'] ?? self::JWT_MODE_NONE) === self::JWT_MODE_JAAS_RS256) {
            $domain = self::normalizeDomain((string)($config['domain'] ?? ''));
            return $domain !== '' ? $domain : '8x8.vc';
        }

        return self::normalizeDomain((string)($config['domain'] ?? ''));
    }

    public static function sanitizePrefix(string $prefix): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9_\-]/', '', $prefix);
        return substr($sanitized, 0, 32);
    }

    public static function generateRoomName(int $chatId, string $prefix = 'lhc_'): string
    {
        $safePrefix = self::sanitizePrefix($prefix);
        $random = bin2hex(random_bytes(8));
        return $safePrefix . $chatId . '_' . $random;
    }

    public static function getFullRoomName(string $room, array $config): string
    {
        $room = preg_replace('/[^A-Za-z0-9_\-]/', '', $room);
        if (($config['jwt_mode'] ?? self::JWT_MODE_NONE) === self::JWT_MODE_JAAS_RS256) {
            $appId = trim((string)($config['jaas_app_id'] ?? ''));
            if ($appId !== '') {
                return trim($appId, '/') . '/' . $room;
            }
        }
        return $room;
    }

    public static function getExternalApiUrl(array $config): string
    {
        $domain = self::getEffectiveDomain($config);
        if ($domain === '') {
            return '';
        }

        if (($config['jwt_mode'] ?? self::JWT_MODE_NONE) === self::JWT_MODE_JAAS_RS256) {
            $appId = trim((string)($config['jaas_app_id'] ?? ''));
            if ($appId !== '') {
                return 'https://' . $domain . '/' . rawurlencode($appId) . '/external_api.js';
            }
        }

        return 'https://' . $domain . '/external_api.js';
    }

    public static function getDirectRoomUrl(string $room, array $config): string
    {
        $domain = self::getEffectiveDomain($config);
        if ($domain === '') {
            return '';
        }
        return 'https://' . $domain . '/' . self::getFullRoomName($room, $config);
    }

    public static function normalizeMode(string $mode, array $config): string
    {
        $videoEnabled = !empty($config['video_enabled']);
        if (!$videoEnabled) {
            return self::MODE_AUDIO;
        }

        if ($mode === self::MODE_AUDIO || $mode === self::MODE_VIDEO) {
            return $mode;
        }

        return ($config['default_mode'] ?? self::MODE_VIDEO) === self::MODE_AUDIO
            ? self::MODE_AUDIO
            : self::MODE_VIDEO;
    }

    public static function shouldUseWidgetForVisitor(array $config, string $callMode): bool
    {
        $visitorCallMode = (string)($config['visitor_call_mode'] ?? self::VISITOR_CALL_MODE_POPUP);
        if ($visitorCallMode === self::VISITOR_CALL_MODE_WIDGET) {
            return true;
        }
        if ($visitorCallMode === self::VISITOR_CALL_MODE_WIDGET_AUDIO) {
            return self::normalizeMode($callMode, $config) === self::MODE_AUDIO;
        }
        return false;
    }

    public static function shouldUseMinimalWidgetAudio(array $config, string $callMode): bool
    {
        return self::shouldUseWidgetForVisitor($config, $callMode)
            && self::normalizeMode($callMode, $config) === self::MODE_AUDIO
            && !empty($config['widget_audio_minimal']);
    }

    public static function shouldHideProfilePic(array $config, bool $isModerator): bool
    {
        $roleKey = $isModerator ? 'operator_options' : 'visitor_options';
        $roleDefaults = self::getDefaultRoleOptions($isModerator);
        $storedRole = isset($config[$roleKey]) && is_array($config[$roleKey]) ? $config[$roleKey] : [];
        $role = array_merge($roleDefaults, $storedRole);
        return !empty($role['hide_profile_pic']);
    }

    public static function buildClientConfig(array $config, string $mode, bool $isModerator = false): array
    {
        $effectiveMode = self::normalizeMode($mode, $config);
        $isAudioOnly = ($effectiveMode === self::MODE_AUDIO);
        $videoEnabled = !empty($config['video_enabled']) && !$isAudioOnly;
        $screenshareEnabled = !empty($config['screenshare_enabled']);
        $disableDeepLinking = !empty($config['disable_deep_linking']);

        $roleKey = $isModerator ? 'operator_options' : 'visitor_options';
        $roleDefaults = self::getDefaultRoleOptions($isModerator);
        $storedRole = isset($config[$roleKey]) && is_array($config[$roleKey]) ? $config[$roleKey] : [];
        $role = array_merge($roleDefaults, $storedRole);

        // Preserve legacy top-level skip_prejoin when per-role key is not explicitly provided
        if (!array_key_exists('skip_prejoin', $storedRole) && array_key_exists('skip_prejoin', $config)) {
            $role['skip_prejoin'] = !empty($config['skip_prejoin']);
        }

        $allowedButtons = array_keys(self::getAvailableToolbarButtons());
        $rawButtons = isset($role['toolbar_buttons']) && is_array($role['toolbar_buttons'])
            ? $role['toolbar_buttons']
            : $roleDefaults['toolbar_buttons'];

        $toolbarButtons = [];
        foreach ($rawButtons as $btn) {
            if (!is_string($btn) || !in_array($btn, $allowedButtons, true)) {
                continue;
            }
            if (!$videoEnabled && in_array($btn, ['camera', 'select-background'], true)) {
                continue;
            }
            if (!$screenshareEnabled && $btn === 'desktop') {
                continue;
            }
            if (!empty($role['disable_virtual_background']) && $btn === 'select-background') {
                continue;
            }
            if (!empty($role['disable_participants_pane']) && $btn === 'participants-pane') {
                continue;
            }
            if (!empty($role['hide_jitsi_hangup']) && $btn === 'hangup') {
                continue;
            }
            if (!in_array($btn, $toolbarButtons, true)) {
                $toolbarButtons[] = $btn;
            }
        }

        $allowedSections = array_keys(self::getAvailableSettingsSections());
        $rawSections = isset($role['settings_sections']) && is_array($role['settings_sections'])
            ? $role['settings_sections']
            : $roleDefaults['settings_sections'];
        $settingsSections = array_values(array_unique(array_filter($rawSections, static function ($sec) use ($allowedSections) {
            return is_string($sec) && in_array($sec, $allowedSections, true);
        })));

        $skipPrejoin = !empty($role['skip_prejoin']);
        $startAudioMuted = !empty($role['start_with_audio_muted']);
        $startVideoMuted = !$videoEnabled || !empty($role['start_with_video_muted']);

        $configOverwrite = [
            'startWithAudioMuted' => $startAudioMuted,
            'startWithVideoMuted' => $startVideoMuted,
            'startAudioOnly' => $isAudioOnly,
            'startSilent' => false,
            'disableInitialGUM' => false,
            'prejoinPageEnabled' => !$skipPrejoin,
            'prejoinConfig' => ['enabled' => !$skipPrejoin],
            'disableDeepLinking' => $disableDeepLinking,
            'toolbarButtons' => $toolbarButtons,
            'disableInviteFunctions' => true,
        ];

        $hideJitsiLogo = !empty($role['hide_jitsi_logo']);
        $interfaceConfigOverwrite = [
            'SHOW_JITSI_WATERMARK' => !$hideJitsiLogo,
            'SHOW_WATERMARK_FOR_GUESTS' => !$hideJitsiLogo,
            'SHOW_BRAND_WATERMARK' => !$hideJitsiLogo,
            'SHOW_POWERED_BY' => !$hideJitsiLogo,
            'HIDE_DEEP_LINKING_LOGO' => $hideJitsiLogo,
            'MOBILE_APP_PROMO' => !$disableDeepLinking,
            'SETTINGS_SECTIONS' => $settingsSections,
        ];

        if ($hideJitsiLogo) {
            $configOverwrite['defaultLogoUrl'] = '';
        }

        if (!empty($role['hide_profile_pic'])) {
            $configOverwrite['gravatar'] = ['disabled' => true];
            $configOverwrite['disableThirdPartyRequests'] = true;
        }

        if (!$videoEnabled || !empty($role['disable_virtual_background'])) {
            $configOverwrite['disableVideoBackground'] = true;
            $configOverwrite['disableAddingBackgroundImages'] = true;
        }

        if (!empty($role['disable_shortcuts'])) {
            $configOverwrite['disableShortcuts'] = true;
        }

        if (!empty($role['disable_profile'])) {
            $configOverwrite['disableProfile'] = true;
            $configOverwrite['readOnlyName'] = true;
            $configOverwrite['hideEmailInSettings'] = true;
            $configOverwrite['disableSelfViewSettings'] = true;
        }

        if (!empty($role['disable_remote_mute'])) {
            $configOverwrite['disableRemoteMute'] = true;
            $configOverwrite['remoteVideoMenu'] = [
                'disabled' => true,
                'disableKick' => true,
                'disableGrantModerator' => true,
                'disablePrivateChat' => true,
            ];
        }

        if (!empty($role['disable_reactions'])) {
            $configOverwrite['disableReactions'] = true;
            $configOverwrite['disablePolls'] = true;
        }

        if (!empty($role['disable_participants_pane'])) {
            $configOverwrite['participantsPane'] = [
                'enabled' => false,
                'hideModeratorSettingsTab' => true,
                'hideMoreActionsButton' => true,
                'hideMuteAllButton' => true,
            ];
        }

        if (!empty($role['hide_conference_subject'])) {
            $configOverwrite['hideConferenceSubject'] = true;
        }

        if (!empty($role['hide_conference_timer'])) {
            $configOverwrite['hideConferenceTimer'] = true;
        }

        if (!$isModerator) {
            $interfaceConfigOverwrite['DISABLE_JOIN_LEAVE_NOTIFICATIONS'] = true;
        }

        $customJson = trim((string)($role['custom_config_json'] ?? ''));
        if ($customJson !== '') {
            $decoded = json_decode($customJson, true);
            if (is_array($decoded) && !array_is_list($decoded)) {
                $configOverwrite = array_replace_recursive($configOverwrite, $decoded);
            }
        }

        return [
            'configOverwrite' => $configOverwrite,
            'interfaceConfigOverwrite' => $interfaceConfigOverwrite,
        ];
    }

    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Generates a Jitsi JWT when jwt_mode is hs256 or jaas_rs256.
     * Returns null when jwt_mode is none.
     *
     * @throws RuntimeException when JWT configuration is incomplete or signing fails.
     */
    public static function generateJwt(array $config, string $room, array $participant, bool $isModerator = false, ?int $now = null): ?string
    {
        $jwtMode = $config['jwt_mode'] ?? self::JWT_MODE_NONE;
        if ($jwtMode === self::JWT_MODE_NONE) {
            return null;
        }

        $now = $now ?? time();
        $ttl = max(60, min(86400, (int)($config['jwt_ttl'] ?? 3600)));

        $userContext = [
            'id' => (string)($participant['id'] ?? ''),
            'name' => (string)($participant['name'] ?? ($isModerator ? 'Operator' : 'Visitor')),
        ];
        if (!empty($participant['email'])) {
            $userContext['email'] = (string)$participant['email'];
        }
        if (!empty($participant['avatar'])) {
            $userContext['avatar'] = (string)$participant['avatar'];
        }

        if ($jwtMode === self::JWT_MODE_HS256) {
            $appId = trim((string)($config['jwt_app_id'] ?? ''));
            $appSecret = (string)($config['jwt_app_secret'] ?? '');
            $domain = self::getEffectiveDomain($config);

            if ($appId === '' || $appSecret === '' || $domain === '') {
                throw new RuntimeException('Jitsi HS256 JWT configuration is incomplete.');
            }

            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT',
            ];

            $payload = [
                'aud' => $appId,
                'iss' => $appId,
                'sub' => $domain,
                'room' => $room,
                'iat' => $now,
                'nbf' => $now - 10,
                'exp' => $now + $ttl,
                'moderator' => $isModerator,
                'context' => [
                    'user' => $userContext,
                ],
            ];

            $encodedHeader = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
            $encodedPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
            $signature = hash_hmac('sha256', $encodedHeader . '.' . $encodedPayload, $appSecret, true);

            return $encodedHeader . '.' . $encodedPayload . '.' . self::base64UrlEncode($signature);
        }

        if ($jwtMode === self::JWT_MODE_JAAS_RS256) {
            $jaasAppId = trim((string)($config['jaas_app_id'] ?? ''));
            $jaasKid = trim((string)($config['jaas_kid'] ?? ''));
            $privateKeyPem = trim((string)($config['jaas_private_key'] ?? ''));

            if ($jaasAppId === '' || $jaasKid === '' || $privateKeyPem === '') {
                throw new RuntimeException('Jitsi JaaS RS256 JWT configuration is incomplete.');
            }

            $userContext['moderator'] = $isModerator;

            $header = [
                'alg' => 'RS256',
                'typ' => 'JWT',
                'kid' => $jaasKid,
            ];

            $payload = [
                'aud' => 'jitsi',
                'iss' => 'chat',
                'sub' => $jaasAppId,
                'room' => $room,
                'iat' => $now,
                'nbf' => $now - 10,
                'exp' => $now + $ttl,
                'context' => [
                    'user' => $userContext,
                    'features' => [
                        'livestreaming' => false,
                        'recording' => false,
                        'outbound-call' => false,
                        'transcription' => false,
                    ],
                ],
            ];

            $encodedHeader = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
            $encodedPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
            $signingInput = $encodedHeader . '.' . $encodedPayload;

            $privateKey = openssl_pkey_get_private($privateKeyPem);
            if ($privateKey === false) {
                throw new RuntimeException('Invalid Jitsi JaaS RSA private key.');
            }

            $signature = '';
            $ok = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            if (!$ok) {
                throw new RuntimeException('Failed to sign Jitsi JaaS JWT.');
            }

            return $signingInput . '.' . self::base64UrlEncode($signature);
        }

        throw new RuntimeException('Unsupported Jitsi JWT mode: ' . $jwtMode);
    }

    /**
     * Pure deterministic call state transition engine.
     *
     * @param array  $jitsiMeta Current meta_msg['content']['jitsi'] array
     * @param string $event     accept | deny | cancel | joined | left | end
     * @param string $actor     operator | visitor
     * @param int|null $now     Optional unix timestamp for deterministic testing
     * @return array{meta: array, changed: bool, error: ?string}
     */
    public static function applyStateTransition(array $jitsiMeta, string $event, string $actor, ?int $now = null): array
    {
        $now = $now ?? time();
        $meta = array_merge([
            'room' => '',
            'mode' => self::MODE_VIDEO,
            'status' => self::STATUS_INVITED,
            'op_joined' => false,
            'vi_joined' => false,
            'invited_at' => $now,
            'accepted_at' => 0,
            'started_at' => 0,
            'ended_at' => 0,
            'ended_by' => '',
            'operator_name' => '',
        ], $jitsiMeta);

        $status = $meta['status'];
        $isTerminal = in_array($status, [self::STATUS_DENIED, self::STATUS_CANCELED, self::STATUS_ENDED], true);

        switch ($event) {
            case 'accept':
                if ($actor !== 'visitor') {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Only visitor can accept a call invitation.'];
                }
                if ($status === self::STATUS_ACCEPTED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => null];
                }
                if ($status !== self::STATUS_INVITED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Call invitation is no longer active.'];
                }
                $meta['status'] = self::STATUS_ACCEPTED;
                $meta['accepted_at'] = $now;
                return ['meta' => $meta, 'changed' => true, 'error' => null];

            case 'deny':
                if ($actor !== 'visitor') {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Only visitor can decline a call invitation.'];
                }
                if ($status === self::STATUS_DENIED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => null];
                }
                if ($status !== self::STATUS_INVITED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Call invitation cannot be declined in its current state.'];
                }
                $meta['status'] = self::STATUS_DENIED;
                $meta['op_joined'] = false;
                $meta['vi_joined'] = false;
                $meta['ended_at'] = $now;
                $meta['ended_by'] = 'visitor';
                return ['meta' => $meta, 'changed' => true, 'error' => null];

            case 'cancel':
                if ($actor !== 'operator') {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Only operator can cancel a call invitation.'];
                }
                if ($status === self::STATUS_CANCELED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => null];
                }
                if ($status !== self::STATUS_INVITED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Call invitation cannot be canceled in its current state.'];
                }
                $meta['status'] = self::STATUS_CANCELED;
                $meta['op_joined'] = false;
                $meta['vi_joined'] = false;
                $meta['ended_at'] = $now;
                $meta['ended_by'] = 'operator';
                return ['meta' => $meta, 'changed' => true, 'error' => null];

            case 'joined':
                if ($isTerminal) {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Call has already ended.'];
                }
                $changed = false;
                if ($actor === 'visitor') {
                    if (empty($meta['op_joined'])) {
                        return ['meta' => $meta, 'changed' => false, 'error' => 'Operator must join the call before visitor can join.'];
                    }
                    if ($status === self::STATUS_INVITED) {
                        $meta['status'] = self::STATUS_ACCEPTED;
                        $meta['accepted_at'] = $now;
                        $changed = true;
                    }
                    if (empty($meta['vi_joined'])) {
                        $meta['vi_joined'] = true;
                        $changed = true;
                    }
                } elseif ($actor === 'operator') {
                    if (empty($meta['op_joined'])) {
                        $meta['op_joined'] = true;
                        $changed = true;
                    }
                } else {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Invalid actor.'];
                }

                if ($meta['status'] === self::STATUS_ACCEPTED && empty($meta['started_at'])) {
                    $meta['started_at'] = $now;
                    $changed = true;
                }
                return ['meta' => $meta, 'changed' => $changed, 'error' => null];

            case 'left':
                if ($isTerminal) {
                    return ['meta' => $meta, 'changed' => false, 'error' => null];
                }
                $changed = false;
                if ($actor === 'visitor' && !empty($meta['vi_joined'])) {
                    $meta['vi_joined'] = false;
                    $changed = true;
                } elseif ($actor === 'operator' && !empty($meta['op_joined'])) {
                    $meta['op_joined'] = false;
                    $changed = true;
                }

                // End the accepted call once no participants remain in the room
                if ($meta['status'] === self::STATUS_ACCEPTED && empty($meta['op_joined']) && empty($meta['vi_joined'])) {
                    $meta['status'] = self::STATUS_ENDED;
                    $meta['ended_at'] = $now;
                    $meta['ended_by'] = $actor;
                    $changed = true;
                }
                return ['meta' => $meta, 'changed' => $changed, 'error' => null];

            case 'end':
                if ($status === self::STATUS_ENDED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => null];
                }
                if ($status === self::STATUS_DENIED || $status === self::STATUS_CANCELED) {
                    return ['meta' => $meta, 'changed' => false, 'error' => 'Call is already closed.'];
                }
                if ($status === self::STATUS_INVITED && $actor === 'visitor') {
                    // Visitor ending an invited call acts as declining it
                    $meta['status'] = self::STATUS_DENIED;
                } elseif ($status === self::STATUS_INVITED && $actor === 'operator') {
                    $meta['status'] = self::STATUS_CANCELED;
                } else {
                    $meta['status'] = self::STATUS_ENDED;
                }
                $meta['op_joined'] = false;
                $meta['vi_joined'] = false;
                $meta['ended_at'] = $now;
                $meta['ended_by'] = $actor;
                return ['meta' => $meta, 'changed' => true, 'error' => null];

            default:
                return ['meta' => $meta, 'changed' => false, 'error' => 'Unknown call event.'];
        }
    }

    public static function canVisitorJoinRoom(array $jitsiMeta): bool
    {
        return ($jitsiMeta['status'] ?? '') === self::STATUS_ACCEPTED
            && !empty($jitsiMeta['op_joined']);
    }

    public static function getDurationSeconds(array $jitsiMeta, ?int $now = null): int
    {
        $start = !empty($jitsiMeta['started_at']) ? (int)$jitsiMeta['started_at'] : (!empty($jitsiMeta['accepted_at']) ? (int)$jitsiMeta['accepted_at'] : 0);
        if ($start <= 0) {
            return 0;
        }

        $end = !empty($jitsiMeta['ended_at']) ? (int)$jitsiMeta['ended_at'] : ($now ?? time());
        return max(0, $end - $start);
    }

    public static function getValidChatStatuses($chat): array
    {
        $validStatuses = [
            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            erLhcoreClassModelChat::STATUS_BOT_CHAT,
        ];

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat', [
            'chat' => & $chat,
            'valid_statuses' => & $validStatuses,
        ]);

        return $validStatuses;
    }

    public static function isVisitorChatValid($chat, string $hash, bool $allowClosed = false): bool
    {
        if (!($chat instanceof erLhcoreClassModelChat)) {
            return false;
        }

        if ($hash === '' || !hash_equals((string)$chat->hash, $hash)) {
            return false;
        }

        if ($allowClosed) {
            return true;
        }

        if ((int)$chat->status === (int)erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            return false;
        }

        $validStatuses = array_map('intval', self::getValidChatStatuses($chat));
        return in_array((int)$chat->status, $validStatuses, true)
            && !in_array((int)$chat->status_sub, [
                erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED,
                erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT,
                erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,
                erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM,
            ], true);
    }

    /**
     * Persists updated Jitsi call metadata on a chat message inside the active DB transaction.
     * Call dispatchCallUpdated() AFTER $db->commit() so listeners read committed state.
     */
    public static function updateCallMessage(
        erLhcoreClassModelChat $chat,
        erLhcoreClassModelmsg $message,
        array $newJitsiMeta,
        bool $notifyVisitor = true,
        bool $notifyAdmin = true
    ): void {
        $metaMessage = $message->meta_msg_array;
        if (!is_array($metaMessage)) {
            $metaMessage = [];
        }

        $metaMessage['content']['jitsi'] = $newJitsiMeta;
        $message->meta_msg_array = $metaMessage;
        $message->meta_msg = json_encode($metaMessage);
        $message->updateThis(['update' => ['meta_msg']]);

        $chatUpdates = [];

        if ($notifyVisitor) {
            $opCmd = "lhinst.updateMessageRow({$message->id});\n";
            if (strpos((string)$chat->operation, $opCmd) === false) {
                $chat->operation .= $opCmd;
            }
            $chatUpdates[] = 'operation';
        }

        if ($notifyAdmin) {
            $opAdminCmd = "lhinst.updateMessageRowAdmin({$chat->id},{$message->id});\n";
            if (strpos((string)$chat->operation_admin, $opAdminCmd) === false) {
                $chat->operation_admin .= $opAdminCmd;
            }
            $chatUpdates[] = 'operation_admin';
        }

        if (!empty($chatUpdates)) {
            $chat->updateThis(['update' => $chatUpdates]);
        }
    }

    public static function dispatchCallUpdated(erLhcoreClassModelChat $chat, erLhcoreClassModelmsg $message): void
    {
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.message_updated', [
            'chat' => & $chat,
            'msg' => $message,
        ]);

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.added_operation', [
            'chat' => & $chat,
        ]);
    }

    public static function getStateKey(array $jitsiMeta): string
    {
        return (string)($jitsiMeta['status'] ?? self::STATUS_INVITED)
            . ':' . (!empty($jitsiMeta['op_joined']) ? '1' : '0')
            . ':' . (!empty($jitsiMeta['vi_joined']) ? '1' : '0');
    }
}
