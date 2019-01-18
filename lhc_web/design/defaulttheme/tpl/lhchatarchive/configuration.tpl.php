<h1>Configuration</h1>

<form action="" method="post">
    <div class="form-group">
        <label><input type="checkbox" name="automatic_archiving" value="on" <?php isset($ar_options['automatic_archiving']) && $ar_options['automatic_archiving'] == true ? print 'checked="checked"' : '' ?> > Automatic archiving</label>
    </div>
    <div class="form-group">
        <label>Archive older chat's than defined days</label>
        <input type="text" class="form-control" name="older_than" value="<?php isset($ar_options['older_than']) && $ar_options['older_than'] == true ? print $ar_options['older_than'] : '' ?>" />
    </div>
    <h4>Archive size options</h4>
    <div class="form-group">
        <label><input type="radio" name="archive_strategy" value="1" <?php isset($ar_options['archive_strategy']) && $ar_options['archive_strategy'] == 1 ? print 'checked="checked"' : '' ?> >Create new archive every month. New archive will be created every month</label>
    </div>
    <div class="form-group">
        <label><input type="radio" name="archive_strategy" value="2" <?php isset($ar_options['archive_strategy']) && $ar_options['archive_strategy'] == 2 ? print 'checked="checked"' : '' ?> >Create new archive If chat's number in last archive reaches defined number</label>
        <input type="text" class="form-control" name="max_chats" value="<?php isset($ar_options['max_chats']) ? print htmlspecialchars($ar_options['max_chats']) : ''?>" >
    </div>
    <input type="submit" class="btn btn-default" name="StoreOptions" value="Save">
</form>