<div class="row">

	<div class="col-xs-6">
		<div class="row">
			<div class="col-xs-6">
				<ul>
					<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Date from')?> - <?php echo htmlspecialchars($archive->range_from_front);?></li>
					<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Date to')?> - <?php echo htmlspecialchars($archive->range_to_front);?></li>
				</ul>
				<input type="hidden" value="<?php echo htmlspecialchars($archive->range_from_front);?>" name="RangeFrom" />
				<input type="hidden" value="<?php echo htmlspecialchars($archive->range_to_front);?>" name="RangeTo" />
			</div>
			<div class="col-xs-6 end">
				<ul>
					<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Potential chats to archive')?> - <?php echo htmlspecialchars($archive->potential_chats_count);?></li>
					<li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Archived chats')?> - <?php echo htmlspecialchars($archive->chats_in_archive);?></li>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','First archived chat ID')?> - <?php echo $archive->first_id?></li>
                    <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Last archived chat ID')?>  - <?php echo $archive->last_id?></li>
				</ul>
			</div>
		</div>

        <?php if ($archive->potential_chats_count > 0) : ?>
		    <input type="submit" onclick="chatArchive.startArchive();" class="btn btn-default radius success right" name="Start_archive_progress" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Start archiving');?>"/>
        <?php endif; ?>

		<div class="btn-group" role="group" aria-label="...">
		  <?php if ($archive->id > 0) : ?>
		  <a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/edit')?>/<?php echo $archive->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit')?></a>
		  <?php endif;?>
		  <a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('chatarchive/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?></a>
		</div>

	</div>
	<div class="col-xs-6 end">
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Archive progress')?></h3>
		<div id="archive-progress" class="mx170 fs12 p10"><div class="radius secondary label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Pending for action...')?></div></div>

		<hr>
		<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Terms dictionary')?></h3>
		<ul>
			<li>FCID - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','first archived chat ID')?></li>
			<li>LCID - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','last archived chat ID')?></li>
			<li>AC - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','number of archived chats')?></li>
			<li>AM - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','number of archived messages')?></li>
		</ul>
	</div>
</div>

<script>
var chatArchive = {
	startArchive : function(){
		var inst = this;
		if (confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?')?>')) {

			$('input[name="Start_archive_progress"]').attr('disabled','disabled');

			$.postJSON(WWW_DIR_JAVASCRIPT  + 'chatarchive/startarchive',{RangeFrom:$('input[name="RangeFrom"]').val(),RangeTo:$('input[name="RangeTo"]').val()}, function(data){
				if (data.error == 'false'){
					inst.archiveChat(data.id);
				} else {
					alert(data.msg);
				}
	    	});
		}
	},
	archiveChat : function(archive_id) {
		var inst = this;
		$.postJSON(WWW_DIR_JAVASCRIPT  + 'chatarchive/archivechats',{id:archive_id}, function(data){
			if (data.error == 'false'){
				$('#archive-progress').prepend(data.result);
				if (data.pending_archive == 'true') {
					inst.archiveChat(archive_id);
				} else {
					$('input[name="Start_archive_progress"]').removeAttr('disabled','disabled');
				}
			} else {
				alert(data.msg);
			};
    	}).fail(function(){
    		alert('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/process_content','Error accoured during archive process')?>');
        });
	}
};
</script>