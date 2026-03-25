<div class="d-flex flex-wrap gap-2">
    <?php foreach($metaMessage as $messageAttachement) : ?>
        <?php if (isset($messageAttachement['id']) && isset($messageAttachement['security_hash'])) : ?>
                <?php
                $msgBody = '[file='.$messageAttachement['id'].'_'.$messageAttachement['security_hash'] .']';
                $paramsMessageRender = array(
                        'img_verify_min_dim' => (isset($data['img_verify_min_dim']) ? $data['img_verify_min_dim'] : 100),
                        'print_admin' => (isset($print_admin) && $print_admin === true),
                        'download_policy' => $download_policy,
                        'operator_render' => true,
                        'no_reactions' => true,
                        'sender' => 0)
                ?>
                <div class="attachment-item">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
                </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
