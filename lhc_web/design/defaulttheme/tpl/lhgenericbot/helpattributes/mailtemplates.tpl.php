<?php if ($context == 'mailtemplates') : ?>
    <ul>
        <li><strong>{args.mail.id}</strong> mail conversation id</li>
        <li><strong>{args.mail.mail_variables_array.&lt;variables&gt;}</strong> any mail variable from variables array.</li>
        <li><strong>{args.mail.dep_id}</strong> Department ID</li>
        <li><strong>{args.msg.id}</strong> Message ID.</li>
        <li><strong>{args.msg.subject}</strong> Message subject.</li>
        <li><strong>{args.msg.from_name}</strong> Message from name.</li>
        <li><strong>{args.msg.sender_name}</strong> Message sender name.</li>
        <li><strong>{args.current_user.name}</strong> Operator name.</li>
        <li><strong>{args.current_user.surname}</strong> Operator surname.</li>
        <li><strong>{args.current_user.chat_nickname}</strong> Chat nickname.</li>
        <li><strong>{args.current_user.job_title}</strong> Job title.</li>
        <li><strong>{args.current_user.email}</strong> E-mail.</li>
        <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/mailtemplates_multiinclude.tpl.php'));?>
    </ul>
<?php endif; ?>