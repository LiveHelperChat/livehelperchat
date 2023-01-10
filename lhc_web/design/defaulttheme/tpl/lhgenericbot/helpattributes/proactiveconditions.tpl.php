<?php if ($context == 'proactiveconditions') : ?>
    <p>Supported magic attributes</p>
    <ul>
        <li><strong>{time}</strong> present time in timestamp</li>
    </ul>
    <p>Few examples</p>
    <ul>
        <li><span class="badge bg-secondary">date_opened</span> <span class="badge bg-secondary">&lt;</span> <span class="badge bg-secondary">{time}-(3*24*3600)</span></li>
        <li>If you are passing user account open date you can show invitation only to those members whois account was opened 3 or more days ago.</li>
    </ul>
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/proactiveconditions_multiinclude.tpl.php'));?>
<?php endif; ?>

