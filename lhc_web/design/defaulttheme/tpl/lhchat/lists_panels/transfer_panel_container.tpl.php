<div role="tabpanel" ng-show="transfer_dep_chats.list.length > 0 || transfer_chats.list.length > 0">
	<!-- Nav tabs -->
	<ul class="nav nav-pills" role="tablist">
		<li role="presentation" class="active"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats transferred to you directly');?>" href="#transferedperson" aria-controls="transferedperson" role="tab" data-toggle="tab"><i class="material-icons">account_box</i><span class="tru-cnt"></span></a></li>
		<li role="presentation"><a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Transferred to your department');?>" href="#transfereddep" aria-controls="transfereddep" role="tab" data-toggle="tab"><i class="material-icons">account_box</i><span class="trd-cnt"></span></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="transferedperson">
		    <div id="right-transfer-chats">
	      		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_transfer_chats.tpl.php'));?>
            </div>
		</div>
		<div role="tabpanel" class="tab-pane" id="transfereddep">
		    <div id="right-transfer-departments">
	      		<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_transfer_chats_departments.tpl.php'));?>
            </div>
		</div>
	</div>
</div>