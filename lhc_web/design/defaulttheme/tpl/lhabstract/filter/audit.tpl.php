<form action="" method="get" ng-non-bindable>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Object ID');?></label>
            <input type="text" class="form-control form-control-sm" name="object_id" value="<?php echo htmlspecialchars((string)$input_form->object_id)?>" />
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Category');?></label>
            <input type="text" list="category_list" class="form-control form-control-sm" name="category" value="<?php echo htmlspecialchars($input_form->category)?>" />
        </div>
        <datalist id="category_list" autocomplete="new-password">
            <option value="js">js</option>
            <option value="block">block</option>
            <option value="cronjob_exception">cronjob_exception</option>
            <option value="cronjob_fatal">cronjob_fatal</option>
            <option value="slow_view">slow_view</option>
            <option value="update_active_chats">update_active_chats</option>
            <option value="Departament">Departament</option>
            <option value="Subject">Subject</option>
            <option value="User">User</option>
            <option value="AutoResponder">AutoResponder</option>
            <option value="AutoResponderDelete">AutoResponderDelete</option>
            <option value="CannedMsg">CannedMsg</option>
            <option value="CannedMsgDelete">CannedMsgDelete</option>
            <option value="ChatConfig">ChatConfig</option>
            <?php include(erLhcoreClassDesign::designtpl('lhabstract/filter/audit/category_list_multiinclude.tpl.php'));?>
        </datalist>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Source');?></label>
            <input type="text" list="source_list" class="form-control form-control-sm" name="source" value="<?php echo htmlspecialchars($input_form->source)?>" />
        </div>
        <datalist id="source_list" autocomplete="new-password">
            <option value="lhc">lhc</option>
            <option value="View">View</option>
            <?php include(erLhcoreClassDesign::designtpl('lhabstract/filter/audit/source_list_multiinclude.tpl.php'));?>
        </datalist>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <input type="submit" class="btn btn-sm btn-secondary" value="Search" name="doSearch">
        </div>
    </div>
</div>
</form>