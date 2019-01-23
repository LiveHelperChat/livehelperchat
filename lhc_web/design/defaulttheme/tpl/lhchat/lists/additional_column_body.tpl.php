<td ng-repeat="column in lhc.additionalColumns" ng-if="column.cenabl == true">
    <span ng-repeat="val in column.items">{{chat[val]}}&nbsp;</span>
</td>