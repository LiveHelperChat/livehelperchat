<div>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/blockedusers.tpl.php'));?>

<?php if (isset($block_saved) && $block_saved == true) : ?>
		<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Updated'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div class="row" ng-non-bindable>
    <div class="col-6">
        <form class="mb-2" action="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>" >
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control form-control-sm" name="ip" value="<?php echo htmlspecialchars($input->ip)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP');?>" />
                </div>
                <div class="col">
                    <input type="text" class="form-control form-control-sm" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick/Email');?>" />
                </div>
                <div class="col-2">
                    <input type="submit" class="btn btn-sm btn-secondary w-100" name="doSearch" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Search');?>" />
                    <?php if (isset($enabled_log)) : ?>
                        <a target="_blank" class="text-muted d-block pt-1" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block history');?>" href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Audit/(category)/block/(source)/lhc"><span class="material-icons">history</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block history');?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
        </form>
    </div>
    <div class="col-6">
        <form class="mb-2" action="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"  method="post">
            <div class="row">
                <div class="col-6">
                    <input type="text" class="form-control form-control-sm" name="IPToBlock" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP/E-mail');?>" />
                </div>
                <div class="col-6">
                    <div class="btn-group w-100" role="group" aria-label="Basic example">
                        <input type="submit" class="btn btn-sm btn-warning w-100" name="AddBlock" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Block IP');?>" />
                        <input type="submit" class="btn btn-sm btn-warning w-100" name="AddBlockEmail" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Block e-mail');?>" />
                    </div>
                </div>
                <div class="col-12 pt-1">
                    <div class="row">
                        <div class="col-3">
                                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                                    'input_name'     => 'AddBlockCountryDep',
                                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                                    'selected_id'    => "0",
                                    'type'           => 'radio',
                                    'data_prop'      => 'data-limit="1"',
                                    'css_class'      => 'form-control',
                                    'display_name'   => 'name',
                                    'show_optional'  => true,
                                    'list_function_params' => array('limit' => false, 'sort' => '`name` ASC'),
                                    'list_function'  => 'erLhcoreClassModelDepartament::getList',
                                )); ?>
                        </div>
                        <div class="col-3">
                            <?php $countryList = json_decode('[{"Code": "AF", "Name": "Afghanistan"},{"Code": "AX", "Name": "\u00c5land Islands"},{"Code": "AL", "Name": "Albania"},{"Code": "DZ", "Name": "Algeria"},{"Code": "AS", "Name": "American Samoa"},{"Code": "AD", "Name": "Andorra"},{"Code": "AO", "Name": "Angola"},{"Code": "AI", "Name": "Anguilla"},{"Code": "AQ", "Name": "Antarctica"},{"Code": "AG", "Name": "Antigua and Barbuda"},{"Code": "AR", "Name": "Argentina"},{"Code": "AM", "Name": "Armenia"},{"Code": "AW", "Name": "Aruba"},{"Code": "AU", "Name": "Australia"},{"Code": "AT", "Name": "Austria"},{"Code": "AZ", "Name": "Azerbaijan"},{"Code": "BS", "Name": "Bahamas"},{"Code": "BH", "Name": "Bahrain"},{"Code": "BD", "Name": "Bangladesh"},{"Code": "BB", "Name": "Barbados"},{"Code": "BY", "Name": "Belarus"},{"Code": "BE", "Name": "Belgium"},{"Code": "BZ", "Name": "Belize"},{"Code": "BJ", "Name": "Benin"},{"Code": "BM", "Name": "Bermuda"},{"Code": "BT", "Name": "Bhutan"},{"Code": "BO", "Name": "Bolivia, Plurinational State of"},{"Code": "BQ", "Name": "Bonaire, Sint Eustatius and Saba"},{"Code": "BA", "Name": "Bosnia and Herzegovina"},{"Code": "BW", "Name": "Botswana"},{"Code": "BV", "Name": "Bouvet Island"},{"Code": "BR", "Name": "Brazil"},{"Code": "IO", "Name": "British Indian Ocean Territory"},{"Code": "BN", "Name": "Brunei Darussalam"},{"Code": "BG", "Name": "Bulgaria"},{"Code": "BF", "Name": "Burkina Faso"},{"Code": "BI", "Name": "Burundi"},{"Code": "KH", "Name": "Cambodia"},{"Code": "CM", "Name": "Cameroon"},{"Code": "CA", "Name": "Canada"},{"Code": "CV", "Name": "Cape Verde"},{"Code": "KY", "Name": "Cayman Islands"},{"Code": "CF", "Name": "Central African Republic"},{"Code": "TD", "Name": "Chad"},{"Code": "CL", "Name": "Chile"},{"Code": "CN", "Name": "China"},{"Code": "CX", "Name": "Christmas Island"},{"Code": "CC", "Name": "Cocos (Keeling) Islands"},{"Code": "CO", "Name": "Colombia"},{"Code": "KM", "Name": "Comoros"},{"Code": "CG", "Name": "Congo"},{"Code": "CD", "Name": "Congo, the Democratic Republic of the"},{"Code": "CK", "Name": "Cook Islands"},{"Code": "CR", "Name": "Costa Rica"},{"Code": "CI", "Name": "C\u00f4te d\'Ivoire"},{"Code": "HR", "Name": "Croatia"},{"Code": "CU", "Name": "Cuba"},{"Code": "CW", "Name": "Cura\u00e7ao"},{"Code": "CY", "Name": "Cyprus"},{"Code": "CZ", "Name": "Czech Republic"},{"Code": "DK", "Name": "Denmark"},{"Code": "DJ", "Name": "Djibouti"},{"Code": "DM", "Name": "Dominica"},{"Code": "DO", "Name": "Dominican Republic"},{"Code": "EC", "Name": "Ecuador"},{"Code": "EG", "Name": "Egypt"},{"Code": "SV", "Name": "El Salvador"},{"Code": "GQ", "Name": "Equatorial Guinea"},{"Code": "ER", "Name": "Eritrea"},{"Code": "EE", "Name": "Estonia"},{"Code": "ET", "Name": "Ethiopia"},{"Code": "FK", "Name": "Falkland Islands (Malvinas)"},{"Code": "FO", "Name": "Faroe Islands"},{"Code": "FJ", "Name": "Fiji"},{"Code": "FI", "Name": "Finland"},{"Code": "FR", "Name": "France"},{"Code": "GF", "Name": "French Guiana"},{"Code": "PF", "Name": "French Polynesia"},{"Code": "TF", "Name": "French Southern Territories"},{"Code": "GA", "Name": "Gabon"},{"Code": "GM", "Name": "Gambia"},{"Code": "GE", "Name": "Georgia"},{"Code": "DE", "Name": "Germany"},{"Code": "GH", "Name": "Ghana"},{"Code": "GI", "Name": "Gibraltar"},{"Code": "GR", "Name": "Greece"},{"Code": "GL", "Name": "Greenland"},{"Code": "GD", "Name": "Grenada"},{"Code": "GP", "Name": "Guadeloupe"},{"Code": "GU", "Name": "Guam"},{"Code": "GT", "Name": "Guatemala"},{"Code": "GG", "Name": "Guernsey"},{"Code": "GN", "Name": "Guinea"},{"Code": "GW", "Name": "Guinea-Bissau"},{"Code": "GY", "Name": "Guyana"},{"Code": "HT", "Name": "Haiti"},{"Code": "HM", "Name": "Heard Island and McDonald Islands"},{"Code": "VA", "Name": "Holy See (Vatican City State)"},{"Code": "HN", "Name": "Honduras"},{"Code": "HK", "Name": "Hong Kong"},{"Code": "HU", "Name": "Hungary"},{"Code": "IS", "Name": "Iceland"},{"Code": "IN", "Name": "India"},{"Code": "ID", "Name": "Indonesia"},{"Code": "IR", "Name": "Iran, Islamic Republic of"},{"Code": "IQ", "Name": "Iraq"},{"Code": "IE", "Name": "Ireland"},{"Code": "IM", "Name": "Isle of Man"},{"Code": "IL", "Name": "Israel"},{"Code": "IT", "Name": "Italy"},{"Code": "JM", "Name": "Jamaica"},{"Code": "JP", "Name": "Japan"},{"Code": "JE", "Name": "Jersey"},{"Code": "JO", "Name": "Jordan"},{"Code": "KZ", "Name": "Kazakhstan"},{"Code": "KE", "Name": "Kenya"},{"Code": "KI", "Name": "Kiribati"},{"Code": "KP", "Name": "Korea, Democratic People\'s Republic of"},{"Code": "KR", "Name": "Korea, Republic of"},{"Code": "KW", "Name": "Kuwait"},{"Code": "KG", "Name": "Kyrgyzstan"},{"Code": "LA", "Name": "Lao People\'s Democratic Republic"},{"Code": "LV", "Name": "Latvia"},{"Code": "LB", "Name": "Lebanon"},{"Code": "LS", "Name": "Lesotho"},{"Code": "LR", "Name": "Liberia"},{"Code": "LY", "Name": "Libya"},{"Code": "LI", "Name": "Liechtenstein"},{"Code": "LT", "Name": "Lithuania"},{"Code": "LU", "Name": "Luxembourg"},{"Code": "MO", "Name": "Macao"},{"Code": "MK", "Name": "Macedonia, the Former Yugoslav Republic of"},{"Code": "MG", "Name": "Madagascar"},{"Code": "MW", "Name": "Malawi"},{"Code": "MY", "Name": "Malaysia"},{"Code": "MV", "Name": "Maldives"},{"Code": "ML", "Name": "Mali"},{"Code": "MT", "Name": "Malta"},{"Code": "MH", "Name": "Marshall Islands"},{"Code": "MQ", "Name": "Martinique"},{"Code": "MR", "Name": "Mauritania"},{"Code": "MU", "Name": "Mauritius"},{"Code": "YT", "Name": "Mayotte"},{"Code": "MX", "Name": "Mexico"},{"Code": "FM", "Name": "Micronesia, Federated States of"},{"Code": "MD", "Name": "Moldova, Republic of"},{"Code": "MC", "Name": "Monaco"},{"Code": "MN", "Name": "Mongolia"},{"Code": "ME", "Name": "Montenegro"},{"Code": "MS", "Name": "Montserrat"},{"Code": "MA", "Name": "Morocco"},{"Code": "MZ", "Name": "Mozambique"},{"Code": "MM", "Name": "Myanmar"},{"Code": "NA", "Name": "Namibia"},{"Code": "NR", "Name": "Nauru"},{"Code": "NP", "Name": "Nepal"},{"Code": "NL", "Name": "Netherlands"},{"Code": "NC", "Name": "New Caledonia"},{"Code": "NZ", "Name": "New Zealand"},{"Code": "NI", "Name": "Nicaragua"},{"Code": "NE", "Name": "Niger"},{"Code": "NG", "Name": "Nigeria"},{"Code": "NU", "Name": "Niue"},{"Code": "NF", "Name": "Norfolk Island"},{"Code": "MP", "Name": "Northern Mariana Islands"},{"Code": "NO", "Name": "Norway"},{"Code": "OM", "Name": "Oman"},{"Code": "PK", "Name": "Pakistan"},{"Code": "PW", "Name": "Palau"},{"Code": "PS", "Name": "Palestine, State of"},{"Code": "PA", "Name": "Panama"},{"Code": "PG", "Name": "Papua New Guinea"},{"Code": "PY", "Name": "Paraguay"},{"Code": "PE", "Name": "Peru"},{"Code": "PH", "Name": "Philippines"},{"Code": "PN", "Name": "Pitcairn"},{"Code": "PL", "Name": "Poland"},{"Code": "PT", "Name": "Portugal"},{"Code": "PR", "Name": "Puerto Rico"},{"Code": "QA", "Name": "Qatar"},{"Code": "RE", "Name": "R\u00e9union"},{"Code": "RO", "Name": "Romania"},{"Code": "RU", "Name": "Russian Federation"},{"Code": "RW", "Name": "Rwanda"},{"Code": "BL", "Name": "Saint Barth\u00e9lemy"},{"Code": "SH", "Name": "Saint Helena, Ascension and Tristan da Cunha"},{"Code": "KN", "Name": "Saint Kitts and Nevis"},{"Code": "LC", "Name": "Saint Lucia"},{"Code": "MF", "Name": "Saint Martin (French part)"},{"Code": "PM", "Name": "Saint Pierre and Miquelon"},{"Code": "VC", "Name": "Saint Vincent and the Grenadines"},{"Code": "WS", "Name": "Samoa"},{"Code": "SM", "Name": "San Marino"},{"Code": "ST", "Name": "Sao Tome and Principe"},{"Code": "SA", "Name": "Saudi Arabia"},{"Code": "SN", "Name": "Senegal"},{"Code": "RS", "Name": "Serbia"},{"Code": "SC", "Name": "Seychelles"},{"Code": "SL", "Name": "Sierra Leone"},{"Code": "SG", "Name": "Singapore"},{"Code": "SX", "Name": "Sint Maarten (Dutch part)"},{"Code": "SK", "Name": "Slovakia"},{"Code": "SI", "Name": "Slovenia"},{"Code": "SB", "Name": "Solomon Islands"},{"Code": "SO", "Name": "Somalia"},{"Code": "ZA", "Name": "South Africa"},{"Code": "GS", "Name": "South Georgia and the South Sandwich Islands"},{"Code": "SS", "Name": "South Sudan"},{"Code": "ES", "Name": "Spain"},{"Code": "LK", "Name": "Sri Lanka"},{"Code": "SD", "Name": "Sudan"},{"Code": "SR", "Name": "Suriname"},{"Code": "SJ", "Name": "Svalbard and Jan Mayen"},{"Code": "SZ", "Name": "Swaziland"},{"Code": "SE", "Name": "Sweden"},{"Code": "CH", "Name": "Switzerland"},{"Code": "SY", "Name": "Syrian Arab Republic"},{"Code": "TW", "Name": "Taiwan, Province of China"},{"Code": "TJ", "Name": "Tajikistan"},{"Code": "TZ", "Name": "Tanzania, United Republic of"},{"Code": "TH", "Name": "Thailand"},{"Code": "TL", "Name": "Timor-Leste"},{"Code": "TG", "Name": "Togo"},{"Code": "TK", "Name": "Tokelau"},{"Code": "TO", "Name": "Tonga"},{"Code": "TT", "Name": "Trinidad and Tobago"},{"Code": "TN", "Name": "Tunisia"},{"Code": "TR", "Name": "Turkey"},{"Code": "TM", "Name": "Turkmenistan"},{"Code": "TC", "Name": "Turks and Caicos Islands"},{"Code": "TV", "Name": "Tuvalu"},{"Code": "UG", "Name": "Uganda"},{"Code": "UA", "Name": "Ukraine"},{"Code": "AE", "Name": "United Arab Emirates"},{"Code": "GB", "Name": "United Kingdom"},{"Code": "US", "Name": "United States"},{"Code": "UM", "Name": "United States Minor Outlying Islands"},{"Code": "UY", "Name": "Uruguay"},{"Code": "UZ", "Name": "Uzbekistan"},{"Code": "VU", "Name": "Vanuatu"},{"Code": "VE", "Name": "Venezuela, Bolivarian Republic of"},{"Code": "VN", "Name": "Viet Nam"},{"Code": "VG", "Name": "Virgin Islands, British"},{"Code": "VI", "Name": "Virgin Islands, U.S."},{"Code": "WF", "Name": "Wallis and Futuna"},{"Code": "EH", "Name": "Western Sahara"},{"Code": "YE", "Name": "Yemen"},{"Code": "ZM", "Name": "Zambia"},{"Code": "ZW", "Name": "Zimbabwe"}]',true);?>
                            <input type="text" class="form-control form-control-sm" name="AddBlockCountry" value="" list="country_list">
                            <datalist id="country_list" autocomple="new-password">
                                <?php foreach ($countryList as $country) : ?>
                                <option value="<?php echo htmlspecialchars(strtolower($country['Code']))?>"><?php echo htmlspecialchars($country['Name'])?></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-6">
                            <input type="submit" class="btn btn-sm btn-warning w-100" name="AddBlockCountryButton" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Block country');?>" />
                        </div>
                    </div>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
        </form>
    </div>
