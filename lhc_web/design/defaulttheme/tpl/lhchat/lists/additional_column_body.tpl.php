<td ng-repeat="column in lhc.additionalColumns" ng-if="column.cenabl == true">
    <div class="abbr-list" ng-repeat="val in column.items">{{chat[val]}}&nbsp;</div>
</td>