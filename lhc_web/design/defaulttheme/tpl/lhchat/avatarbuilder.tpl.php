<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Build your avatar')?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','We will generate avatar based on this string if you do not choose some parts')?></label>
                        <input type="text" id="id_avatar_string_construct" class="form-control form-control-sm" value="<?php echo htmlspecialchars($id)?>">
                    </div>

                    <?php $partsNames = [
                        'clo' => 'Clothes',
                        'head' => 'Head',
                        'mouth' => 'Mouth',
                        'eyes' => 'Eyes',
                        'top' => 'Top'];
                    foreach (['clo','head','mouth','eyes','top'] as $item) : ?>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><?php echo htmlspecialchars($partsNames[$item])?></label>
                                        <select id="scope_<?php echo $item?>" class="form-control form-control-sm avatar-scope">
                                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Choose')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '00') : ?>selected="selected"<?php endif; ?> value="00"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Robo')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '01') : ?>selected="selected"<?php endif; ?> value="01"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Girl')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '02') : ?>selected="selected"<?php endif; ?> value="02"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Blonde')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '03') : ?>selected="selected"<?php endif; ?> value="03"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Evilnormie')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '04') : ?>selected="selected"<?php endif; ?> value="04"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Country')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '05') : ?>selected="selected"<?php endif; ?> value="05"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Johnyold')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '06') : ?>selected="selected"<?php endif; ?> value="06"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Asian')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '07') : ?>selected="selected"<?php endif; ?> value="07"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Punk')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '08') : ?>selected="selected"<?php endif; ?> value="08"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Afrohair')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '09') : ?>selected="selected"<?php endif; ?> value="09"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Normie female')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '10') : ?>selected="selected"<?php endif; ?> value="10"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Older')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '11') : ?>selected="selected"<?php endif; ?> value="11"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Firehair')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '12') : ?>selected="selected"<?php endif; ?> value="12"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Blond')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '13') : ?>selected="selected"<?php endif; ?> value="13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Ateam')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '14') : ?>selected="selected"<?php endif; ?> value="14"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Rasta')?></option>
                                            <option <?php if (isset($props[$item]['part']) && $props[$item]['part'] === '15') : ?>selected="selected"<?php endif; ?> value="15"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Meta')?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Color')?></label>
                                        <select id="variant_<?php echo $item?>" class="form-control form-control-sm avatar-variant">
                                            <option <?php if (isset($props[$item]['theme']) && $props[$item]['theme'] === 'A') : ?>selected="selected"<?php endif; ?> value="A">A</option>
                                            <option <?php if (isset($props[$item]['theme']) && $props[$item]['theme'] === 'B') : ?>selected="selected"<?php endif; ?> value="B">B</option>
                                            <option <?php if (isset($props[$item]['theme']) && $props[$item]['theme'] === 'C') : ?>selected="selected"<?php endif; ?> value="C">C</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-6">
                    <img width="w-100" id="id_avatar_img" src='<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/avatar')?>/<?php echo htmlspecialchars($id)?>' alt="" title="" />
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="button" id="set_avatar_action" class="btn btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/avatarbuilder','Set')?></button>
                </div>
            </div>

            <script>
                (function() {
                    var avatarString = '';

                    function buildAvatar() {
                        avatarString = $('#id_avatar_string_construct').val() != '' ? $('#id_avatar_string_construct').val() : 'tmp';
                        var elements = ['clo','head','mouth','eyes','top'];
                        elements.forEach(function(item) {
                            if ($('#scope_' + item).val() != '') {
                                avatarString += '__' + item.substr(0,1) +'_'+$('#scope_' + item).val()+'_'+$('#variant_' + item).val();
                            }
                        });
                        $('#id_avatar_img').attr('src','<?php echo erLhcoreClassDesign::baseurl('widgetrestapi/avatar')?>'+'/'+avatarString);
                    }

                    $('.avatar-scope, .avatar-variant').change(function(){
                        buildAvatar();
                    });

                    $('#id_avatar_string_construct').keyup(function(){
                        buildAvatar();
                    });

                    $('#set_avatar_action').click(function(){
                        $('#<?php echo htmlspecialchars($prefix)?>id_avatar_string').val(avatarString);
                        $('#<?php echo htmlspecialchars($prefix)?>avatar_string_img').attr('src',$('#id_avatar_img').attr('src'));
                        $('#myModal').modal('hide');
                    });

                    buildAvatar();

                })();
            </script>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>