</div>

<script>
    $(function() {
        $('.btn-block-department').makeDropdown();
    });
</script>



<?php if (!empty($items)) : ?>
<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th width="1%">ID</th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block type');?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Department');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick/E-mail/Country');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Expires in');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block date');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','User who blocked');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td title="<?php echo $item->btype?>">

            <?php if (isset($enabled_log)) : ?>
                <a class="text-danger" target="_blank" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block history');?>" href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Audit/(category)/block/(source)/lhc/(object_id)/<?php echo $item->id?>"><span class="material-icons me-0">history</span></a>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_IP,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK_DEP])) : ?>
                <span class="badge bg-secondary">IP</span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_NICK,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK])) : ?>
                <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick');?></span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_COUNTRY])) : ?>
                <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Country');?></span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_NICK_DEP,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK_DEP])) : ?>
                <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick and Department');?></span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_EMAIL])) : ?>
                <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','E-mail');?></span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_ONLINE_USER])) : ?>
                <span class="badge bg-secondary" title="<?php echo $item->online_user_id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Online user');?></span>
            <?php endif; ?>

            <?php if ($item->chat_id > 0) : ?><a class="material-icons" title="<?php echo htmlspecialchars($item->chat_id)?>" onclick="lhc.previewChat(<?php echo $item->chat_id?>)">info_outline</a><?php endif; ?><?php
            if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','seeip')) {
                echo htmlspecialchars($item->ip);
            } else {
                echo htmlspecialchars(preg_replace(
                    [
                        '/(\.\d+){2}$/',
                        '/(:[\da-f]*){2,4}$/'
                    ],
                    [
                        '.XXX.XXX',
                        ':XXXX:XXXX:XXXX:XXXX'
                    ],
                    $item->ip
                ));
            }
            ?>
        </td>
        <td><?php echo htmlspecialchars((string)$item->department)?></td>
        <td><?php echo htmlspecialchars($item->nick)?></td>
        <td><?php echo htmlspecialchars($item->expires_front)?> [<?php echo $item->block_duration?>]</td>
        <td><?php echo htmlspecialchars($item->datets_front)?></td>
        <td><?php echo htmlspecialchars($item->user)?></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>/(remove_block)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Remove block');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Empty...');?></p>
<?php endif; ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
</div>
