Editable div as text input field for chat


<lhc-editor id="1647601882" scope="chat"></lhc-editor>

<input id="fileupload-1647601882" class="fs12 d-none" type="file" name="files[]" multiple="">

<script>
    ee.addListener('svelte_chat_1647601882_msg', function(msg){
        console.log(msg);
    });

    setTimeout(function(){
        <?php $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data ?>
        lhinst.addFileUpload({ft_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Not an accepted file type')?>',fs_msg:'<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Filesize is too big')?>',chat_id:'1647601882',fs:<?php echo $fileData['fs_max']*1024?>,ft_op:/(\.|\/)(<?php echo $fileData['ft_op']?>)$/i});
    },2000);

</script>

<textarea id="test-area" cols="10" style="width: 100%"></textarea>