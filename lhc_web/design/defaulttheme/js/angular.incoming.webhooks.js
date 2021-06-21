lhcAppControllers.controller('WebhooksIncomingCtrl',['$scope','$http','$location','$rootScope', '$log', '$window', function($scope, $http, $location, $rootScope, $log, $window) {

    this.conditions = {};
    this.conditions_json = "";

    var that = this;

    this.setConditions = function() {
        that.conditions = $window['incomingConditions'];
    }

    this.makeid = function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    this.updateContinuous = function() {
        this.conditions_json = JSON.stringify(this.conditions)
    }

    this.addParam = function (params) {
        params.push({
            'key' : '',
            'value' : '',
            'id' : 'temp'+new Date().getTime()
        });
    }

    this.deleteParam = function (params,param) {
        params.splice(params.indexOf(param),1);
    }

}]);