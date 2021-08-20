lhcAppControllers.controller('CannedReplaceCtrl',['$scope','$http','$location','$rootScope', '$log','$window', function($scope, $http, $location, $rootScope, $log, $window) {

    this.combinations = [];
    var that = this;

    this.makeid = function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    this.setConditions = function() {
        this.combinations = $window['replaceConditions'];
    }

    this.deleteElement = function (element,list) {
        if (confirm('Are you sure?')){
            list.splice(list.indexOf(element), 1);
        }
    }

    this.move = function(element, list, offset) {
        index = list.indexOf(element);
        newIndex = index + offset;
        if (newIndex > -1 && newIndex < list.length){
            removedElement = list.splice(index, 1)[0];
            list.splice(newIndex, 0, removedElement)
        }
    };

    this.addCombination = function() {
        that.combinations.push({
            'conditions' : [],
            'value' : '',
            'dep_id': "0",
            'priority' : 0,
        });
    };

    this.addCondition = function(items) {
        items.conditions.push({field:"", logic: "and"});
    }

    this.moveUp = function(field,list) {
        that.move(field,list,-1);
    }

    this.moveDown = function(field,list) {
        that.move(field,list,1);
    }

}]);