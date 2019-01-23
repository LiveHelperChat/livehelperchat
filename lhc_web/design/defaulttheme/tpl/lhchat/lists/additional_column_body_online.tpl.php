<td ng-repeat="column in lhc.additionalColumns" ng-if="column.oenabl == true">
    <span ng-repeat="val in column.items">{{ou[val]}}&nbsp;</span>
</td>