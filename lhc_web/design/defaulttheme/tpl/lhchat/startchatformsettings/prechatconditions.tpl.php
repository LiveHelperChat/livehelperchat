<br>
<p><strong>Online conditions:</strong><br>
<small>If these conditions are met widget will become online</small>
</p>
<button type="button" class="btn btn-outline-secondary btn-xs">Add condition</button>


<hr>

<p class="mb-1"><strong>Offline conditions:</strong><br>
    <small>Make widget offline if widget is not in oline mode</small>
</p>
    <label><input type="checkbox" value="on" name="prec_enable_offline" <?php (isset($start_chat_data['prec_enable_offline']) && $start_chat_data['prec_enable_offline'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable offline mode');?></label>

<hr>

<p class="mb-1"><strong>Disable conditions.</strong><br>
<small>If widget is not in online/offline mode after online conditions check. We will show a custom message once they open a widget.</small>
</p>
<label><input type="checkbox" value="on" name="prec_enable_disable" <?php (isset($start_chat_data['prec_enable_disable']) && $start_chat_data['prec_enable_disable'] == true) ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchatformsettings','Enable disable mode');?></label>

<hr>

<p>If none of the above conditions are met widget will become hidden.</p>



