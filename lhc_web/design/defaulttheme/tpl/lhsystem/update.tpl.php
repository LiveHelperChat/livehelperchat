<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Update');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
	<div class="col-md-4">
		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Your version')?> - <?php echo sprintf("%0.2f", erLhcoreClassUpdate::LHC_RELEASE/100);?></h5>
		<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Current version')?> - <span class="text-success" id="recent-version">...</span></h5>

        <p class="fw-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information')?></p>

        <ul>
            <li><a rel="noreferrer" href="http://livehelperchat.com/news-5c.html" target="_blank"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','News')?></a></li>
            <li><a target="_blank" rel="noreferrer" href="https://doc.livehelperchat.com/">Documentation</a></li>
            <li><a target="_blank" rel="noreferrer" href="https://doc.livehelperchat.com/docs/upgrading"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Upgrade instructions')?></a></li>
            <li><a target="_blank" rel="noreferrer" href="http://livehelperchat.com">Live Helper Chat official website</a></li>
        </ul>

	</div>
	<div class="col-md-8">
        <button type="button" class="btn btn-secondary btn-xs" onclick="compareLocal()">Click to compare with local definition</button>
		<div id="status-db"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Comparing current database structure, please wait...')?></div>
	</div>
</div>

<?php
// Security checks are rendered as a full width table below the database status
$lhSecBadge = function ($level, $text) {
    return '<span class="badge bg-' . $level . ' me-1">' . $text . '</span>';
};

$lhSecLink = function ($url, $text, $external = false) {
    return '<a class="btn btn-xs btn-outline-secondary" href="' . $url . '"' . ($external ? ' target="_blank" rel="noreferrer"' : '') . '>' . $text . '</a>';
};

$securityRows = array();
$lhSecAdd = function ($level, $name, $status, $actions = '', $rowId = '') use (&$securityRows) {
    $securityRows[] = array('level' => $level, 'name' => $name, 'status' => $status, 'actions' => $actions, 'id' => $rowId);
};

// Strong secret hash
$lhSecOk = strlen((string)erConfigClassLhConfig::getInstance()->getSetting('site', 'secrethash')) >= 50;
$lhSecStatus = $lhSecBadge($lhSecOk ? 'success' : 'danger', $lhSecOk ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Strong') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Weak'));
$lhSecAdd($lhSecOk ? 'success' : 'danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Strong secret hash'), $lhSecStatus, $lhSecLink('https://doc.livehelperchat.com/docs/security#secret-hash', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information'), true));

// Strong export hash. Empty hash means export is disabled, which is fine.
$lhSecExportHash = (string)erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;
$lhSecOk = $lhSecExportHash === '' || strlen($lhSecExportHash) >= 50;
$lhSecStatus = $lhSecExportHash === '' ? $lhSecBadge('success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','not set')) . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Export hash is not set, chats export is not possible') : $lhSecBadge($lhSecOk ? 'success' : 'danger', $lhSecOk ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Strong') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Weak'));
$lhSecAdd($lhSecOk ? 'success' : 'danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Strong export hash'), $lhSecStatus, $lhSecLink('https://doc.livehelperchat.com/docs/security#export-hash', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information'), true));

// Trusted host
$lhSecTrustedHosts = erConfigClassLhConfig::getInstance()->getSetting('site', 'trusted_host_patterns', false);
$lhSecOk = is_array($lhSecTrustedHosts) && !empty($lhSecTrustedHosts);
$lhSecStatus = $lhSecBadge($lhSecOk ? 'success' : 'danger', $lhSecOk ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','set') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','not set'));
$lhSecAdd($lhSecOk ? 'success' : 'danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Trusted host'), $lhSecStatus, $lhSecLink('https://doc.livehelperchat.com/docs/security#trusted-hosts-and-site-address', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information'), true));

// Site address
$lhSecSiteAddress = (string)erConfigClassLhConfig::getInstance()->getSetting('site', 'site_address', false);
$lhSecOk = $lhSecSiteAddress !== '';
$lhSecStatus = $lhSecOk ? $lhSecBadge('success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','set') . ' - ' . htmlspecialchars($lhSecSiteAddress)) : $lhSecBadge('danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','not set'));
$lhSecAdd($lhSecOk ? 'success' : 'danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Site address'), $lhSecStatus, $lhSecLink('https://doc.livehelperchat.com/docs/security#trusted-hosts-and-site-address', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information'), true));

// Auto login
$lhSecStrongAutologin = $security_check['autologin_secret_length'] >= 32;
$lhSecStatus = $lhSecBadge($security_check['autologin_enabled'] ? 'warning' : 'success', $security_check['autologin_enabled'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Enabled') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Disabled'));
if ($security_check['autologin_enabled']) {
    $lhSecStatus .= $lhSecStrongAutologin ? $lhSecBadge('success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Strong secret hash')) : $lhSecBadge('danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Weak secret hash'));
    if ($security_check['autologin_options'] > 0) {
        $lhSecStatus .= $lhSecBadge('secondary', (int)$security_check['autologin_options'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','predefined login links'));
    }
}
$lhSecAdd(!$security_check['autologin_enabled'] ? 'success' : ($lhSecStrongAutologin ? 'warning' : 'danger'), erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Auto login'), $lhSecStatus, $lhSecLink(erLhcoreClassDesign::baseurl('user/autologinconfig'), erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Configure')));

