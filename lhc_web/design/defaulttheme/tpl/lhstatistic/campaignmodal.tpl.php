<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = htmlspecialchars($invitation->name);
$modalSize = 'xl';
?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<table class="table" ng-non-bindable>
    <thead>
        <tr>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Parameter');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Total value');?></th>
            <th>
                <span class="material-icons">supervisor_account</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Parent');?>
            </th>
            <?php $statsChild = []; foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) :
                $statsChild[$childInvitation->id] = erLhcoreClassChatStatistic::getProactiveStatistic(array('filter' => array('filter' => array('invitation_id' => $invitation->id, 'variation_id' => $childInvitation->id))));
                $statsChild[$childInvitation->id]['EXECUTED_TIMES'] = $childInvitation->executed_times;
                ?>
                <th><span class="material-icons">insert_invitation</span><?php echo htmlspecialchars($childInvitation->name);?></th>
            <?php endforeach; ?>
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
            <?php $total = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_SEND'];} echo $stats['INV_SEND'] - $total; ?>
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
        <td>
            <?php echo $statsChild[$childInvitation->id]['INV_SEND'];?>
        </td>
        <?php endforeach; ?>
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
            <?php $total = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_SHOWN'];} echo $stats['INV_SHOWN'] - $total; ?>
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
        <td>
            <?php echo $statsChild[$childInvitation->id]['INV_SHOWN'];?>
        </td>
        <?php endforeach; ?>
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
            <?php $total = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_SEEN'];} echo $stats['INV_SEEN'] - $total; ?>
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
            <td>
                <?php echo $statsChild[$childInvitation->id]['INV_SEEN'];?>
            </td>
        <?php endforeach; ?>
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
            <?php $total = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_CHAT_STARTED'];} echo $stats['INV_CHAT_STARTED'] - $total; ?>
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
            <td>
                <?php echo $statsChild[$childInvitation->id]['INV_CHAT_STARTED'];?>
            </td>
        <?php endforeach; ?>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Visitor started chat by online invitation');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Matched');?></strong>
        </td>
        <td>
            <?php echo $invitation->executed_times;?>
        </td>
        <td>
            <?php $total = 0; foreach ($statsChild as $statChild) {$total+=$statChild['EXECUTED_TIMES'];} echo $invitation->executed_times - $total; ?>
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
            <td>
                <?php echo $childInvitation->executed_times;?>
            </td>
        <?php endforeach; ?>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Total times invitation was matched');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Conversions');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_CONVERSIONS'];?> (<?php echo $stats['INV_CONVERSIONS_INIT'];?>)
        </td>
        <td>
            <?php $total = 0;$totalInit = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_CONVERSIONS']; $totalInit+=$statChild['INV_CONVERSIONS_INIT'];} echo $stats['INV_CONVERSIONS'] - $total; ?> (<?php echo $stats['INV_CONVERSIONS_INIT'] - $totalInit;?>)
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
            <td>
                <?php echo $statsChild[$childInvitation->id]['INV_CONVERSIONS'];?> (<?php echo $statsChild[$childInvitation->id]['INV_CONVERSIONS_INIT'];?>)
            </td>
        <?php endforeach; ?>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Number of times conversions has happened from total initiated');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Unique conversions');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_UNIQ_CONVERSIONS'];?> (<?php echo $stats['INV_UNIQ_CONVERSIONS_INIT'];?>)
        </td>
        <td>
            <?php $total = 0;$totalInit = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_UNIQ_CONVERSIONS']; $totalInit+=$statChild['INV_UNIQ_CONVERSIONS_INIT'];} echo $stats['INV_UNIQ_CONVERSIONS'] - $total; ?> (<?php echo $stats['INV_UNIQ_CONVERSIONS_INIT'] - $totalInit;?>)
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
            <td>
                <?php echo $statsChild[$childInvitation->id]['INV_UNIQ_CONVERSIONS'];?> (<?php echo $statsChild[$childInvitation->id]['INV_UNIQ_CONVERSIONS_INIT'];?>)
            </td>
        <?php endforeach; ?>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Multiple invitations to the same online visitor is considered as a one record');?></i></small>
        </td>
    </tr>
    <tr>
        <td colspan="4"><i>unique_id</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','field based statistic. Field has to be not empty.');?></td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Conversions');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_CUSTOM_CONVERSIONS'];?> (<?php echo $stats['INV_CUSTOM_CONVERSIONS_INIT'];?>)
        </td>
        <td>
            <?php $total = 0;$totalInit = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_CUSTOM_CONVERSIONS']; $totalInit+=$statChild['INV_CUSTOM_CONVERSIONS_INIT'];} echo $stats['INV_CUSTOM_CONVERSIONS'] - $total; ?> (<?php echo $stats['INV_CUSTOM_CONVERSIONS_INIT'] - $totalInit;?>)
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
            <td>
                <?php echo $statsChild[$childInvitation->id]['INV_CUSTOM_CONVERSIONS'];?> (<?php echo $statsChild[$childInvitation->id]['INV_CUSTOM_CONVERSIONS_INIT'];?>)
            </td>
        <?php endforeach; ?>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Number of times conversions has happened from total initiated');?></i></small>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Unique conversions');?></strong>
        </td>
        <td>
            <?php echo $stats['INV_CUSTOM_UNIQ_CONVERSIONS'];?> (<?php echo $stats['INV_CUSTOM_UNIQ_CONVERSIONS_INIT'];?>)
        </td>
        <td>
            <?php $total = 0;$totalInit = 0; foreach ($statsChild as $statChild) {$total+=$statChild['INV_CUSTOM_UNIQ_CONVERSIONS']; $totalInit+=$statChild['INV_CUSTOM_UNIQ_CONVERSIONS_INIT'];} echo $stats['INV_CUSTOM_UNIQ_CONVERSIONS'] - $total; ?> (<?php echo $stats['INV_CUSTOM_UNIQ_CONVERSIONS_INIT'] - $totalInit;?>)
        </td>
        <?php foreach (erLhAbstractModelProactiveChatInvitation::getList(['filter' => ['parent_id' => $invitation->id]]) as $childInvitation) : ?>
        <td>
            <?php echo $statsChild[$childInvitation->id]['INV_CUSTOM_UNIQ_CONVERSIONS'];?> (<?php echo $statsChild[$childInvitation->id]['INV_CUSTOM_UNIQ_CONVERSIONS_INIT'];?>)
        </td>
        <?php endforeach; ?>
        <td>
            <small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('statistic/campaign','Multiple invitation of the same unique_id value is considered as a one record.');?></i></small>
        </td>
    </tr>
</table>


<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>