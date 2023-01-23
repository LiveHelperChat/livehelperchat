try {

    services.factory('LiveHelperChatViewsFactory', ['$http','$q',function ($http, $q) {

        this.updateViewsList = function(appendURL) {
            var deferred = $q.defer();
            $http.get(WWW_DIR_JAVASCRIPT + 'views/updateviews').then(function(data) {
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

        this.updatePassiveMode = function(id) {
            var deferred = $q.defer();
            $http.post(WWW_DIR_JAVASCRIPT + 'views/updatepassivemode/' + id).then(function(data) {
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

    lhcAppControllers.controller('LiveHelperChatViewsCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','$window','LiveHelperChatViewsFactory', function($scope, $http, $location, $rootScope, $log, $interval, $window, LiveHelperChatViewsFactory) {

        $scope.current_user_id = confLH.user_id;

        // Parameters for back office sync
        this.views = [];
        this.invites = 0;
        this.default_view_id = 0;

        this.currentView = null;

        this.updateTimeout = null;
        this.updateViewsTimeout = null;

        var _that = this;

        ee.addListener('views.updateViews',function (status) {
            _that.fetchViews();
        });

        this.shareView = function(view) {
            lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'views/shareview/' + view.id});
        }

        // Bootstraps initial attributes
        this.initLHCData = function() {
            var appendURL = '';

            LiveHelperChatViewsFactory.loadInitialData(appendURL).then(function(data) {
                _that.views = data.views;
                _that.invites = data.invites;

                if (_that.views.length > 0) {
                    var viewDefault = _that.views[0];
                    if ($window['vctrl_default_view_id']) {
                        angular.forEach(_that.views, function(view) {
                            if (view.id == $window['vctrl_default_view_id']) {
                                viewDefault = view;
                            }
                        });
                    }
                    _that.loadView(viewDefault);
                }

                _that.setUpdateLive();
                _that.setUpdateLiveViews();
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
                    if (_that.currentView != null && _that.currentView.id == data.view_id) {
                        _that.currentView.total_records = data.total_records;
                    }
                    _that.protectCSFR();
            })
        }

        this.changePassiveMode = function(view) {
            view.passive = !view.passive;;
            LiveHelperChatViewsFactory.updatePassiveMode(view.id).then(function(data) {

            })
        }

        this.setUpdateLiveViews = function () {
            clearTimeout(this.updateViewsTimeout);
            this.updateViewsTimeout = setTimeout(function () {
                LiveHelperChatViewsFactory.updateViewsList().then(function(data){
                    _that.views = data.views;
                    _that.invites = data.invites;
                });
                _that.setUpdateLiveViews();
            },5000);
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
                _that.invites = data.invites;
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

        // Required on different view load as we are not refreshing pages
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
                if (_that.currentView !== null && _that.currentView.id == data.view_id) {
                    document.getElementById('view-content').innerHTML = data.body;
                    _that.currentView.total_records = data.total_records;
                    _that.protectCSFR();
                }
            })
        }
        
        this.exportView = function(view) {
            lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':WWW_DIR_JAVASCRIPT + '/views/exportview/' + view.id})
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

} catch (e) {
    if (lhcError) lhcError.log(e.message, "angular.lhc-views.js", e.lineNumber || e.line, e.stack); else throw Error("lhc : " + e.message);
}
