<form action="" method="post">
    <div class="form-group">
        <input type="text" id="template-keyword" class="form-control form-control-sm" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Search for template');?>"/>
    </div>
</form>

<div id="list-result-template" class="border-bottom" style="max-height: 540px; overflow-y: auto">

</div>

<script>
    $(document).ready(function(){
        var timeoutKeyword = null;
        $.get(WWW_DIR_JAVASCRIPT + 'mailconv/searchtemplate/<?php echo (int)$dep_id?>?<?php isset($message_id) ? print 'm='.$message_id.'&' : '' ?><?php isset($conversation_id) ? print 'c='.$conversation_id.'&' : '' ?>q=' + encodeURIComponent($('#template-keyword').val()), function(data) {
            $('#list-result-template').html(data);
        });
        $('#list-result-template').on('click', 'a.use-template', function(item) {
            window.parent.postMessage({
                mceAction: 'insertContent',
                content: $('#use-template-value-'+$(this).attr('data-id')).val()
            }, '*');
            window.parent.postMessage({ mceAction: 'close' });
        });
        $('#template-keyword').keyup(function(){
            clearTimeout(timeoutKeyword);
            setTimeout(function() {
                $.get(WWW_DIR_JAVASCRIPT + 'mailconv/searchtemplate/<?php echo (int)$dep_id?>?<?php isset($message_id) ? print 'm='.$message_id.'&' : '' ?><?php isset($conversation_id) ? print 'c='.$conversation_id.'&' : '' ?>q=' + encodeURIComponent($('#template-keyword').val()), function(data) {
                    $('#list-result-template').html(data);
                });
            },300);
        });
    });
</script>