// Debug output
$lhSecStatus = $lhSecBadge($security_check['debug_output'] ? 'danger' : 'success', $security_check['debug_output'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Enabled') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Disabled'));
$lhSecAdd($security_check['debug_output'] ? 'danger' : 'success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Debug output'), $lhSecStatus, $lhSecLink('https://doc.livehelperchat.com/docs/debug', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information'), true));

// HTTPS
$lhSecStatus = $lhSecBadge($security_check['https'] ? 'success' : 'danger', $security_check['https'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Yes') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','No'));
$lhSecAdd($security_check['https'] ? 'success' : 'danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Site address uses HTTPS'), $lhSecStatus, $lhSecLink('https://doc.livehelperchat.com/docs/security#trusted-hosts-and-site-address', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','More information'), true));

// iFrame embedding
$lhSecStatus = $lhSecBadge($security_check['iframe_enabled'] ? 'warning' : 'success', $security_check['iframe_enabled'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Enabled') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Disabled'));
if ($security_check['iframe_enabled']) {
    $lhSecStatus .= $lhSecBadge('secondary', $security_check['iframe_domain'] != '' ? htmlspecialchars($security_check['iframe_domain']) : 'frame-ancestors \'self\'');
}
$lhSecAdd($security_check['iframe_enabled'] ? 'warning' : 'success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','iFrame embedding'), $lhSecStatus);

// Password policy
if ($security_check['password_length'] < 8 || $security_check['password_max_attempts'] <= 0) {
    $lhSecPasswordLevel = 'danger';
} elseif ($security_check['password_expires_in'] <= 0 || !$security_check['login_ip_restriction']) {
    $lhSecPasswordLevel = 'warning';
} else {
    $lhSecPasswordLevel = 'success';
}
$lhSecStatus = $lhSecBadge($security_check['password_length'] >= 8 ? 'success' : 'danger', (int)$security_check['password_length'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','characters'));
$lhSecStatus .= $security_check['password_max_attempts'] > 0 ? $lhSecBadge('success', (int)$security_check['password_max_attempts'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','attempts')) : $lhSecBadge('danger', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','no attempt limit'));
$lhSecStatus .= $security_check['password_expires_in'] > 0 ? $lhSecBadge('success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','expires in') . ' ' . (int)$security_check['password_expires_in'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','days')) : $lhSecBadge('warning', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','never expires'));
$lhSecStatus .= $lhSecBadge($security_check['login_ip_restriction'] ? 'success' : 'warning', $security_check['login_ip_restriction'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','IP restriction set') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','no IP restriction'));
$lhSecAdd($lhSecPasswordLevel, erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Password policy'), $lhSecStatus, $lhSecLink(erLhcoreClassDesign::baseurl('user/passwordrequirements'), erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Configure')));

