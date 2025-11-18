<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?>*</label>
    <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Identifier');?>*</label>
    <input type="text" class="form-control form-control-sm" name="identifier" value="<?php echo htmlspecialchars($item->identifier);?>" />
</div>

<script>
    var priorityValue = <?php echo $item->configuration != '' ? $item->configuration : '[]'?>;
</script>

<div ng-controller="LHCPriorityCtrl as pchat" ng-init='pchat.setValue()'>

    <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Main conditions');?> <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/cannedreplacerules'});" class="material-icons text-muted">help</a></h6>

    <p class="text-muted fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','If no conditions are defined, it is considered as invalid.');?></p>

    <textarea class="hide" name="configuration">{{pchat.value | json : 0}}</textarea>

    <div class="btn-group btn-group-sm me-2 mb-2" role="group">
        <div class="input-group input-group-sm">
            <input type="button" ng-click="pchat.addFilter()" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Add condition');?>">
            <?php if (is_numeric($item->id)) : ?>
                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhgenericbot','test_pattern')) : ?>
                <input type="text" class="form-control form-control-sm" id="test-chat-id" name="chat_id_test" value="<?php isset($_POST['chat_id_test']) ? print (int)$_POST['chat_id_test'] : '';?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat ID');?>" value="">
                <button type="button" id="check-against-chat" class="btn btn-sm btn-secondary" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Make sure to save condition first.');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Check against chat');?></button>
                <?php endif; ?>
                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhgenericbot','use_cases')) : ?>
                <button type="button" id="btn-use-cases" class="btn btn-sm btn-secondary" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Investigate places where this condition is used');?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Use cases');?></button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (is_numeric($item->id)) : ?>
        <script>
            $('#check-against-chat').click(function(){
                $.post(WWW_DIR_JAVASCRIPT + 'genericbot/testpattern/' + $('#test-chat-id').val(), {'condition_id' : <?php echo $item->id?>}, function(data){
                    $('#output-test').html('<pre class="fs11">'+data+'</pre>');
                });
            });
            $('#btn-use-cases').click(function(){
                lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/usecases/condition/<?php echo $item->id?>'});
            });
        </script>
    <?php endif; ?>

    <div class="row" ng-show="pchat.value.length > 0">
        <div class="col-11">
            <div class="row">
                <div class="col-5">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Field');?></label>
                </div>
                <div class="col-2">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Condition');?></label>
                </div>
                <div class="col-5">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Value');?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="row form-group" ng-repeat="filter in pchat.value">
        <div class="col-12">
            <div class="row">
                <div class="col-11">
                    <div class="row">
                        <div class="col-5" ng-show="filter.comparator != 'start_or'">
                            <input class="form-control form-control-sm" ng-model="filter.field" name="field[{{$index}}]" type="text" value="" placeholder="field" list="field-options-{{$index}}">
                            <datalist id="field-options-{{$index}}">
                                <option value="lhc.id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat ID or any other chat variable');?></option>
                                <option value="lhc.status"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat Status');?></option>
                                <option value="lhc.dep_id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Department ID');?></option>
                                <option value="lhc.user_id"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','User ID');?></option>
                                <option value="lhc.email"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Email');?></option>
                                <option value="lhc.phone"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Phone');?></option>
                                <option value="lhc.nick"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Nickname');?></option>
                                <option value="lhc.country_code"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Country Code');?></option>
                                <option value="{args.chat.chat_variables_array.msg_v}"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat Variable');?></option>
                                <option value="msg_v"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat variable accessible directly');?></option>
                                <option value="{condition.identifier}"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Reuse existing conditions');?></option>
                                <option value="{args.chat.id}"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Chat ID or any other chatr variable');?></option>
                            </datalist>
                            <label><input type="checkbox" ng-model="filter.field_math" name="field_math[{{$index}}]" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Parse for mathematical outcome');?></label>
                        </div>
                        <div class="col-{{filter.comparator == 'start_or' ? '2  offset-md-5' : '2'}}">
                            <select class="form-control form-control-sm" name="comparator[{{$index}}]" ng-model="filter.comparator">
                                <option value="&gt;">&gt;</option>
                                <option value="&lt;">&lt;</option>
                                <option value="&gt;=">&gt;=</option>
                                <option value="&lt;=">&lt;=</option>
                                <option value="=">=</option>
                                <option value="!=">!=</option>
                                <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Text like')?></option>
                                <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Text not like')?></option>
                                <option value="contains"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Contains')?></option>
                                <option value="start_or"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Start of OR')?></option>
                                <option value="in_list"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','In list, items separated by ||')?></option>
                                <option value="in_list_lowercase"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','In list (lowercase before comparison), items separated by ||')?></option>
                                <option value="not_in_list"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Not in list, items separated by ||')?></option>
                                <option value="not_in_list_lowercase"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Not in list (lowercase before comparison), items separated by ||')?></option>
                            </select>
                        </div>
                        <div class="col-5" ng-show="filter.comparator != 'start_or'">
                            <input class="form-control form-control-sm" ng-model="filter.value" name="value[{{$index}}]" type="text" value="" placeholder="value">
                            <label><input type="checkbox" ng-model="filter.value_math" name="value_math[{{$index}}]" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('bot/conditions','Parse for mathematical outcome');?></label>
                        </div>
                    </div>
                </div>
                <div class="col-1">
                    <button class="btn btn-danger btn-sm btn-block" ng-click="pchat.removeFilter(filter)"><i class="material-icons me-0">&#xE872;</i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (is_numeric($item->id)) : ?>
    <div id="output-test" class="ps-1 pt-1"></div>
<?php endif; ?>
