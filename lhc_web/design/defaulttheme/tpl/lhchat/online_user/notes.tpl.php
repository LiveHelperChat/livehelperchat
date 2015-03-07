<div role="tabpanel" class="tab-pane" id="notestab"> 

<div id="remarks-status-online-<?php echo $online_user->id?>" class="icon-pencil pb10 success-color"></div>

<div>
    <textarea class="form-control mh150" onkeyup="lhinst.saveNotes('<?php echo $online_user->id?>')" id="OnlineRemarks-<?php echo $online_user->id?>"><?php echo htmlspecialchars($online_user->notes)?></textarea>
</div>

</div>