lhcAppControllers.controller('LHCPriorityCtrl',['$scope', function($scope) {

    this.value = [];

    var that = this;

    this.addFilter = function() {
        var item = {
            "field": "",
            "comparator": "=",
            "value": ""
        };
        that.value.push(item);
    }

    this.removeFilter = function(item) {
        var index = that.value.indexOf(item);
        that.value.splice(index, 1);
    }

    this.move = function(list, element, offset) {
        index = list.indexOf(element);
        newIndex = index + offset;
        if (newIndex > -1 && newIndex < list.length){
            removedElement = list.splice(index, 1)[0];
            list.splice(newIndex, 0, removedElement)
        }
    }

}]);