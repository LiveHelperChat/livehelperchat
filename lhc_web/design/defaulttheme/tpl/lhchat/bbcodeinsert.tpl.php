<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-body">
            <?php
            $icons = array(
                array(
                    'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Positive'),
                    'icons' => array(
                        '&#x1F600', '&#x1F601', '&#x1F602', '&#x1F923', '&#x1F603', '&#x1F604', '&#x1F605', '&#x1F606', '&#x1F609', '&#x1F60A', '&#x1F60B', '&#x1F60E', '&#x1F60D', '&#x1F618', '&#x1F617', '&#x1F619', '&#x1F61A', '&#x1F642', '&#x1F917', '&#x1F929', '&#x1F607', '&#x1F920', '&#x1F925', '&#x1F92B', '&#x1F92D', '&#x1F9D0', '&#x1F913', '&#x1F635', '&#x1F44D', '&#x1F44E', '&#x1F44C', '&#x270C', '&#x1F44B', '&#x1F446', '&#x1F447', '&#x1F449', '&#x1F448', '&#x261D', '&#x1F44F', '&#x1F4AA','&#x1F91E')
                ),
                array(
                    'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Neutral'),
                    'icons' => array(
                        '&#x1F914', '&#x1F928', '&#x1F610', '&#x1F611', '&#x1F636', '&#x1F644', '&#x1F60F', '&#x1F623', '&#x1F625', '&#x1F62E', '&#x1F910', '&#x1F62F', '&#x1F62A', '&#x1F62B', '&#x1F634', '&#x1F60C', '&#x1F61B', '&#x1F61C', '&#x1F61D', '&#x1F924', '&#x1F612', '&#x1F613', '&#x1F614', '&#x1F615', '&#x1F643', '&#x1F911', '&#x1F632')
                ),
                array(
                    'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Negative'),
                    'icons' => array(
                        '&#x2639', '&#x1F641', '&#x1F616', '&#x1F61E', '&#x1F61F', '&#x1F624', '&#x1F622', '&#x1F62D', '&#x1F626', '&#x1F627', '&#x1F628', '&#x1F629', '&#x1F92F', '&#x1F62C', '&#x1F630', '&#x1F631', '&#x1F633', '&#x1F92A', '&#x1F635', '&#x1F621', '&#x1F620', '&#x1F92C', '&#x1F614', '&#x1F615', '&#x1F643', '&#x1F911', '&#x1F632','&#x1F637', '&#x1F912', '&#x1F915', '&#x1F922', '&#x1F92E', '&#x1F927')
                ),
                array(
                    'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Things'),
                    'icons' => array('&#x1F4A4', '&#x1F30F', '&#x1F319', '&#x2B50', '&#x2600', '&#x26C5', '&#x2601', '&#x26A1', '&#x2614', '&#x2744', '&#x26C4', '&#x1F525', '&#x2764', '&#x1F48B', '&#x1F389', '&#x270F', '&#x1F4DA', '&#x1F4F0', '&#x1F453', '&#x1F302', '&#x1F680', '&#x1F3C1', '&#x1F3AC', '&#x1F4AC', '&#x1F4BB', '&#x1F4F1', '&#x1F4DE', '&#x231B', '&#x23F0', '&#x231A', '&#x1F513', '&#x1F512', '&#x1F50E', '&#x1F4CE', '&#x1F4A1', '&#x1F527', '&#x1F3C6', '&#x1F4B0', '&#x1F4B3', '&#x2709', '&#x1F4E6', '&#x1F4DD', '&#x1F4C5', '&#x1F4C2', '&#x2702', '&#x1F4CC', '&#x270F', '&#x1F374', '&#x1F37A', '&#x1F37B', '&#x1F378', '&#x1F379', '&#x1F377', '&#x1F355', '&#x1F354', '&#x1F366', '&#x1F382', '&#x1F370', '&#x1F35F', '&#x1F36B', '&#x1F34F', '&#x1F34A', '&#x1F353', '&#x1F34C', '&#x1F340', '&#x1F339', '&#x1F33B', '&#x1F334', '&#x1F383', '&#x1F47B', '&#x1F384', '&#x1F381', '&#x2708', '&#x1F684', '&#x1F68C', '&#x1F697', '&#x1F3C3', '&#x1F3C2', '&#x1F3CA', '&#x1F3C4', '&#x1F3BF', '&#x26BD', '&#x1F3C8', '&#x1F3C0','&#x1F941')
                ),
                array(
                    'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Money'),
                    'icons' => array('&#x1F4B0','&#x1F4B5','&#x1F4B3','&#x1F4B2')
                )
            );
            ?>

            <div class="row">
                <div class="col-12">
                    <button type="button" <?php if ($react == true) : ?>id="react-close-modal"<?php endif;?> class="close float-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <ul class="nav nav-pills nav-pills-bbcode"  role="tablist">
                        <?php foreach ($icons as $index => $iconGroup) : ?>
                            <li class="nav-item" role="presentation" ><a class="nav-link px-2 py-1 small <?php if ($index == 0) : ?>active<?php endif;?>" href="#bbcode-smiley-<?php echo $index?>" aria-controls="bbcode-smiley-<?php echo $index?>" role="tab" data-toggle="tab"><?php echo htmlspecialchars($iconGroup['title'])?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="tab-content nav-pills-bbcode-content">
                        <?php foreach ($icons as $index => $iconGroup) : ?>
                            <div role="tabpanel" class="tab-pane bb-list<?php if ($index == 0) : ?> active<?php endif;?><?php if ($chat_id !== null) : ?> admin-emoji<?php endif;?>" id="bbcode-smiley-<?php echo $index?>">
                                    <?php if ($index == 0) : ?>
                                        <a bbitem="true" class="rounded d-inline-block badge-light p-1 m-1 action-image" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Image'); ?>" data-promt="img" data-bb-code="img"><i class="material-icons mr-0"><?php if ($react == true) : ?>&#xf114;<?php else : ?>image<?php endif; ?></i></a>
                                        <a bbitem="true" class="rounded d-inline-block badge-light p-1 m-1 action-image" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Link'); ?>" data-promt="url" data-bb-code=" [url=http://example.com]<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Link title'); ?>[/url] "><i class="material-icons mr-0"><?php if ($react == true) : ?>&#xf115;<?php else : ?>link<?php endif; ?></i></a>
                                        <a bbitem="true" class="rounded d-inline-block badge-light p-1 m-1 action-image" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Bold'); ?>" data-bb-code=" [b][/b] "><strong>B</strong></a>
                                        <a bbitem="true" class="rounded d-inline-block badge-light p-1 m-1 action-image" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Italic'); ?>" data-bb-code=" [i][/i] "><em>I</em></a>
                                    <?php endif; ?>
                                    <?php foreach ($iconGroup['icons'] as $icon) : ?><a bbitem="true" class="rounded d-inline-block badge-light p-1 m-1 action-image" data-bb-code="<?php echo $icon ?>"><?php echo $icon ?></a><?php endforeach; ?>
                                </ul>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if ($react != true) : ?>
            <script>
                $('.bb-list a').click(function () {

                    <?php if ($mode == 'editor') : ?>
                        var selectorInsert = window.lhcSelector;
                    <?php else : ?>
                        var selectorInsert = "#CSChatMessage<?php $chat_id !== null ? print '-' . $chat_id : null?>";
                    <?php endif; ?>

                    var textAreaElement = jQuery(selectorInsert);

                    var caretPos = textAreaElement[0].selectionStart;
                    var textAreaTxt = jQuery(selectorInsert).val();

                    var txtToAdd = $(this).attr('data-bb-code');
                    if (typeof $(this).attr('data-promt') != 'undefined' && $(this).attr('data-promt') == 'img') {
                        var link = prompt("<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Please enter link to an image')?>");
                        if (link) {
                            txtToAdd = '[' + txtToAdd + ']' + link + '[/' + txtToAdd + ']';
                            textAreaElement.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
                            $('#myModal').modal('hide');
                        }
                    } else if (typeof $(this).attr('data-promt') != 'undefined' && $(this).attr('data-promt') == 'url') {
                        var link = prompt("<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Please enter a link')?>");
                        if (link) {
                            txtToAdd = '[url=' + link + ']<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/bbcodeinsert', 'Here is a link')?>[/url]';
                            textAreaElement.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
                            $('#myModal').modal('hide');
                        }
                    } else {
                        textAreaElement.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos)).focus();
                        $('#myModal').modal('hide');
                        setTimeout(function () {
                            textAreaElement.focus();
                        },500)
                     };

                    return false;
            });</script>
            <?php endif; ?>

        </div>
    </div>
</div>