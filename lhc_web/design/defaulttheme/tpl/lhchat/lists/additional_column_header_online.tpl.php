<th nowrap="nowrap" ng-repeat="column in lhc.additionalColumns" ng-if="column.oenabl == true">
    <i ng-if="column.icon !== ''" class="material-icons">{{column.icon}}</i>{{column.name}}
</th>