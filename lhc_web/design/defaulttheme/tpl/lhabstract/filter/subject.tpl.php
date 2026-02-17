<form action="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Subject" method="get">
    <div class="row">

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
                <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($input_form->name)?>" />
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label d-block">&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="pinnedCheck" <?php echo $input_form->pinned == 1 ? print 'checked="checked"' : ''?> value="1" name="pinned"/>
                    <label class="form-check-label" for="pinnedCheck">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show only pinned');?>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label d-block">&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="internalCheck" <?php echo $input_form->internal == 1 ? print 'checked="checked"' : ''?> value="1" name="internal"/>
                    <label class="form-check-label" for="internalCheck">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Show only internal');?>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="mb-3">
                <input type="submit" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" name="doSearch">
            </div>
        </div>

    </div>
</form>
