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

        this.loadView = function(id, mode) {
            var deferred = $q.defer();

            var params = typeof mode !== 'undefined' ? mode : '';

            $http.get(WWW_DIR_JAVASCRIPT + 'views/loadview/' + id + params).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.deleteView = function(id) {
            var deferred = $q.defer();
            $http.post(WWW_DIR_JAVASCRIPT + 'views/deleteview/' + id).then(function(data) {
                deferred.resolve(data.data);
            },function(internalError){
                deferred.reject(typeof internalError.status !== 'undefined' ? '['+internalError.status+']' : '[0]');
            });
            return deferred.promise;
        };

        this.loadViewPage = function(href) {
            var deferred = $q.defer();
            $http.get(href + '/(mode)/list').then(function(data) {
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

        this.currentView = null;

        this.updateTimeout = null;

        var _that = this;


        // Bootstraps initial attributes
        this.initLHCData = function() {
            var appendURL = '';
            LiveHelperChatViewsFactory.loadInitialData(appendURL).then(function(data) {
                _that.views = data.views;
                _that.views.length > 0 && _that.loadView(_that.views[0]);
                _that.setUpdateLive();
            });

            $('#view-content').on("click", ".page-link", function(e) {
                e.stopPropagation();
                e.preventDefault();
                if ($(e.target).attr('href') != '#') {
                    var link = $(e.target).attr('href');

                    var matches = link.match(/\(page\)\/(\d+)/);

                    var page = 1;

                    if (matches != null) {
                        page = matches[1];
                    }

                    _that.currentView.page = page;
                    _that.updateViewList(link);
                }
            })
        }

        this.updateViewList = function(href) {
            LiveHelperChatViewsFactory.loadViewPage(href).then(function(data) {
                    document.getElementById('view-content-list').innerHTML = data.body;
                    _that.protectCSFR();
            })
        }

        this.setUpdateLive = function () {
            clearTimeout(this.updateTimeout);
            this.updateTimeout = setTimeout(function () {
                if (_that.currentView != null) {
                    _that.updateViewList(WWW_DIR_JAVASCRIPT + 'views/loadview/' + _that.currentView.id + '/(page)/' + _that.currentView.page);
                }
                _that.setUpdateLive();
            },20000);
        }

        this.fetchViews = function () {
            var appendURL = '';
            LiveHelperChatViewsFactory.loadInitialData(appendURL).then(function(data) {
                _that.views = data.views;
            });
        }

        this.deleteView = function (view) {
            if (confirm('Are you sure?')) {
                LiveHelperChatViewsFactory.deleteView(view.id).then(function() {
                    _that.views.splice(_that.views.indexOf(view),1);
                    if (_that.currentView !== null && _that.currentView.id == view.id) {
                        _that.currentView = null;
                    }
                    if (_that.views.length > 0) {
                        _that.loadView(_that.views[0]);
                    }
                })
            }
        }

        this.cleanupTabs = function() {
            var chatsToRemove = [];

            $.each(lhinst.chatsSynchronising, function( index, chat_id ) {
                chatsToRemove.push(chat_id);
            });

            $.each(chatsToRemove, function( index, chat_id ) {
                lhinst.removeSynchroChat(chat_id);
            });
        }

        this.loadView = function(view) {
            this.cleanupTabs();
            _that.currentView = view;
            _that.currentView.page = 1;
            LiveHelperChatViewsFactory.loadView(view.id).then(function(data) {
                document.getElementById('view-content').innerHTML = data.body;
                _that.protectCSFR();
            })
        }

        this.protectCSFR = function () {
            $('#view-content a.csfr-required').click(function() {
                var inst = $(this);
                if (!inst.attr('data-secured')) {
                    inst.attr('href',inst.attr('href')+'/(csfr)/'+confLH.csrf_token);
                    inst.attr('data-secured',1);
                }
            });
        }

        this.initLHCData();
    }]);

/*
} catch (e) {
    if (lhcError) lhcError.log(e.message, "angular.lhc-views.js", e.lineNumber || e.line, e.stack); else throw Error("lhc : " + e.message);
}*/
