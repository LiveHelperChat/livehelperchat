lhcAppControllers.controller('WebhooksIncomingCtrl',['$scope','$http','$location','$rootScope', '$log', function($scope, $http, $location, $rootScope, $log) {

    this.conditions = {};
    this.conditions_json = "";

    var that = this;

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

}]);