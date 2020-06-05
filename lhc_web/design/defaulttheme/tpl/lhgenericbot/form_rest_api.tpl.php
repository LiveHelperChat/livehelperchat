<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Name');?></label>
    <input type="text" class="form-control" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Description');?></label>
    <input type="text" class="form-control" name="description"  value="<?php echo htmlspecialchars($item->description);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Host');?></label>
    <input type="text" class="form-control" name="configuration" ng-model="lhcrestapi.host" value="" />
</div>

<input type="hidden" name="configuration" value="{{lhcrestapi.getJSON()}}" />

<button class="btn btn-secondary" ng-click="lhcrestapi.addParameter()" type="button"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Add request');?></button>

<div ng-repeat="param in lhcrestapi.parameters" class="mt-2">

    <hr style="height: 5px;"/>

    <button type="button" class="btn btn-danger btn-xs" ng-click="lhcrestapi.deleteParam(lhcrestapi.parameters,param)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Delete');?></button>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><b><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Name of the request');?></b></label>
                <input type="text" class="form-control form-control-sm" ng-model="param.name" placeholder="" value="" />
            </div>
        </div>
        <div class="col-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Method');?></label>
            <select class="form-control form-control-sm" name="method" ng-model="param.method">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Sub URL');?></label>
                <input type="text" class="form-control form-control-sm" ng-model="param.suburl" placeholder="" value="" />
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs mb-2" role="tablist" >
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#params-rest-{{$index}}" aria-controls="params" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Params');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#authorization-rest-{{$index}}" aria-controls="authorization" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Authorization');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#headers-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Headers');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#body-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Body');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#userparams-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','User parameters');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#outputrest-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Output parsing');?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="params-rest-{{$index}}">

            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','You can put visitor message as placeholder')?> <code ng-non-bindable>{{msg}}, {{msg_clean}}, {{msg_url}}, {{chat_id}}, {{lhc.nick}}, {{lhc.email}}, {{lhc.department}}, {{lhc.dep_id}}, {{ip}}, {{lhc.add. &lt;additional variable key/identifier&gt;}}, User parameters {{Location/Key}}</code></p>

            <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.query)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Add param')?></button>

            <div ng-repeat="paramQuery in param.query" class="mt-2">
                <div class="row">
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" ng-model="paramQuery.key" placeholder="Key">
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" ng-model="paramQuery.value" placeholder="Value">
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control form-control-sm" ng-model="paramQuery.description" placeholder="Description">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger d-block w-100 btn-xs" ng-click="lhcrestapi.deleteParam(param.query,paramQuery)">-</button>
                    </div>
                </div>
            </div>

        </div>
        <div role="tabpanel" class="tab-pane" id="authorization-rest-{{$index}}">
            <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','You can always just define custom header if you do not find authorisation method here.')?></p>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Authorization method')?></label>
                <select ng-model="param.authorization" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Skip')?></option>
                    <option value="basicauth">Basic Auth</option>
                    <option value="bearer">Bearer token</option>
                    <option value="apikey">API Key</option>
                </select>
            </div>

            <div ng-if="param.authorization == 'basicauth'">
                <div class="row">
                    <div class="col-6">
                        <input type="text" autocomplete="new-password" class="form-control form-control-sm" ng-model="param.auth_username" placeholder="Username">
                    </div>
                    <div class="col-6">
                        <input type="password" autocomplete="new-password" class="form-control form-control-sm" ng-model="param.auth_password" placeholder="Password">
                    </div>
                </div>
            </div>

            <div ng-if="param.authorization == 'bearer'">
                <input type="text" class="form-control form-control-sm" ng-model="param.auth_bearer" placeholder="Token">
            </div>

            <div ng-if="param.authorization == 'apikey'">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','API Key Location')?></label>
                            <select ng-model="param.api_key_location" class="form-control form-control-sm">
                                <option value="header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Header')?></option>
                                <option value="queryparams"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Query Params')?></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-6">
                        <input type="text" autocomplete="new-password" class="form-control form-control-sm" ng-model="param.auth_api_key_name" placeholder="Key">
                    </div>

                    <div class="col-6">
                        <input type="password" autocomplete="new-password" class="form-control form-control-sm" ng-model="param.auth_api_key_key" placeholder="API Key">
                    </div>
                </div>
            </div>

        </div>
        <div role="tabpanel" class="tab-pane" id="headers-rest-{{$index}}">

            <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.header)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Add param')?></button>

            <div ng-repeat="paramHeader in param.header" class="mt-2">
                <div class="row">
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" ng-model="paramHeader.key" placeholder="Key">
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" ng-model="paramHeader.value" placeholder="Value">
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control form-control-sm" ng-model="paramHeader.description" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Description')?>">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger d-block w-100 btn-xs" ng-click="lhcrestapi.deleteParam(param.header,paramHeader)">-</button>
                    </div>
                </div>
            </div>


        </div>
        <div role="tabpanel" class="tab-pane" id="body-rest-{{$index}}">

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Request Body')?></label>
                <select ng-model="param.body_request_type" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','none')?></option>
                    <option value="raw"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','raw (Use this to send JSON Body)')?></option>
                    <option value="form-data"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','form data (Use this to send post parameters)')?></option>
                </select>
            </div>


            <div ng-if="param.body_request_type == 'raw'">
                <div class="form-group">
                    <label>Request Body</label>
                    <select ng-model="param.body_request_type_content" class="form-control form-control-sm">
                        <option value="">Text</option>
                        <option value="text">Text (text/plain)</option>
                        <option value="json">JSON (application/json)</option>
                        <option value="js">Javascript (application/javascript)</option>
                        <option value="appxml">XML (application/xml)</option>
                        <option value="textxml">XML (text/xml)</option>
                        <option value="texthtml">HTML (text/html)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Paste your request here (E.g JSON body). You can put visitor message as placeholder')?> <code ng-non-bindable>{{msg}}, {{msg_clean}}, {{msg_url}}, {{chat_id}}, {{lhc.nick}}, {{lhc.email}}, {{lhc.department}}, {{lhc.dep_id}}, {{ip}}, {{lhc.add. &lt;additional variable key/identifier&gt;}}, User parameters {{Location/Key}}</code></label>
                    <textarea rows="10" class="form-control form-control-sm" ng-model="param.body_raw"></textarea>
                </div>
            </div>

            <div ng-if="param.body_request_type == 'form-data'">

                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','You can put visitor message as placeholder')?> <code ng-non-bindable>{{msg}}, {{chat_id}}</code></p>

                <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.postparams)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Add param')?></button>

                <div ng-repeat="paramPost in param.postparams" class="mt-2">
                    <div class="row">
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" ng-model="paramPost.key" placeholder="Key">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" ng-model="paramPost.value" placeholder="Value">
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control form-control-sm" ng-model="paramPost.description" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Description')?>">
                        </div>
                        <div class="col-1">
                            <button type="button" class="btn btn-danger d-block w-100 btn-xs" ng-click="lhcrestapi.deleteParam(param.postparams,paramPost)">-</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div role="tabpanel" class="tab-pane" id="userparams-rest-{{$index}}">
            <div class="form-group">
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','You can define additional parameters user can enter in bot trigger directly.')?></p>

                <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.userparams)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Add param')?></button>

                <hr>

                <div ng-repeat="paramUser in param.userparams" class="mt-2">

                    <button type="button" class="btn btn-danger btn-xs" ng-click="lhcrestapi.deleteParam(param.userparams,paramUser)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Delete')?></button>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Location')?></label>
                                <select class="form-control form-control-sm" ng-model="paramUser.location">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Query')?></option>
                                    <option value="post_param"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Body Post Param')?></option>
                                    <option value="body_param"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Body Param')?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Name')?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="paramUser.value" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Name visible in trigger')?>">
                        </div>
                        <div class="col-6">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Location/Key')?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="paramUser.key" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Either key which will be used for replacement')?>" >
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="outputrest-rest-{{$index}}">

                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','You can define response conditions to which you will be able to add corresponding triggers.')?></p>

                <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.output)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Add combination')?></button>

                <div ng-repeat="paramOutput in param.output" class="mt-2">
                    <hr>
                    <h5><button type="button" class="btn btn-danger btn-xs" ng-click="lhcrestapi.deleteParam(param.output,paramOutput)"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Delete')?></button> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Expected output definition')?></h5>

                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Name')?></label>
                        <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_name" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Name')?>">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','HTTP status code E.g 200,301,500')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_header" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','HTTP status code 200,301')?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Response format</label>
                                <select class="form-control form-control-sm" ng-model="paramOutput.format">
                                    <option value="">JSON</option>
                                    <option value="xml">XML</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location 1. Available as {content_1} in messages.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location 2. Available as {content_2} in messages.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_2" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location 3. Available as {content_3} in messages.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_3" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location 4. Available as {content_4} in messages.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_4" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location 5. Available as {content_5} in messages.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_5" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location 6. Available as {content_6} in messages.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_6" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Meta msg location. If you support Live Helper Chat JSON syntax you can set location of this response.')?></label>
                        <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_meta" placeholder="response:msg">
                    </div>

                    <h5>Conditions</h5>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','If required you can also have condition to check')?>. <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response Location. It will also fail if attribute is not found.')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_condition_val" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','response:msg you can leave an empty if you want forward whole response.')?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Response comparison')?></label>
                                <select class="form-control form-control-sm" ng-model="paramOutput.success_condition" >
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Check for presence of variable')?></option>
                                    <option value="gt">&gt;</option>
                                    <option value="gte">&gt;=</option>
                                    <option value="lt">&lt;</option>
                                    <option value="lte">&lt;=</option>
                                    <option value="eq">=</option>
                                    <option value="neq">!=</option>
                                    <option value="like"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Text like')?></option>
                                    <option value="notlike"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Text not like')?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4" ng-if="paramOutput.success_condition != '' && paramOutput.success_condition != undefined">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Comparison value')?></label>
                                <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_compare_value" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/restapi','Value to compare')?>">
                            </div>
                        </div>
                    </div>



                </div>

        </div>
    </div>
</div>

<hr>