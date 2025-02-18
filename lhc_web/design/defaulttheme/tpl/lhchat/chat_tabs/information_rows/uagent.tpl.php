<tr>
    <td colspan="2">

        <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/visitor_title.tpl.php'));?>

        <div class="row text-muted">

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/uagent_row.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/ip.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/email.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/phone.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/online_profile.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/referrer.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/session_referrer.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/country_code.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/city.tpl.php'));?>

            <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/send_mail.tpl.php'));?>

            <?php if (isset($canEditChat) && $canEditChat == true) : ?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_contact.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/show_survey.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/redirect_user.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/information_rows/screenshot.tpl.php'));?>

                <?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/actions/blockuser.tpl.php'));?>
            <?php endif; ?>

        </div>

    </td>
</tr>
