<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Description');?></label>
    <input type="text" class="form-control" name="description"  value="<?php echo htmlspecialchars($item->description);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Host');?></label>
    <input type="text" class="form-control" name="configuration" ng-model="lhcrestapi.host" value="" />
</div>

<input type="hidden" name="configuration" value="{{lhcrestapi.getJSON()}}" />

<button class="btn btn-secondary" ng-click="lhcrestapi.addParameter()" type="button">Add request</button>

<div ng-repeat="param in lhcrestapi.parameters" class="mt-2">

    <hr style="height: 5px;"/>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name of the request');?></label>
                <input type="text" class="form-control form-control-sm" ng-model="param.name" placeholder="" value="" />
            </div>
        </div>
        <div class="col-6">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Method');?></label>
            <select class="form-control form-control-sm" name="method" ng-model="param.method">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Sub URL');?></label>
                <input type="text" class="form-control form-control-sm" ng-model="param.suburl" placeholder="" value="" />
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs mb-2" role="tablist" >
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#params-rest-{{$index}}" aria-controls="params" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Params');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#authorization-rest-{{$index}}" aria-controls="authorization" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Authorization');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#headers-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Headers');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#body-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Body');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#userparams-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','User parameters');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#outputrest-rest-{{$index}}" aria-controls="headers" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Output parsing');?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="params-rest-{{$index}}">

            <p>You can put visitor message as placeholder <code ng-non-bindable>{{msg}}, {{chat_id}}</code></p>

            <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.query)">Add param</button>

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
            <p>You can always just define custom header if you do not find authorisation method here.</p>

            <div class="form-group">
                <label>Authorization method</label>
                <select ng-model="param.authorization" class="form-control form-control-sm">
                    <option value="">Skip</option>
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
                            <label>API Key Location</label>
                            <select ng-model="param.api_key_location" class="form-control form-control-sm">
                                <option value="header">Header</option>
                                <option value="queryparams">Query Params</option>
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

            <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.header)">Add param</button>

            <div ng-repeat="paramHeader in param.header" class="mt-2">
                <div class="row">
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" ng-model="paramHeader.key" placeholder="Key">
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm" ng-model="paramHeader.value" placeholder="Value">
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control form-control-sm" ng-model="paramHeader.description" placeholder="Description">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger d-block w-100 btn-xs" ng-click="lhcrestapi.deleteParam(param.header,paramHeader)">-</button>
                    </div>
                </div>
            </div>


        </div>
        <div role="tabpanel" class="tab-pane" id="body-rest-{{$index}}">

            <div class="form-group">
                <label>Request Body</label>
                <select ng-model="param.body_request_type" class="form-control form-control-sm">
                    <option value="">none</option>
                    <option value="raw">raw (Use this to send JSON Body)</option>
                    <option value="form-data">form data (Use this to send post parameters)</option>
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
                    <label>Paste your request here (E.g JSON body). You can put visitor message as placeholder <code ng-non-bindable>{{msg}}, {{chat_id}}</code></label>
                    <textarea rows="10" class="form-control form-control-sm" ng-model="param.body_raw"></textarea>
                </div>
            </div>

            <div ng-if="param.body_request_type == 'form-data'">

                <p>You can put visitor message as placeholder <code ng-non-bindable>{{msg}}, {{chat_id}}</code></p>

                <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.postparams)">Add param</button>

                <div ng-repeat="paramPost in param.postparams" class="mt-2">
                    <div class="row">
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" ng-model="paramPost.key" placeholder="Key">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" ng-model="paramPost.value" placeholder="Value">
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control form-control-sm" ng-model="paramPost.description" placeholder="Description">
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
                <p>You can define additional parameters user can enter in bot trigger directly.</p>

                <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.userparams)">Add param</button>

                <div ng-repeat="paramUser in param.userparams" class="mt-2">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Location</label>
                                <select class="form-control" ng-model="paramUser.location">
                                    <option value="">Query</option>
                                    <option value="post_param">Body Post Param</option>
                                    <option value="body_param">Body Param</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <label>Name</label>
                            <input type="text" class="form-control form-control-sm" ng-model="paramUser.value" placeholder="Name visible in trigger">
                        </div>
                        <div class="col-6">
                            <label>Location/Key</label>
                            <input type="text" class="form-control form-control-sm" ng-model="paramUser.key" placeholder="Either name or location in json ['params']['msg']" >
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="outputrest-rest-{{$index}}">

                <p>You can define response conditions to which you will be able to add corresponding triggers.</p>

                <button type="button" class="btn btn-secondary btn-xs" ng-click="lhcrestapi.addParam(param.output)">Add combination</button>

                <div ng-repeat="paramOutput in param.output" class="mt-2">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_name" placeholder="Name">
                    </div>

                    <div class="form-group">
                        <label>HTTP Code</label>
                        <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_header" placeholder="HTTP status code 200,301">
                    </div>

                    <div class="form-group">
                        <label>Response Location.</label>
                        <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location" placeholder="['response']['msg'] you can leave an empty if you want forward whole response.">
                    </div>

                    <div class="form-group">
                        <label>Meta msg location. If you support Live Helper Chat json syntax you can set location of this response.</label>
                        <input type="text" class="form-control form-control-sm" ng-model="paramOutput.success_location_meta" placeholder="['response']['meta_msg']">
                    </div>
                </div>

        </div>
    </div>
</div>

<hr>