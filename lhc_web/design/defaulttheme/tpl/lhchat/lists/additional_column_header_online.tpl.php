<th width=15%" nowrap="nowrap" ng-repeat="column in lhc.additionalColumns">
    <i ng-if="column.icon !== ''" class="material-icons">{{column.icon}}</i>{{column.name}}
</th>