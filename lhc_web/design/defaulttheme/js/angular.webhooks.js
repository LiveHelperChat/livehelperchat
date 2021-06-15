lhcAppControllers.controller('WebhooksCtrl',['$scope','$http','$location','$rootScope', '$log','$window', function($scope, $http, $location, $rootScope, $log, $window) {

    this.conditions = [];
    this.itemAdd = "1";
    this.conditions_json = "";
    var that = this;

    this.setConditions = function () {
        this.conditions = $window['conditionsWebhook'];
    }

    this.makeid = function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    this.addItem = function(type) {
        this.conditions.push({
            '_id': this.makeid(10),
            'type': type,
            'logic': 'and'
        });
    }

    this.deleteCondition = function (condition) {
        if (confirm('Are you sure?')){
            this.conditions.splice(this.conditions.indexOf(condition), 1);
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

    this.moveUp = function(field,list) {
        that.move(field,list,-1);
    }

    this.moveDown = function(field,list) {
        that.move(field,list,1);
    }

    this.updateContinuous = function() {
        this.conditions_json = JSON.stringify(this.conditions)
    }

}]);