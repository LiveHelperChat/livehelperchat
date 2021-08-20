<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer chat')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div id="transfer-block-<?php echo $chat->id?>"></div>

<div role="tabpanel">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="nav-item"><a class="active nav-link" href="#transferusermodal" aria-controls="transferusermodal" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a user');?></a></li>
		<li role="presentation" class="nav-item"><a class="nav-link" href="#transferdepmodal" aria-controls="transferdepmodal" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a department');?></a></li>

        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','changeowner')) : ?>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#changeowner" aria-controls="changeowner" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Change owner');?></a></li>
        <?php endif; ?>

        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','changedepartment')) : ?>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#changedepartment" aria-controls="changedepartment" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Change department');?></a></li>
        <?php endif; ?>

	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="transferusermodal">
		
    		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged in users');?></h4>
    
      		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer a chat to one of your departments users');?></p>

            <div class="checkbox">
                <label><input type="checkbox" onchange="updateTransferUser()" checked="checked" id="logged_and_online"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Only logged and online operators');?></label>
            </div>

            <div class="checkbox">
                <label><input type="checkbox" onchange="updateTransferUser()" id="logged_and_same"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Only operators from same departments');?></label>
            </div>

            <div class="mx550" id="transfer-chat-listuserrefilter">

            </div>

    		<input type="button" onclick="lhinst.transferChat('<?php echo $chat->id;?>')" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
    		
		</div>
		<div role="tabpanel" class="tab-pane" id="transferdepmodal">

    		<div class="row">
    		    <div class="col-6">

                    <div id="transfer-chat-list-refilter" class="mx550">

                    </div>

            		<input type="button" onclick="lhinst.transferChatDep('<?php echo $chat->id;?>')" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
        		</div>
        		<div class="col-6">
        		    <div class="checkbox">
        		      <label><input type="checkbox" onchange="updateTransferDepartments()" checked="checked" id="dep_transfer_only_explicit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Only departments which are online and explicitly assigned operator are online');?></label>
        		    </div>
        		    
        		    <div class="checkbox">
        		      <label><input type="checkbox" onchange="updateTransferDepartments()" id="dep_transfer_exclude_hidden"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Exclude hidden departments');?></label>
        		    </div>
        		    
        		    <div class="checkbox">
        		      <label><input type="checkbox" onchange="updateTransferDepartments()" id="dep_transfer_exclude_disabled"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Exclude disabled departments');?></label>
        		    </div>
        		</div>
            </div>

            <script type="text/javascript">
            function updateTransferDepartments() {
            	$('#transfer-chat-list-refilter').html('...');
                $.post(WWW_DIR_JAVASCRIPT + 'chat/transferchatrefilter/<?php echo $chat->id?>/(mode)/dep',{
                    'dep_transfer_only_explicit':$('#dep_transfer_only_explicit').is(':checked'),
                    'dep_transfer_exclude_hidden':$('#dep_transfer_exclude_hidden').is(':checked'),
                    'dep_transfer_exclude_disabled':$('#dep_transfer_exclude_disabled').is(':checked')
                    }, function(data) {
                        $('#transfer-chat-list-refilter').html(data);
                });
            }

            function updateTransferUser() {
                $('#transfer-chat-listuserrefilter').html('...');
                $.post(WWW_DIR_JAVASCRIPT + 'chat/transferchatrefilter/<?php echo $chat->id?>/(mode)/user',{
                    'logged_and_online':$('#logged_and_online').is(':checked'),
                    'logged_and_same_dep':$('#logged_and_same').is(':checked')
                }, function(data) {
                    $('#transfer-chat-listuserrefilter').html(data);
                });
            }
            updateTransferUser();
            updateTransferDepartments();
            function searchUserTransfer() {
                var value = $('#search-changeowner').val();
                $.getJSON(WWW_DIR_JAVASCRIPT+ 'chat/searchprovider/users/?exclude_disabled=1&q='+escape(value), function(result){
                    var resultHTML = '';
                    result.items.forEach(function(item){
                        var selected = <?php echo $chat->user_id?> == item.id ? ' selected="selected" ' : '';
                        resultHTML += "<option " + selected + " value=\""+item.id+"\">" + $("<div>").text(item.name + (item.nick != "" ? " | " + item.nick : '')).html() + "</option>";
                    });
                    $('#id_new_user_id').html(resultHTML);
                });
            }
            </script>
		</div>
        
        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','changeowner')) : ?>
        <div role="tabpanel" class="tab-pane pt-2" id="changeowner">
            <input class="form-control mb-2 form-control-sm" onkeyup="searchUserTransfer()" id="search-changeowner" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search for a user.  First 50 users are shown.')?>" />
            <div class="form-group" id="search-changeowner-result">
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'new_user_id',
                    'selected_id'    => $chat->user_id,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => function($item){return $item->name_official . ($item->chat_nickname != '' ? ' | '.$item->chat_nickname : '');},
                    'size' => 10,
                    'list_function'  => 'erLhcoreClassModelUser::getUserList',
                    'list_function_params'  => array('limit' => 50, 'filter' => array('disabled' => 0))
                )); ?>
            </div>
            <input type="button" onclick="lhinst.changeOwner('<?php echo $chat->id?>')" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Change owner');?>">
        </div>
        <?php endif; ?>

        <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','changedepartment')) : ?>
        <div role="tabpanel" class="tab-pane" id="changedepartment">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/transfer/department.tpl.php'));?>
            <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','You will still remain an owner of the chat.');?></small></p>
            <input type="button" onclick="lhinst.changeDep('<?php echo $chat->id?>')" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Change department');?>">
        </div>
        <?php endif; ?>

	</div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>