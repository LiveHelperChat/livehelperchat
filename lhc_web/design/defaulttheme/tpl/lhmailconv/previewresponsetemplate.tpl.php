<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2" ng-non-bindable>
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span><?php echo htmlspecialchars($response_template->name)?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="p-1 ps-2 border-bottom" ng-non-bindable>
            <?php $subjects = $response_template->subjects; if (!empty($subjects)) : ?><span class="fs12 fw-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','Subjects');?></span>&nbsp;-&nbsp;<?php foreach ($response_template->subjects as $subject) : ?><span class="badge bg-info me-1"><?php echo htmlspecialchars((string)$subject->subject) ?></span><?php endforeach;?><?php endif; ?><?php $depIds = $response_template->department_ids_front; if (!empty($depIds)) : ?><span class="fs12 fw-bold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','Departments');?>&nbsp;-&nbsp;</span><?php foreach (erLhcoreClassModelDepartament::getList(['filterin' => ['id' => $depIds]]) as $department) : ?><span class="badge bg-primary me-1"><?php echo htmlspecialchars((string)$department) ?></span><?php endforeach;endif; ?>
            <?php if ($response_template->dep_id == 0) : ?><span class="badge bg-success me-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','All departments');?></span><?php endif; ?>
        </div>

        <div class="modal-body" ng-non-bindable>

            <h5 class="border-bottom pb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','HTML Template');?></h5>
            <div class="mx300 text-muted" >
                <?php echo erLhcoreClassMailconvHTMLParser::getHTMLPreview($response_template->template)?>
            </div>

            <h5 class="border-bottom pb-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrp','Plain text Template');?></h5>
            <div class="mx300 text-muted" >
                <?php echo nl2br(htmlspecialchars($response_template->template_plain)) ?>
            </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>