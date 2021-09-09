<div ng-controller="LiveHelperChatViewsCtrl as vctrl">
    <div class="row">
        <div translate="no" class="col chats-column border-right pr-0 pl-0">
            <div class="w-100 d-flex flex-column flex-grow-1">
                <table class="table table-sm mb-0 table-small">
                    <thead>
                    <tr>
                        <th width="99%">
                            <span title="Name" class="material-icons">saved_search</span> My views
                        </th>
                        <th width="1%">
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="view in vctrl.views">
                        <td>
                            <div class="abbr-list p-1 fs13">
                                <a class="d-block" ng-class="{'font-weight-bold':view.id == vctrl.currentView.id}" ng-click="vctrl.loadView(view)">
                                    <span class="material-icons">{{view.scope}}</span>
                                    {{view.name}} <span class="badge pb-1 font-weight-normal badge-light" ng-if="view.total_records > 0">({{view.total_records}})</span>
                                </a>
                            </div>
                        </td>
                        <td nowrap>
                            <a href="<?php echo erLhcoreClassDesign::baseurl('views/edit')?>/{{view.id}}" class="text-muted"><span class="material-icons mr-0">mode_edit</span></a>
                            <a ng-click="vctrl.deleteView(view)" class="text-muted"><span class="material-icons mr-0">delete</span></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col" id="view-content">
            dasd
        </div>
    </div>
</div>


