lhcAppControllers.controller('BotRestAPIParameters',['$scope','$http','$location','$rootScope', '$log', function($scope, $http, $location, $rootScope, $log) {
    this.parameters = [];

    var that = this;

    this.addParameter = function() {
        that.parameters.push({
            'method' : 'GET',
            'authorization' : '',
            'api_key_location' : 'header',
            'query' : [],
            'header' : [],
            'postparams' : [],
            'userparams' : [],
            'output' : [],
            'id' : 'temp'+new Date().getTime()
        });
    };

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

    this.deleteParameter = function(field) {
        that.parameters.splice(that.parameters.indexOf(field),1);
    };

    this.getJSON = function () {
        return JSON.stringify({'host' : that.host, 'parameters' : that.parameters});
    }

}]);