<th width="20%" ng-repeat="column in lhc.additionalColumns">
    <i ng-if="column.icon !== ''" class="material-icons">{{column.icon}}</i>{{column.name}}
</th>