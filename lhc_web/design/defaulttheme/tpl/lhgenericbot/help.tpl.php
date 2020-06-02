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

            <?php if ($context == 'execute_js') : ?>
                <ul>
                    <li>Few example. If you are using new widget you can execute the following examples
                        <ul>
                            <li>alert('Hello')</li>
                            <li>console.log(window.parent)</li>
                            <li>window.parent.document.body.style = "background-color: red";</li>
                            <li>window.parent.document.title = "Change main window title";</li>
                            <li>window.parent.callMe()</li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>

            <?php if ($context == 'attribute_identifier') : ?>
            <ul>
                <li>Few examples
                    <ul>
                        <li><strong>lhc.email</strong> - set user provided data as chat e-mail</li>
                        <li><strong>lhc.nick</strong> - set user provided data as visitor username</li>
                        <li><strong>lhc.phone</strong> - set user provided data as visitor phone</li>
                        <li><strong>order_number</strong> - non internal attribute. Can be anything. Like order number</li>
                    </ul>
                </li>
            </ul>
            <?php endif; ?>

            <?php if ($context == 'attribute_name') : ?>
                <p>This field be used only if you are collecting non internal attribute. This will be used as display name for that attribute in back office.</p>
            <?php endif; ?>

            <?php if ($context == 'execute_tbody') : ?>
                <p>You can use this field as response body from your Rest API integration. You can paste content from `Show code` section.</p>
            <?php endif; ?>

            <?php if ($context == 'preg_match') : ?>
                    <ul>
                        <li>Few examples
                                <ul>
                                    <li><strong>^.{5,}+$</strong> string with minimum 5 characters</li>
                                    <li><strong>foo</strong> The string “foo”</li>
                                    <li><strong>^foo</strong> “foo” at the start of a string</li>
                                    <li><strong>foo$</strong> “foo” at the end of a string</li>
                                    <li><strong> ^foo$</strong> “foo” when it is alone on a string</li>
                                    <li><strong>[abc]</strong> a, b, or c</li>
                                    <li><strong>[a-z]</strong> Any lowercase letter</li>
                                    <li><strong>[^A-Z]</strong> Any character that is not a uppercase letter</li>
                                    <li><strong>(gif|jpg)</strong> Matches either “gif” or “jpg”</li>
                                    <li><strong>[a-z]+</strong> One or more lowercase letters</li>
                                    <li><strong>[0-9.-]</strong> Any number, dot, or minus sign</li>
                                    <li><strong>^[a-zA-Z0-9_]{1,}$</strong> Any word of at least one letter, number or _</li>
                                    <li><strong>([wx])([yz])</strong> wy, wz, xy, or xz</li>
                                    <li><strong>[^A-Za-z0-9]</strong> Any symbol (not a number or a letter)</li>
                                    <li><strong>([A-Z]{3}|[0-9]{4})</strong> Matches three letters or four numbers</li>
                                    <li><strong>([A-Z]{3}[0-9]{4})</strong> Matches three letters followed by four numbers</li>
                                </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </p>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>