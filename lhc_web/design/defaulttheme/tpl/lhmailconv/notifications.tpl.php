<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','Notifications about mails')?>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
        </div>
        <div class="modal-body">

            <?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','Settings updated'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
                <script>
                    setTimeout(function(){
                        location.reload();
                    },250);
                </script>
            <?php endif; ?>

            <form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/notifications')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li role="presentation" class="nav-item"><a href="#subject-filter" class="nav-link active" aria-controls="subject-filter" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Subject filter');?></a></li>
                    <li role="presentation" class="nav-item"><a href="#time-filter" class="nav-link" aria-controls="time-filter" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Time filter');?></a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="subject-filter">
                        <div class="row" style="max-height: 500px; overflow-y: auto">
                            <?php foreach (erLhAbstractModelSubject::getList(array('sort' => 'name ASC','limit' => false)) as $item) : ?>
                                <div class="col-6">
                                    <label><input name="subject_id[]" <?php if (in_array($item->id,$subject_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo $item->id?>"> <?php echo htmlspecialchars($item->name)?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="time-filter">
                        <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','To receive browser notifications you have to enable them in your account Notifications settings.')?></small></p>

                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','E-mail arrived during past X time')?></label>
                            <?php $valuePending = (int)erLhcoreClassModelUserSetting::getSetting('malarm_h', -1)?>
                            <select class="form-control form-control-sm" name="malarm_h">
                                <option value="-1" <?php echo $valuePending == -1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','Do not inform')?></option>
                                <option value="600" <?php echo $valuePending == 600 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="1800" <?php echo $valuePending == 1800 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="3600" <?php echo $valuePending == 3600 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hour')?></option>
                                <option value="7200" <?php echo $valuePending == 7200 ? 'selected="selected"' : ''?>>2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hours')?></option>
                                <option value="14400" <?php echo $valuePending == 14400 ? 'selected="selected"' : ''?>>4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hours')?></option>
                                <option value="<?php echo 3600*8?>" <?php echo $valuePending == 3600*8 ? 'selected="selected"' : ''?>>8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hours')?></option>
                                <option value="<?php echo 3600*16?>" <?php echo $valuePending == 3600*16 ? 'selected="selected"' : ''?>>16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hours')?></option>
                                <option value="<?php echo 3600*24?>" <?php echo $valuePending == 3600*24 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','day')?></option>
                                <option value="<?php echo 3600*24*2?>" <?php echo $valuePending == 3600*24*2 ? 'selected="selected"' : ''?>>2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','days')?></option>
                                <option value="<?php echo 3600*24*5?>" <?php echo $valuePending == 3600*24*5 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','days')?></option>
                                <option value="<?php echo 3600*24*7?>" <?php echo $valuePending == 3600*24*7 ? 'selected="selected"' : ''?>>7 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','days')?></option>
                                <option value="<?php echo 3600*24*14?>" <?php echo $valuePending == 3600*24*14 ? 'selected="selected"' : ''?>>14 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','days')?></option>
                                <option value="<?php echo 3600*24*28?>" <?php echo $valuePending == 3600*24*28 ? 'selected="selected"' : ''?>>28 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','days ')?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','When an email takes X mail queue time. Mail is in the pending state longer than X time.')?></label>
                            <?php $valuePending = (int)erLhcoreClassModelUserSetting::getSetting('malarm_p', -1)?>
                            <select class="form-control form-control-sm" name="malarm_p">
                                <option value="-1" <?php echo $valuePending == -1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','Do not inform')?></option>
                                <option value="30" <?php echo $valuePending == 30 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','seconds')?></option>
                                <option value="60" <?php echo $valuePending == 60 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minute')?></option>
                                <option value="120" <?php echo $valuePending == 120 ? 'selected="selected"' : ''?>>2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="180" <?php echo $valuePending == 180 ? 'selected="selected"' : ''?>>3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="240" <?php echo $valuePending == 240 ? 'selected="selected"' : ''?>>4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="300" <?php echo $valuePending == 300 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="600" <?php echo $valuePending == 600 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="900" <?php echo $valuePending == 900 ? 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="1800" <?php echo $valuePending == 1800 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="3600" <?php echo $valuePending == 3600 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hour')?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','When an email has been accepted by an agent and for X time and has no response.')?></label>
                            <?php $valuePendingResponse = (int)erLhcoreClassModelUserSetting::getSetting('malarm_pr', -1)?>
                            <select class="form-control form-control-sm" name="malarm_pr">
                                <option value="-1" <?php echo $valuePendingResponse == -1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','Do not inform')?></option>
                                <option value="30" <?php echo $valuePendingResponse == 30 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','seconds')?></option>
                                <option value="60" <?php echo $valuePendingResponse == 60 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minute')?></option>
                                <option value="120" <?php echo $valuePendingResponse == 120 ? 'selected="selected"' : ''?>>2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="180" <?php echo $valuePendingResponse == 180 ? 'selected="selected"' : ''?>>3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="240" <?php echo $valuePendingResponse == 240 ? 'selected="selected"' : ''?>>4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="300" <?php echo $valuePendingResponse == 300 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="600" <?php echo $valuePendingResponse == 600 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="900" <?php echo $valuePendingResponse == 900 ? 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="1800" <?php echo $valuePendingResponse == 1800 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','minutes')?></option>
                                <option value="3600" <?php echo $valuePendingResponse == 3600 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvdashboard','hour')?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <input type="submit" class="btn btn-secondary btn-sm" name="updateMailSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">

            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>