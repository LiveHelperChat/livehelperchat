<th width="20%" ng-repeat="column in lhc.additionalColumns" ng-if="column.cenabl == true && !column.iconm">
    <i ng-if="column.icon !== ''" class="material-icons text-muted">{{column.icon}}</i>{{column.name}}
    <?php if (isset($additionalChatColumnOptions['enable_sort']) && $additionalChatColumnOptions['enable_sort'] == true) : ?>
    <a ng-if="column.sorten" ng-click="lhc.toggleWidgetSort('<?php echo $additionalChatColumnOptions['sort_field']?>',column.items[0] + '_dsc', column.items[0] + '_asc',true)">
        <i ng-class="{'text-muted' : (lhc.toggleWidgetData['<?php echo $additionalChatColumnOptions['sort_field']?>'] != column.items[0] + '_asc' && lhc.toggleWidgetData['<?php echo $additionalChatColumnOptions['sort_field']?>'] != column.items[0] + '_dsc')}" class="material-icons">{{lhc.toggleWidgetData['<?php echo $additionalChatColumnOptions['sort_field']?>'] == column.items[0] + '_dsc' || lhc.toggleWidgetData['<?php echo $additionalChatColumnOptions['sort_field']?>'] != column.items[0] + '_asc' ? 'trending_up' : 'trending_down'}}</i>
    </a>
    <?php unset($additionalChatColumnOptions);endif; ?>
</th>