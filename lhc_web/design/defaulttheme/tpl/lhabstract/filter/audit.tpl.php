<form action="" method="get">
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Object ID');?></label>
            <input type="text" class="form-control form-control-sm" name="object_id" value="<?php echo htmlspecialchars($input_form->object_id)?>" />
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Category');?></label>
            <input type="text" class="form-control form-control-sm" name="category" value="<?php echo htmlspecialchars($input_form->category)?>" />
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Source');?></label>
            <input type="text" class="form-control form-control-sm" name="source" value="<?php echo htmlspecialchars($input_form->source)?>" />
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <input type="submit" class="btn btn-secondary" value="Search" name="doSearch">
        </div>
    </div>
</div>
</form>