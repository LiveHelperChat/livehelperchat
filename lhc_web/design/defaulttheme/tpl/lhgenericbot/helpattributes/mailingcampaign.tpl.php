<?php if ($context == 'mailingcampaign') : ?>
    <p>Supported attributes</p>
    <ul>
        <li><strong>{args.current_user.name}</strong> Operator name.</li>
        <li><strong>{args.current_user.surname}</strong> Operator surname.</li>
        <li><strong>{args.current_user.chat_nickname}</strong> Chat nickname.</li>
        <li><strong>{args.current_user.job_title}</strong> Job title.</li>
        <li><strong>{args.current_user.email}</strong> E-mail.</li>
        <li><strong>Replaceable canned variables</strong> are also supported</li>
        <li><strong>{args.recipient.email}</strong> - recipient e-mail</li>
        <li><strong>{args.recipient.name}</strong> - recipient name</li>
        <li><strong>{args.recipient.attr_str_1}</strong> - attribute string 1</li>
        <li><strong>{args.recipient.attr_str_2}</strong> - attribute string 2</li>
        <li><strong>{args.recipient.attr_str_3}</strong> - attribute string 3</li>
        <li><strong>{args.recipient.attr_str_4}</strong> - attribute string 4</li>
        <li><strong>{args.recipient.attr_str_5}</strong> - attribute string 5</li>
        <li><strong>{args.recipient.attr_str_6}</strong> - attribute string 6</li>
    </ul>
<?php endif; ?>