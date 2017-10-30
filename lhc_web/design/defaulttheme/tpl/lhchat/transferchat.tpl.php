<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer chat')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div id="transfer-block-<?php echo $chat->id?>"></div>

<div role="tabpanel">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#transferusermodal" aria-controls="transferusermodal" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a user');?></a></li>
		<li role="presentation"><a href="#transferdepmodal" aria-controls="transferdepmodal" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a department');?></a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="transferusermodal">
		
    		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged in users');?></h4>
    
      		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer a chat to one of your departments users');?></p>

            <div class="checkbox">
                <label><input type="checkbox" onchange="updateTransferUser()" checked="checked" id="logged_and_online"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Only logged and online operators');?></label>
            </div>

            <div class="mx550" id="transfer-chat-listuserrefilter">
                <?php $user_filter = array('hide_online' => 0); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/transfer/transferchatrefilteruser.tpl.php'));?>
            </div>

    		<input type="button" onclick="lhinst.transferChat('<?php echo $chat->id;?>')" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
    		
		</div>
		<div role="tabpanel" class="tab-pane" id="transferdepmodal">

    		<div class="row">
    		    <div class="col-xs-6">

                    <?php 
                    $departments_filter = array (
                        'dep_id' => $chat->dep_id,
                        'chat_id' => $chat->id,
                        'filter' => array(),
                        'explicit' => true
                    );
                    ?>
                    
                    <div id="transfer-chat-list-refilter">
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/transfer/transferchatrefilter.tpl.php'));?>
                    </div>

            		<input type="button" onclick="lhinst.transferChatDep('<?php echo $chat->id;?>')" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
        		</div>
        		<div class="col-xs-6">
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
                    'logged_and_online':$('#logged_and_online').is(':checked')
                }, function(data) {
                    $('#transfer-chat-listuserrefilter').html(data);
                });
            }
            </script>

		</div>
	</div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>