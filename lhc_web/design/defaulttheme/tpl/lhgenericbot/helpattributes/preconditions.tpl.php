<?php if ($context == 'preconditions') : ?>
<p>These conditions you can check and control widget state. More attributes <a href="https://github.com/LiveHelperChat/livehelperchat/blob/master/lhc_web/lib/models/lhchat/erlhcoreclassmodelchatonlineuser.php#L18" target="_blank">here</a></p>
    <ul>
        <li><strong>{args.online_user.user_country_code}</strong> = <b>us</b> allow to start only for visitors from United States</li>
        <li><strong>{args.online_user.user_country_code}</strong> in <b>us,lt</b> allow to start only for visitors from United States and Lithuania</li>
        <li><strong>{args.online_user.online_attr_system_array.&lt;any_attribute_you_passed&gt;}</strong> = <b>VIP</b> allow to start only for visitors whois attribute is VIP</li>
        <li><strong>{args.online_user.referrer}</strong> `contains`. Referer from where visitor come to site.</li>
        <li><strong>{args.online_user.dep_id}</strong> = Department ID</li>
        <li><strong>{args.is_online}</strong> = <b>1</b> or <b>0</b> (are we online)</li>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/preconditions_multiinclude.tpl.php'));?>
    </ul>
<?php endif; ?>