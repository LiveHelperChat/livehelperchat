<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel">Usage Help</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>

            <?php if (preg_match('/^[a-z0-9-]+/i', $context) &&($pathDynamic = erLhcoreClassDesign::designtpldynamic('lhgenericbot/helpattributes/' . $context . '.tpl.php')) && $pathDynamic !== null ) : ?>
                <?php include $pathDynamic;?>
            <?php endif; ?>
            
            <?php if ($context == 'text') : ?>
                <ul>
                    <li>{&lt;translation&gt;__default message__t[show from hour, show till hour]} inclusive is first hour. Few examples
                            <ul>
                                <li>Default message</li>
                                <li>{welcome_message__Welcome to our website}</li>
                                <li>{good_evening__Good evening__t[17:24]} - Show this message from 17 until midnight</li>
                                <li>{good_morning__Good morning__t[0:17]} - Show this message from midnight until evening</li>
                                <li>{good_overnight__Good morning__t[22:4]} - Show this message from 22 till 4 in the morning</li>
                                <li>{good_morning_monday__Good morning monday__t[7:9]||1} - Show this message only on Monday</li>
                                <li>{good_morning_tue_wed__Good morning monday__t[7:9]||2,3} - Show this message only on Tuesday and Wednesday</li>
                            </ul>
                    </li>
                <li>Special functions are usefull in case you have checked <span class="badge bg-info">Save content as JSON.</span></li>
                <li>Special functions for replaceable variables. Ensure you have checked <span class="badge bg-info">Encode all replaceable variables as JSON.</span>
                    <ul>
                        <li><span class="badge bg-info">rawjson_{content_1}</span> - Use for objects/arrays.<br>
                            Rest API response : <span class="badge bg-info">json_encode(['price' => 100, 'currency' => 'EUR']);</span> Text body definition <span class="badge bg-info">"Should be rawjson_{content_1}"</span> resulting in <span class="badge bg-info">"Should be {\"price\":100,\"currency\":\"EUR\"}"</span>
                        </li>
                        <li><span class="badge bg-info">json_{content_1}</span> - Use for objects/arrays.<br>
                            Rest API response : <span class="badge bg-info">json_encode(['price' => 100, 'currency' => 'EUR']);</span> Text body definition: <span class="badge bg-info">"output": json_{content_1}</span> results in <span class="badge bg-info">"output": "{\"price\":100,\"currency\":\"EUR\"}"</span>
                        </li>
                        <li><span class="badge bg-info">raw_{content_1}</span> - Use for strings/numbers. Required - Encode all replaceable variables as JSON.<br>
                            Rest API response : <span class="badge bg-info">json_encode("Funny it\"s 30 EUR");</span> Example: <span class="badge bg-info">"output": "should be - raw_{content_1}"</span> results in <span class="badge bg-info">"output": "Should be Funny it\"s 30 EUR"</span>
                        </li>
                        <li><span class="badge bg-info">direct_{content_1}</span> - Use for strings/numbers in bot individualization. Required - Encode all replaceable variables as JSON.<br>
                            Rest API response : <span class="badge bg-info">json_encode("Funny it\"s 30 EUR");</span> Example: <span class="badge bg-info">"output": {support_price__Should be fine direct_{content_1}}</span> results in <span class="badge bg-info">"output": "Should be Funny it\"s 30 EUR"</span>
                        </li>
                        <li><span class="badge bg-info">{content_1}</span> - Use for all.<br>
                            Example: <span class="badge bg-info">"output": {content_1}</span> results in <span class="badge bg_info">"output": "Funny it\"s 30 EUR"</span>
                        </li>
                    </ul>
                </li>
                <li>Special functions for <span class="badge bg-info">{args*}</span>. Ensure you have checked <span class="badge bg-info">Encode arrays and objects of args.* variables as JSON</span>
                    <ul>
                        <li><span class="badge bg-info">{args.chat.chat_variables_array.list__json}</span> - Use for objects/arrays.<br>
                            Example: <span class="badge bg-info">"output": {args.chat.chat_variables_array.list__json}</span> results in <span class="badge bg-info">"[\"item_1\", \"items_2\", \"item_3\"]"</span>
                        </li>
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
                        <li><strong>[file]</strong> - this attributes expects that visitor would upload a file. Preg match rule can look like <strong>(gif|jpg|png)</strong></li>
                        <li><strong>[msg]</strong> - we will not store this text as an attribute, but only as a normal visitor message.</li>
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
                                    <li><strong>^[1-2]{1}$</strong> Only 1 or 2 is accepted</li>
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

            <?php if ($context == 'matchingruleconditions') : ?>
                <ul>
                    <li>Few examples
                        <?php include(erLhcoreClassDesign::designtpl('lhmailconv/help/matchingrule.tpl.php'));?>
                    </li>
                </ul>
            <?php endif; ?>

            <?php if ($context == 'personalmailbox') : ?>
                <ul>
                    <li>Once you setup personal mailbox group each time mail comes to any of the defined mailbox conversation mailbox will be changed to the operator mailbox if he has one defined in mailbox groups.</li>
                    <li>Example. Mailbox group consists of these combinations
                        <ul>
                            <li>Mailbox group consist of <b>a@example.com</b> who belongs to operator <b>A</b></li>
                            <li>Mailbox group consist of <b>b@example.com</b> who belongs to operator <b>B</b></li>
                        </ul>
                    </li>
                    <li>
                        If mail is send to <b>a@example.com</b> but operator <b>B</b> accepts it. Conversation mailbox upon chat acceptance will be changed to <b>b@example.com</b>. So reply send will be seen as send from <b>b@example.com</b>
                    </li>
                </ul>
            <?php endif; ?>

            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/helpattributes/mailtemplates.tpl.php'));?>

            </p>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>