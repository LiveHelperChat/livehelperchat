<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 pl-2 pr-2">
            <h4 class="modal-title" id="myModalLabel">Usage Help</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <p>
                <?php if ($context == 'text') : ?>
                    <ul>
                        <li>{<translation>__default message__t[show from hour, show till hour]} inclusive is first hour. Few examples
                                <ul>
                                    <li>Default message</li>
                                    <li>{welcome_message__Welcome to our website}</li>
                                    <li>{good_evening__Good evening__t[17:24]} - Show this message from 17 until midnight</li>
                                    <li>{good_morning__Good morning__t[0:17]} - Show this message from midnight until evening</li>
                                </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </p>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>