<div class="form-group">
    <label>Pre-chat HTML</label>
    <textarea rows="15" class="form-control" name="pre_chat_html"><?php if (isset($start_chat_data['pre_chat_html'])) : ?><?php echo htmlspecialchars($start_chat_data['pre_chat_html'])?><?php endif;?></textarea>
</div>

<div class="form-group">
    <label>Offline Pre-chat HTML</label>
    <textarea rows="15" class="form-control" name="pre_offline_chat_html"><?php if (isset($start_chat_data['pre_offline_chat_html'])) : ?><?php echo htmlspecialchars($start_chat_data['pre_offline_chat_html'])?><?php endif;?></textarea>
</div>