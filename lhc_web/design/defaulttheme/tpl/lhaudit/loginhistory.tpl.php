<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Login history')?></h1>

<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" class="pb-2" name="SearchFormRight" autocomplete="off">
    <input type="hidden" name="doSearch" value="1">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User ID');?></label>
                <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo htmlspecialchars((string)$input->user_id)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','IP');?></label>
                <input type="text" class="form-control form-control-sm" name="ip" value="<?php echo htmlspecialchars((string)$input->ip)?>" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
                <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" autocomplete="off" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars((string)$input->timefrom)?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="timefrom_hours" id="id_timefrom_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timefrom_minutes" id="id_timefrom_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timefrom_seconds" id="id_timefrom_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_seconds) && $input->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
                <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" autocomplete="off" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars((string)$input->timeto)?>" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="timeto_hours" id="id_timeto_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timeto_minutes" id="id_timeto_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timeto_seconds" id="id_timeto_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" name="doSearch" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />

                <?php if (isset($_GET['doSearch'])) : ?>
                    <a class="btn btn-sm btn-outline-secondary" href="<?php echo $input->form_action?>"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<script>
    (function() {
        $('#id_timefrom,#id_timeto').fdatepicker({
            format: 'yyyy-mm-dd'
        });
    })();
</script>

<?php if (isset($items)) : ?>
    <table class="table table-sm" ng-non-bindable cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','ID');?></th>
            <th width="1%" nowrap=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','User ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Message');?></th>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','IP');?></th>
            <th width="1%" nowrap=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Allowed IP');?></th>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Date');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php echo $item->id?>
                </td>
                <td>
                    <?php echo $item->user_id?>
                </td>
                <td title="<?php echo $item->type?>">
                    <?php if ($item->status == erLhcoreClassModelUserLogin::STATUS_COMPLETED) : ?>
                        <span class="material-icons text-success">done</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($item->msg)?>
                </td>
                <td>
                    <?php echo $item->ip?>
                </td>
                <td nowrap>
                    <?php if ($item->ip_in_allowed_range === null) : ?>
                        <span class="text-muted" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','IP restriction is not configured');?>">-</span>
                    <?php elseif ($item->ip_in_allowed_range === true) : ?>
                        <span class="badge bg-success" title="<?php echo htmlspecialchars((string)$allowLoginFromIp)?>"><span class="material-icons" style="font-size:14px">done</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Allowed');?></span>
                    <?php else : ?>
                        <span class="badge bg-danger" title="<?php echo htmlspecialchars((string)$allowLoginFromIp)?>"><span class="material-icons" style="font-size:14px">close</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Not allowed');?></span>
                    <?php endif; ?>
                </td>
                <td nowrap>
                    <?php echo $item->ctime_front?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

