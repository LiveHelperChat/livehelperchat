<h1><?php echo htmlspecialchars($object_trans['name'])?></h1>

<?php if ($identifier == 'ProactiveChatInvitation' && (erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value == 0 || erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 0)) : ?>
<div class="alert-box secondary round"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','If you want pro active chat invitation to work it has to be enabled in')?>&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/pro_active_invite"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','chat configuration')?></a>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','also online users tracking has to be')?>&nbsp;<a href="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/track_online_visitors"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','enabled')?></a></div>
<?php endif;?>

<?php if ( isset($filter) ) : ?>
    <?php switch ($filter) {
        case 'audit': ?>
            <?php include(erLhcoreClassDesign::designtpl('lhabstract/filter/audit.tpl.php')); ?>
            <?php ;
            break;

        default:
            ;
            break;
    } ?>
<?php endif;?>

<?php if ($pages->items_total > 0) : ?>
	<table cellpadding="0" class="table <?php if (isset($object_trans['table_class'])) : ?><?php echo $object_trans['table_class']?><?php else : ?>table-sm<?php endif?>" cellspacing="0" width="100%">
		<thead>
			<tr>
	    	<?php foreach ($fields as $field) : ?>
	    		<?php if (!isset($field['hidden'])) : ?>
	        		<th<?php if (isset($field['col_class'])) : ?> class="<?php echo $field['col_class']?>"<?php endif;?><?php echo isset($field['no_wrap']) ? " nowrap=\"nowrap\" " : ''?><?php echo isset($field['width']) ? " width=\"{$field['width']}%\" " : ''?>><?php echo $field['trans']?></th>
	        	<?php endif;?>
	    	<?php endforeach;?>
	    	<th width="1%">&nbsp;</th>
	    	<?php if (!isset($hide_delete)) : ?>
	   			<th width="1%">&nbsp;</th>
	    	<?php endif;?>
			</tr>
		</thead>

		<?php if (!isset($items)){
	    	$paramsFilter = array('offset' => $pages->low, 'limit' => $pages->items_per_page);

	    	if ( isset($sort) && !empty($sort) ) {
	        	$paramsFilter['sort'] = $sort;
	    	}

	    	$paramsFilter = array_merge($paramsFilter,$filter_params,$filterObject);
	    	$items = call_user_func('erLhAbstractModel'.$identifier.'::getList',$paramsFilter);
		}

		foreach ($items as $item) : ?>
	    	<tr>
	        	<?php foreach ($fields as $key => $field) : ?>

	        	<?php if (!isset($field['hidden'])) : ?>
	        	<td<?php if (isset($field['col_class'])) : ?> class="<?php echo $field['col_class']?>" <?php endif;?><?php echo isset($field['no_wrap']) ? " nowrap=\"nowrap\" " : ''?>>
	        	
	        	<?php if (isset($field['link'])) : ?><a <?php if (isset($field['link_class'])) : ?>class="<?php echo $field['link_class']?>"<?php endif;?> <?php if (isset($field['is_modal'])) : ?>onclick="return lhc.revealModal({<?php if (!isset($field['is_iframe']) || $field['is_iframe'] == true) : ?>'iframe':true,<?php endif;?>'height':500,'url':WWW_DIR_JAVASCRIPT +'<?php echo $field['link']?>/<?php echo $item->id?>'})" href="#" <?php else : ?>href="<?php echo $field['link']?>/<?php echo $item->id?>"<?php endif;?>><?php endif;?>
	        		        	
	        	<?php if (isset($field['link_title'])) : ?>
	        	  <?php echo $field['link_title']?>
	        	<?php else : ?>

                        <?php if (isset($field['wrap_start'])) : ?><?php echo $field['wrap_start']?><?php endif; ?>

    	        	<?php 
    	        	if (isset($field['frontend'])) {
    		            echo htmlspecialchars($item->{$field['frontend']});
    	        	} else {
    		            echo htmlspecialchars($item->$key);
    	        	}
    		        ?>
                        <?php if (isset($field['wrap_end'])) : ?><?php echo $field['wrap_end']?><?php endif; ?>
		        
		        <?php endif;?>
		        
		        <?php if (isset($field['link'])) : ?></a><?php endif;?>
		        </td>
	       		<?php endif;?>

	        <?php endforeach;?>
	        <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('abstract/edit')?>/<?php echo $identifier.'/'.$item->id?>"><i class="material-icons mr-0">&#xf3ea;</i></a></td>

	         <?php if (!isset($hide_delete)) : ?>
	         	<td><a class="csfr-required btn btn-danger btn-xs" onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/list','Are you sure?')?>')" href="<?php echo erLhcoreClassDesign::baseurl('abstract/delete')?>/<?php echo $identifier.'/'.$item->id?>"><i class="material-icons mr-0">&#xfa78;</i></a></td>
	         <?php endif;?>

	    </tr>
	<?php endforeach; ?>
	</table>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

	<?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>

<?php else:?>
	<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Empty...');?></p>
<?php endif;?>


<?php if (!isset($hide_add)) : ?>

	<a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('abstract/new')?>/<?php echo $identifier?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>

	<br>
<?php endif;?>