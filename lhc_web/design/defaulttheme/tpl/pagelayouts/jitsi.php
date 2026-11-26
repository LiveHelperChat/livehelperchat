<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars((string)erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'content_language')); ?>" dir="<?php echo htmlspecialchars((string)erConfigClassLhConfig::getInstance()->getOverrideValue('site', 'dir_language')); ?>">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="robots" content="noindex, nofollow" />
    <title><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('jitsi/call', 'Jitsi Call'); ?> &laquo; <?php echo htmlspecialchars((string)erLhcoreClassModelChatConfig::fetch('application_name')->current_value); ?></title>
    <link rel="icon" type="image/png" href="<?php echo erLhcoreClassDesign::design('images/favicon.ico'); ?>" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            height: 100dvh;
            overflow: hidden;
            background: #111827;
            color: #f9fafb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .lhc-jitsi-shell {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            height: 100dvh;
        }
        .lhc-jitsi-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 16px;
            background: #1f2937;
            border-bottom: 1px solid #374151;
            flex-shrink: 0;
        }
        .lhc-jitsi-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            min-width: 0;
        }
        .lhc-jitsi-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
            background: #374151;
            color: #e5e7eb;
            white-space: nowrap;
        }
        .lhc-jitsi-badge.active {
            background: #065f46;
            color: #d1fae5;
        }
        .lhc-jitsi-timer {
            font-variant-numeric: tabular-nums;
            font-size: 13px;
            color: #9ca3af;
        }
        .lhc-jitsi-btn {
            border: 0;
            border-radius: 6px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .lhc-jitsi-btn-danger {
            background: #dc2626;
            color: #ffffff;
        }
        .lhc-jitsi-btn-danger:hover {
            background: #b91c1c;
        }
        .lhc-jitsi-btn-success {
            background: #16a34a;
            color: #ffffff;
            padding: 11px 28px;
            font-size: 15px;
        }
        .lhc-jitsi-btn-success:hover {
            background: #15803d;
        }
        .lhc-jitsi-btn-secondary {
            background: #374151;
            color: #f9fafb;
        }
        .lhc-jitsi-btn-secondary:hover {
            background: #4b5563;
        }
        .lhc-jitsi-stage {
            position: relative;
            flex: 1 1 auto;
            width: 100%;
            min-height: 0;
            background: #000000;
        }
        .lhc-jitsi-stage iframe {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
        }
        .lhc-jitsi-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
            padding: 24px;
            box-sizing: border-box;
            gap: 14px;
        }
        .lhc-jitsi-state h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .lhc-jitsi-state p {
            margin: 0;
            color: #9ca3af;
            max-width: 440px;
            font-size: 14px;
            line-height: 1.5;
        }
        .lhc-jitsi-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 6px;
        }
        .lhc-jitsi-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #374151;
            border-top-color: #10b981;
            border-radius: 50%;
            animation: lhc-jitsi-spin 0.9s linear infinite;
        }
        @keyframes lhc-jitsi-spin {
            to { transform: rotate(360deg); }
        }
        body.lhc-jitsi-embedded .lhc-jitsi-topbar {
            padding: 6px 10px;
            gap: 8px;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-title {
            font-size: 12px;
            gap: 6px;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-btn {
            padding: 5px 10px;
            font-size: 12px;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-btn-success {
            padding: 8px 20px;
            font-size: 13px;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-state {
            padding: 12px;
            gap: 8px;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-state h2 {
            font-size: 15px;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-state p {
            font-size: 12px;
            line-height: 1.35;
        }
        body.lhc-jitsi-embedded .lhc-jitsi-spinner {
            width: 26px;
            height: 26px;
        }
    </style>
</head>
<body<?php if (!empty($Result['is_embedded'])) : ?> class="lhc-jitsi-embedded"<?php endif; ?>>
    <?php echo $Result['content']; ?>
</body>
</html>
