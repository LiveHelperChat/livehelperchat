lhcAppControllers.controller('BotRestAPIParameters',['$scope','$http','$location','$rootScope', '$log','$window', function($scope, $http, $location, $rootScope, $log, $window) {
    this.parameters = [];
    this.host = "";
    this.ecache = "";
    this.log_audit = "";
    this.log_system = "";
    this.log_code = "";

    var that = this;

    this.addParameter = function() {
        that.parameters.push({
            'method' : 'GET',
            'authorization' : '',
            'api_key_location' : 'header',
            'query' : [],
            'header' : [],
            'conditions' : [],
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
        return JSON.stringify({'host' : that.host, 'log_code' : that.log_code, 'log_audit': that.log_audit, 'log_system' : this.log_system, 'ecache': that.ecache, 'parameters' : that.parameters});
    }

    this.initParams = function () {
        this.parameters = $window['rest_api_parameters'];
        this.parameters.forEach(function(item){
           if (typeof item.conditions === 'undefined') {
               item.conditions = [];
           }
        });
        this.host = $window['botRestAPIHost'];
        this.ecache = $window['botRestAPIECache'];
        this.log_audit = $window['botRestAPIAuditLog'];
        this.log_system = $window['botRestAPISystemLog'];
        this.log_code = $window['botRestAPICode'];
    }

}]);