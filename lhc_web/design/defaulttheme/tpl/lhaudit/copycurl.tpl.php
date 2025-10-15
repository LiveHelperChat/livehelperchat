<?php
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Debug data');
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>


    <div class="modal-body">
        <ul class="nav nav-tabs" role="tablist">
            <?php if (isset($command)) : ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">CURL Request</button>
            </li>
            <?php endif; ?>
            
            <li class="nav-item" role="presentation">
                <button class="nav-link<?php if (!isset($command)) : ?> active<?php endif;?>" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">JSON View</button>
            </li>

        </ul>
        <div class="tab-content" >

            <?php if (isset($command)) : ?>
            <div class="tab-pane fade show active pt-2" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                <textarea class="form-control form-control-sm fs12" rows="20" id="curl-command-text"><?php echo htmlspecialchars($command);?></textarea>
                <div class="ps-0 ms-0 me-0 pt-1"><button type="button" data-bs-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Copied')?>" onclick="lhinst.copyContent($(this))" data-copy-id="curl-command-text" class="btn btn-success"><span class="material-icons">content_copy</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Copy')?></button></div>
            </div>
            <?php endif; ?>

            <div class="tab-pane fade<?php if (!isset($command)) : ?> show active<?php endif;?>" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                
                <div id="json-renderer" style="max-height: 500px; overflow: auto; font-family: sans-serif"></div>

                <script src="<?php echo erLhcoreClassDesign::designJS('js/jsonview.js');?>"></script>
                <link rel="stylesheet" href="<?php echo erLhcoreClassDesign::designCSS('css/jsonview.css');?>">

                <script>
                    $(function() {
                        var data = <?php echo json_encode($json_data);?>;
                        const tree = jsonview.create(data);
                        jsonview.render(tree, document.getElementById("json-renderer"));
                        jsonview.expand(tree);
                    });
                </script>


            </div>
        </div>
    </div>

    <div class="modal-footer ps-0 pe-0 ms-0 me-0">
        <div class="row w-100 ps-0 pe-0 ms-0 me-0">
            <div class="col"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button></div>
         </div>
    </div>



<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>