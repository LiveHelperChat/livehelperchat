<div class="row">
	<div class="columns small-3">
		<ul>
			<li>Date from - <?php echo htmlspecialchars($archive->range_from_front);?></li>
			<li>Date to - <?php echo htmlspecialchars($archive->range_to_front);?></li>
		</ul>
		<input type="hidden" value="<?php echo htmlspecialchars($archive->range_from_front);?>" name="RangeFrom" />
		<input type="hidden" value="<?php echo htmlspecialchars($archive->range_to_front);?>" name="RangeTo" />
	</div>
	<div class="columns small-3 end">
		<ul>
			<li>Potential chats to archive - <?php echo htmlspecialchars($archive->potential_chats_count);?></li>
		</ul>
	</div>
	<div class="columns small-6 end">
		<h3>Archive progress</h3>
		<div id="archive-progress" class="mx170 fs12">Pending for action...</div>
	</div>
</div>






<ul class="button-group radius">
      <li><input type="submit" onclick="chatArchive.startArchive();" class="small button" name="Start_archive_progress" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Start archive');?>"/></li>
      <li><input type="submit" class="small button" name="Cancel_archive_progress" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
</ul>

<script>
var chatArchive = {
	startArchive : function(){
		var inst = this;
		if (confirm('Are you sure?')) {
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
				}
			} else {
				alert(data.msg);
			}
    	});
	}
};
</script>