// One login per account
$lhSecStatus = $lhSecBadge($security_check['one_login_per_account'] ? 'success' : 'warning', $security_check['one_login_per_account'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Enabled') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Disabled'));
$lhSecAdd($security_check['one_login_per_account'] ? 'success' : 'warning', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','One login per account'), $lhSecStatus);

// Back office URL
$lhSecStatus = $lhSecBadge($security_check['custom_admin_url'] ? 'success' : 'warning', $security_check['custom_admin_url'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','custom') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','default (site_admin)'));
$lhSecAdd($security_check['custom_admin_url'] ? 'success' : 'warning', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Back office URL'), $lhSecStatus);

// Administrators count. An administrator reachable by an autologin link is a risk, the check fails in that case.
$lhSecAdminCount = (int)$security_check['admin_operators'];
$lhSecAdminAutologin = isset($security_check['admin_autologin_allowed']) ? (int)$security_check['admin_autologin_allowed'] : 0;
$lhSecStatus = $lhSecBadge($lhSecAdminCount > 0 ? 'info' : 'warning', $lhSecAdminCount . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','administrators'));
if ($lhSecAdminAutologin > 0) {
    $lhSecStatus .= $lhSecBadge('warning', $lhSecAdminAutologin . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','with autologin enabled'));
}
$lhSecAdd($lhSecAdminCount > 0 && $lhSecAdminAutologin == 0 ? 'success' : 'warning', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Administrators'), $lhSecStatus, $lhSecLink(erLhcoreClassDesign::baseurl('user/userlist'), erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Configure')));

// Rest API. Users behind an active key have to be limited to API key login with autologin disabled, otherwise the check fails.
$lhSecRestApiKeyUsersUnrestricted = isset($security_check['rest_api_key_users_unrestricted']) ? (int)$security_check['rest_api_key_users_unrestricted'] : 0;
$lhSecStatus = $security_check['rest_api_keys'] > 0 ? $lhSecBadge('info', (int)$security_check['rest_api_keys'] . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','active keys')) : $lhSecBadge('success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','no active keys'));
$lhSecStatus .= $lhSecBadge($security_check['rest_api_by_user_disabled'] ? 'success' : 'warning', $security_check['rest_api_by_user_disabled'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','API keys only') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','username/password login allowed'));
if ($lhSecRestApiKeyUsersUnrestricted > 0) {
    $lhSecStatus .= $lhSecBadge('warning', $lhSecRestApiKeyUsersUnrestricted . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','API key users can still login with password or autologin'));
}
$lhSecAdd($security_check['rest_api_by_user_disabled'] && $lhSecRestApiKeyUsersUnrestricted == 0 ? 'success' : 'warning', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Rest API'), $lhSecStatus, $lhSecLink(erLhcoreClassDesign::baseurl('abstract/list') . '/RestAPIKey', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Configure')));

// Mobile app notifications
$lhSecStatus = $lhSecBadge($security_check['mobile_notifications'] ? 'warning' : 'success', $security_check['mobile_notifications'] ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Enabled') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Disabled'));
$lhSecAdd($security_check['mobile_notifications'] ? 'warning' : 'success', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Mobile app notifications'), $lhSecStatus, $lhSecLink(erLhcoreClassDesign::baseurl('mobile/settings'), erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Configure')));

