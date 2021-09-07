<div ng-controller="LiveHelperChatViewsCtrl as vctrl">
    <div class="row">
        <div translate="no" class="col chats-column border-right pr-0 pl-0">
            <div class="w-100 d-flex flex-column flex-grow-1">
                <table class="table table-sm mb-0 table-small">
                    <thead>
                    <tr>
                        <th width="99%">
                            <i title="Name" class="material-icons">saved_search</i>
                        </th>
                        <th width="1%">
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="view in vctrl.views">
                        <td>
                            <div class="abbr-list">
                                <a class="d-block" ng-click="vctrl.loadView(view)">{{view.name}}</a>
                            </div>
                        </td>
                        <td nowrap>
                            <a href="" class="text-muted"><span class="material-icons mr-0">mode_edit</span></a>
                            <a href="" class="text-muted"><span class="material-icons mr-0">delete</span></a>
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


