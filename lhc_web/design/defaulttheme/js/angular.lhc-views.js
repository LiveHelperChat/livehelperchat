/*try {*/

    services.factory('LiveHelperChatViewsFactory', ['$http','$q',function ($http, $q) {

        this.loadChatList = function(filter){
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'chat/syncadmininterface' + filter).then(function(data) {
                if (typeof data.error_url !== 'undefined') {
                    document.location = data.error_url;
                } else {
                    deferred.resolve(data.data);
                }
            },function(internalError) {
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };



        this.loadActiveChats = function() {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'chat/loadactivechats').then(function(data) {
                if (typeof data.error_url !== 'undefined') {
                    document.location = data.error_url;
                } else {
                    deferred.resolve(data.data);
                }
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.getNotificationsData = function(id) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'chat/getnotificationsdata/(id)/' + id).then(function(data) {
                if (typeof data.error_url !== 'undefined') {
                    document.location = data.data.error_url;
                } else {
                    deferred.resolve(data.data);
                }
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };



        this.setInactive = function(status) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'user/setinactive/'+status).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError) {
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.setOnlineMode = function(status) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'user/setoffline/'+status).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError) {
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.setAlwaysOnlineMode = function(status) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'user/setalwaysonline/'+status).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError) {
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.changeVisibility = function(status)
        {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'user/setinvisible/'+status).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError) {
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.getActiveOperatorChat = function(user_id) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'groupchat/startchatwithoperator/'+user_id).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.rejectGroupChat = function(chatId) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'groupchat/leave/'+chatId).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.newGroupChat = function(name,publicChat) {
            var deferred = $q.defer();
            $http.post(WWW_DIR_JAVASCRIPT + 'groupchat/newgroupajax/',{"name":name,"public":publicChat}).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.searchProvider = function(scope,keyword) {
            var deferred = $q.defer();
            $http.post(WWW_DIR_JAVASCRIPT + 'chat/searchprovider/'+scope+"/?exclude_disabled=1&q="+keyword).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };













        this.loadInitialData = function(appendURL) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'views/loadinitialdata' + appendURL).then(function(data) {
                if (typeof data.error_url !== 'undefined') {
                    document.location = data.data.error_url;
                } else {
                    deferred.resolve(data.data);
                }
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.loadView = function(id) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'views/loadview/' + id).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };


        this.truncate = function (text, length, end) {
            if (isNaN(length))
                length = 10;

            if (end === undefined)
                end = "...";

            if (text.length <= length || text.length - end.length <= length) {
                return text;
            }
            else {
                return String(text).substring(0, length-end.length) + end;
            }
        };

        return this;
    }]);

    lhcAppControllers.controller('LiveHelperChatViewsCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','LiveHelperChatViewsFactory', function($scope, $http, $location, $rootScope, $log, $interval,LiveHelperChatViewsFactory) {

        $scope.current_user_id = confLH.user_id;

        // Parameters for back office sync

        this.views = [];

        var _that = this;

        // Bootstraps initial attributes
        this.initLHCData = function() {
            var appendURL = '';
            LiveHelperChatViewsFactory.loadInitialData(appendURL).then(function(data) {
                _that.views=data.views;
            });
        }

        this.loadView = function(view) {
            view.active = true;

            console.log(view);

            LiveHelperChatViewsFactory.loadView(view.id).then(function(data) {
                document.getElementById('view-content').innerHTML = data.body;
            })
        }

        this.initLHCData();

    }]);

/*
} catch (e) {
    if (lhcError) lhcError.log(e.message, "angular.lhc-views.js", e.lineNumber || e.line, e.stack); else throw Error("lhc : " + e.message);
}*/