// Cache log files write & browser reachability check
$lhSecLogWritable = !empty($security_check['default_log_writable']);
$lhSecLogInitialLevel = $lhSecLogWritable ? 'success' : 'warning';
$lhSecStatus = '<span id="lh-sec-log-reachability-summary">' . $lhSecBadge('secondary', erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Checking...')) . '</span>';
$lhSecStatus .= $lhSecBadge($lhSecLogWritable ? 'success' : 'warning', $lhSecLogWritable ? erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','default.log writable') : erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','default.log not writable'));
if (!$lhSecLogWritable && !empty($security_check['default_log_write_error'])) {
    $lhSecStatus .= '<span class="text-muted small me-1">(' . htmlspecialchars($security_check['default_log_write_error']) . ')</span>';
}
$lhSecStatus .= '<div id="lh-sec-log-files-details" class="mt-1"></div>';
$lhSecLogAction = '<button type="button" class="btn btn-xs btn-outline-secondary" onclick="checkLogFilesReachability()">' . erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Re-check') . '</button>';
$lhSecAdd($lhSecLogInitialLevel, erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Log files public access'), $lhSecStatus, $lhSecLogAction, 'lh-sec-log-row');

// Items which require attention are listed first
$lhSecOrder = array('danger' => 0, 'warning' => 1, 'success' => 2);
usort($securityRows, function ($a, $b) use ($lhSecOrder) {
    return $lhSecOrder[$a['level']] - $lhSecOrder[$b['level']];
});

$lhSecIcons = array('success' => 'check', 'warning' => 'warning', 'danger' => 'close');
$lhSecIssues = count(array_filter($securityRows, function ($row) {
    return $row['level'] !== 'success';
}));
$lhSecHasDanger = in_array('danger', array_column($securityRows, 'level'), true);
?>

<div class="row mt-4">
    <div class="col-12">
        <h5 class="d-flex align-items-center mb-2">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Status/security checks')?>
            <?php if ($lhSecIssues == 0) : ?>
                <span id="lh-sec-summary-badge" data-issues="0" data-has-danger="0" class="badge bg-success ms-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','All good')?></span>
            <?php else : ?>
                <span id="lh-sec-summary-badge" data-issues="<?php echo (int)$lhSecIssues?>" data-has-danger="<?php echo $lhSecHasDanger ? '1' : '0'?>" class="badge <?php echo $lhSecHasDanger ? 'bg-danger' : 'bg-warning'?> ms-2"><?php echo (int)$lhSecIssues?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','issues found')?></span>
            <?php endif;?>
        </h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
            <thead>
            <tr>
                <th width="1%">&nbsp;</th>
                <th width="30%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Check')?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Status')?></th>
                <th width="1%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="lh-sec-tbody">
            <?php foreach ($securityRows as $lhSecRow) : ?>
                <tr<?php if (!empty($lhSecRow['id'])) : ?> id="<?php echo htmlspecialchars($lhSecRow['id'])?>" data-initial-level="<?php echo $lhSecRow['level']?>"<?php endif;?>>
                    <td><span class="material-icons fs18 text-<?php echo $lhSecRow['level']?> lh-sec-icon"><?php echo $lhSecIcons[$lhSecRow['level']]?></span></td>
                    <td><?php echo htmlspecialchars($lhSecRow['name'])?></td>
                    <td><?php echo $lhSecRow['status']?></td>
                    <td class="text-end text-nowrap"><?php echo $lhSecRow['actions']?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
function updateDatabase(scope) {
	$('#db-status-checked').hide();
	$('#db-status-updating').show();		
	$.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdbdoupdate' + (scope != '' ? '/(scope)/local' : ''),function(data){
        $('#status-db').html(data.result);            
    }); 
};

function compareLocal() {
    $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdb/(scope)/local',function(data){
        $('#status-db').html(data.result);
    });
}

var lhSecLogConfig = {
    files: <?php echo json_encode(isset($security_check['log_files']) ? $security_check['log_files'] : array()); ?>,
    writable: <?php echo $lhSecLogWritable ? 'true' : 'false'; ?>,
    labels: {
        checking: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Checking...')); ?>,
        majorIssue: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Major security issue: log file(s) publicly reachable')); ?>,
        notReachable: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Not reachable')); ?>,
        checkIncomplete: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Check incomplete')); ?>,
        reachable: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Reachable')); ?>,
        blocked: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Blocked')); ?>,
        notFound: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Not found')); ?>,
        redirected: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Redirected')); ?>,
        failed: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','Check failed')); ?>,
        allGood: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','All good')); ?>,
        issuesFound: <?php echo json_encode(erTranslationClassLhTranslation::getInstance()->getTranslation('system/update','issues found')); ?>
    }
};

function lhSecEscapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function checkLogFilesReachability() {
    var summaryEl = document.getElementById('lh-sec-log-reachability-summary');
    var detailsEl = document.getElementById('lh-sec-log-files-details');
    var rowEl = document.getElementById('lh-sec-log-row');
    var badgeEl = document.getElementById('lh-sec-summary-badge');

    if (!summaryEl || !detailsEl || !rowEl) {
        return;
    }

    summaryEl.innerHTML = '<span class="badge bg-secondary me-1">' + lhSecEscapeHtml(lhSecLogConfig.labels.checking) + '</span>';
    detailsEl.innerHTML = '';

    Promise.all(lhSecLogConfig.files.map(function (file) {
        var separator = file.url.indexOf('?') === -1 ? '?' : '&';
        return fetch(file.url + separator + '_=' + Date.now(), {
            method: 'GET',
            cache: 'no-store',
            redirect: 'manual',
            credentials: 'same-origin'
        }).then(function (resp) {
            if (resp.type === 'opaqueredirect' || (resp.status >= 300 && resp.status < 400)) {
                return { file: file, state: 'safe', text: lhSecLogConfig.labels.redirected };
            }
            var contentType = (resp.headers.get('content-type') || '').toLowerCase();
            if (resp.status >= 200 && resp.status < 300) {
                if (file.exists || contentType.indexOf('text/html') === -1) {
                    return { file: file, state: 'reachable', text: lhSecLogConfig.labels.reachable + ' (HTTP ' + resp.status + ')' };
                }
                return { file: file, state: 'safe', text: lhSecLogConfig.labels.notFound };
            }
            if (resp.status === 403) {
                return { file: file, state: 'safe', text: lhSecLogConfig.labels.blocked + ' (HTTP 403)' };
            }
            if (resp.status === 404) {
                return { file: file, state: 'safe', text: lhSecLogConfig.labels.notFound + ' (HTTP 404)' };
            }
            return { file: file, state: 'safe', text: 'HTTP ' + resp.status };
        }).catch(function () {
            return { file: file, state: 'error', text: lhSecLogConfig.labels.failed };
        });
    })).then(function (results) {
        var hasReachable = results.some(function (r) { return r.state === 'reachable'; });
        var hasError = results.some(function (r) { return r.state === 'error'; });

        var level = 'success';
        var iconName = 'check';
        if (hasReachable) {
            level = 'danger';
            iconName = 'close';
            summaryEl.innerHTML = '<span class="badge bg-danger me-1">' + lhSecEscapeHtml(lhSecLogConfig.labels.majorIssue) + '</span>';
        } else if (hasError || !lhSecLogConfig.writable) {
            level = 'warning';
            iconName = 'warning';
            if (hasError) {
                summaryEl.innerHTML = '<span class="badge bg-warning me-1">' + lhSecEscapeHtml(lhSecLogConfig.labels.checkIncomplete) + '</span>';
            } else {
                summaryEl.innerHTML = '<span class="badge bg-success me-1">' + lhSecEscapeHtml(lhSecLogConfig.labels.notReachable) + '</span>';
            }
        } else {
            summaryEl.innerHTML = '<span class="badge bg-success me-1">' + lhSecEscapeHtml(lhSecLogConfig.labels.notReachable) + '</span>';
        }

        detailsEl.innerHTML = results.map(function (r) {
            var badgeClass = r.state === 'reachable' ? 'bg-danger' : (r.state === 'error' ? 'bg-warning' : 'bg-secondary');
            return '<span class="badge ' + badgeClass + ' me-1 mb-1">' + lhSecEscapeHtml(r.file.path + ': ' + r.text) + '</span>';
        }).join('');

        var iconEl = rowEl.querySelector('.lh-sec-icon');
        if (iconEl) {
            iconEl.className = 'material-icons fs18 text-' + level + ' lh-sec-icon';
            iconEl.textContent = iconName;
        }

        if (level === 'danger') {
            var tbodyEl = document.getElementById('lh-sec-tbody');
            if (tbodyEl && tbodyEl.firstChild !== rowEl) {
                tbodyEl.insertBefore(rowEl, tbodyEl.firstChild);
            }
        }

        if (badgeEl) {
            var initialIssues = parseInt(badgeEl.getAttribute('data-issues') || '0', 10);
            var initialHasDanger = badgeEl.getAttribute('data-has-danger') === '1';
            var initialLevel = rowEl.getAttribute('data-initial-level') || 'success';
            var delta = (level !== 'success' ? 1 : 0) - (initialLevel !== 'success' ? 1 : 0);
            var finalIssues = Math.max(0, initialIssues + delta);
            var finalHasDanger = initialHasDanger || level === 'danger';

            if (finalIssues === 0) {
                badgeEl.className = 'badge bg-success ms-2';
                badgeEl.textContent = lhSecLogConfig.labels.allGood;
            } else {
                badgeEl.className = 'badge ' + (finalHasDanger ? 'bg-danger' : 'bg-warning') + ' ms-2';
                badgeEl.textContent = finalIssues + ' ' + lhSecLogConfig.labels.issuesFound;
            }
        }
    });
}

(function() {
	
  $.ajax({
      url: 'https://livehelperchat.com/update/version',   
      dataType: 'jsonp',      
      jsonp: 'callback',
      jsonpCallback: 'jsonpCallbackLHC',
      success: function(data){        
              $('#recent-version').text((data.version/100).toFixed(2));
      }
  });
	 
  $.postJSON('<?php echo erLhcoreClassDesign::baseurl('system/update')?>/(action)/statusdb',function(data){
      $('#status-db').html(data.result);            
  });

  checkLogFilesReachability();
    
})();
</script>