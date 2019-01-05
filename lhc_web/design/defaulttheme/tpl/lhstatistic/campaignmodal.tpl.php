<?php
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = htmlspecialchars($invitation->name);
$modalSize = 'md';
?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<table class="table">
    <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Parameter');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Value');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Explanation');?></th>
        </tr>
    </thead>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Total send');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_SEND'];?>
        </td>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Invitation was assigned to online visitor');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Total shown');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_SHOWN'];?>
        </td>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','widget was opened with invitation content');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Total seen');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_SEEN'];?>
        </td>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Widget was shown but visitor closed it without starting a chat');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Chat started');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_CHAT_STARTED'];?>
        </td>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Visitor started chat by online invitation');?></i></small>
        </td>
    </tr>
</table>


<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>