$(document).ready(function () {
    var hash = window.location.hash;
    if (hash != '') {
        $('ul[role="tablist"] a[href="' + hash.replace("#!", "") + '"]').tab('show');
    }
});

var phonecatApp = angular.module('lhcApp', [
    'lhcAppServices',
    'lhcAppControllers'
]);

var services = angular.module('lhcAppServices', []);
var lhcAppControllers = angular.module('lhcAppControllers', ["checklist-model"]);

angular.element(document).ready(function () {
    var element = angular.element(document.querySelector("form"));
    element.triggerHandler("$destroy");
});

services.factory('LiveHelperChatFactory', ['$http', '$q', function ($http, $q) {

    this.loadChatList = function (filter) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/syncadmininterface' + filter).then(function (data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.loadInitialData = function (appendURL) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/loadinitialdata' + appendURL).then(function (data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.loadActiveChats = function () {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/loadactivechats').then(function (data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.getNotificationsData = function (id) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/getnotificationsdata/(id)/' + id).then(function (data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.setInactive = function (status) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'user/setinactive/' + status).then(function (data) {
            deferred.resolve(data.data);
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.setOnlineMode = function (status) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'user/setoffline/' + status).then(function (data) {
            deferred.resolve(data.data);
        }, function (data) {
            deferred.reject(data.data);
        });
        return deferred.promise;
    };

    this.changeVisibility = function (status) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'user/setinvisible/' + status).then(function (data) {
            deferred.resolve(data.data);
        }, function (data) {
            deferred.reject(data.data);
        });
        return deferred.promise;
    };

    this.getActiveOperatorChat = function (user_id) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/startchatwithoperator/' + user_id + '/(mode)/check').then(function (data) {
            deferred.resolve(data.data);
        }, function () {
            deferred.reject('error');
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
        } else {
            return String(text).substring(0, length - end.length) + end;
        }
    };

    this.loadChat = function (chat_id) {
        var deferred = $q.defer();

        $http.get(WWW_DIR_JAVASCRIPT + 'chat/adminchat/' + chat_id).then(function (data) {
            deferred.resolve(data.data);
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    }

    this.acceptTransfer = function (id, chatMode) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/accepttransfer/' + id + (chatMode === true ? '/(mode)/chat' : '')).then(function (data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.setSettingAjax = function (attr, val) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'user/setsettingajax/' + attr + '/' + val).then(function (data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        }, function () {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    return this;
}]);


lhcAppControllers.controller('LiveHelperChatCtrl', ['$compile','$scope', '$http', '$location', '$rootScope', '$log', '$interval', 'LiveHelperChatFactory', function ($compile, $scope, $http, $location, $rootScope, $log, $interval, LiveHelperChatFactory) {

    $scope.predicate = 'last_visit';
    $scope.pending_chats = {};
    $scope.pending_chats_expanded = true;
    $scope.active_chats = {};
    $scope.active_chats_expanded = true;
    $scope.my_active_chats_expanded = true;
    $scope.closed_chats = {};
    $scope.closed_chats_expanded = true;
    $scope.unread_chats = {};
    $scope.unread_chats_expanded = true;
    $scope.transfer_dep_chats = {};
    $scope.transfer_chats = {};
    $scope.timeoutControl = null;
    $scope.setTimeoutEnabled = true;
    $scope.lmtoggle = false;
    $scope.lmtoggler = false;
    $scope.cmtoggle = false;

    // Just for extension reserved keywords
    $scope.custom_list_1_expanded = true;
    $scope.custom_list_2_expanded = true;
    $scope.custom_list_3_expanded = true;
    $scope.custom_list_4_expanded = true;

    // Parameters for back office sync

    var _that = this;

    this.restoreLocalSetting = function (variable, defaultValue, split) {
        try {
            if (localStorage) {
                var value = localStorage.getItem(variable);
                if (value !== null) {
                    if (split == true) {

                        var values = value.split('/');
                        var valuesInt = new Array();

                        angular.forEach(values, function (val) {
                            valuesInt.push(parseInt(val));
                        });

                        return valuesInt;
                    } else {
                        return value;
                    }
                } else {
                    return defaultValue;
                }
            }
        } catch (e) {
        }
        return defaultValue;
    };

    this.custom_extension_filter = '';

    // Active chat limit
    this.limitb = this.restoreLocalSetting('limitb', '10', false);
    this.limita = this.restoreLocalSetting('limita', '10', false);
    this.limitu = this.restoreLocalSetting('limitu', '10', false);
    this.limitp = this.restoreLocalSetting('limitp', '10', false);
    this.limito = this.restoreLocalSetting('limito', confLH.dlist.op_n, false);
    this.limitc = this.restoreLocalSetting('limitc', '10', false);
    this.limitd = this.restoreLocalSetting('limitd', '10', false);
    this.limitmc = this.restoreLocalSetting('limitmc', '50', false);

    // Active chat's operators filter
    this.activeu = this.restoreLocalSetting('activeu', [], true);
    this.pendingu = this.restoreLocalSetting('pendingu', [], true);

    // Main left menu of pagelayout
    $scope.lmtoggle = this.restoreLocalSetting('lmtoggle', 'false', false) != 'false';
    $scope.lmtoggler = this.restoreLocalSetting('lmtoggler', 'false', false) != 'false';
    $scope.cmtoggle = this.restoreLocalSetting('cmtoggle', 'false', false) != 'false';
    $scope.chatmlist = this.restoreLocalSetting('chatmlist', 'false', false) != 'false';

    this.lhcVersion = 0;
    this.lhcVersionCounter = 8;
    this.lhcPendingRefresh = false;

    // Stores last ID of unread/pending chat id
    this.lastidEvent = 0;

    // User departments
    this.userDepartments = [];
    this.userProductNames = [];
    this.userDepartmentsGroups = [];
    this.userGroups = [];
    this.userList = [];
    this.additionalColumns = [];

    this.departmentd = this.restoreLocalSetting('departmentd', [], true);
    this.departmentd_dpgroups = this.restoreLocalSetting('departmentd_dpgroups', [], true);
    this.departmentdNames = [];

    this.operatord = this.restoreLocalSetting('operatord', [], true);
    this.operatord_dpgroups = this.restoreLocalSetting('operatord_dpgroups', [], true);
    this.operatordNames = [];

    // Chats with products filters
    this.actived = this.restoreLocalSetting('actived', [], true);
    this.actived_products = this.restoreLocalSetting('actived_products', [], true);
    this.actived_dpgroups = this.restoreLocalSetting('actived_dpgroups', [], true);
    this.actived_ugroups = this.restoreLocalSetting('actived_ugroups', [], true);
    this.activedNames = [];


    this.mcd = this.restoreLocalSetting('mcd', [], true);
    this.mcd_products = this.restoreLocalSetting('mcd_products', [], true);
    this.mcd_dpgroups = this.restoreLocalSetting('mcd_dpgroups', [], true);
    this.mcdNames = [];

    this.unreadd = this.restoreLocalSetting('unreadd', [], true);
    this.unreadd_products = this.restoreLocalSetting('unreadd_products', [], true);
    this.unreadd_dpgroups = this.restoreLocalSetting('unreadd_dpgroups', [], true);
    this.unreaddNames = [];

    this.pendingd = this.restoreLocalSetting('pendingd', [], true);
    this.pendingd_products = this.restoreLocalSetting('pendingd_products', [], true);
    this.pendingd_dpgroups = this.restoreLocalSetting('pendingd_dpgroups', [], true);
    this.pendingd_ugroups = this.restoreLocalSetting('pendingd_ugroups', [], true);
    this.pendingdNames = [];

    this.botd = this.restoreLocalSetting('botd', [], true);
    this.botd_products = this.restoreLocalSetting('botd_products', [], true);
    this.botd_dpgroups = this.restoreLocalSetting('botd_dpgroups', [], true);
    this.botd_ugroups = this.restoreLocalSetting('botd_ugroups', [], true);
    this.botdNames = [];

    this.closedd = this.restoreLocalSetting('closedd', [], true);
    this.closedd_products = this.restoreLocalSetting('closedd_products', [], true);
    this.closedd_dpgroups = this.restoreLocalSetting('closedd_dpgroups', [], true);
    this.closeddNames = [];

    // Storage for notifications
    this.statusNotifications = [];
    this.isListLoaded = false;

    this.widgetsItems = new Array();
    this.widgetsItems.push('actived');
    this.widgetsItems.push('departmentd');
    this.widgetsItems.push('unreadd');
    this.widgetsItems.push('pendingd');
    this.widgetsItems.push('operatord');
    this.widgetsItems.push('closedd');
    this.widgetsItems.push('mcd');

    this.timeoutActivity = null;
    this.timeoutActivityTime = 300;
    this.blockSync = false;

    // Sync icons statuses
    this.hideOnline = false;
    this.hideInvisible = false;

    this.changeVisibility = function () {
        LiveHelperChatFactory.changeVisibility(!_that.hideInvisible == true ? 'true' : 'false').then(function (data) {
            if (data.error === false) {
                _that.hideInvisible = !_that.hideInvisible;
            } else if (typeof data.msg !== 'undefined') {
                alert(data.msg);
            } else {
                alert(data);
            }
        }, function (error) {
            alert('We could not change your status!');
        });
    };

    this.changeOnline = function () {
        LiveHelperChatFactory.setOnlineMode(!_that.hideOnline == true ? 'true' : 'false').then(function (data) {
            if (data.error === false) {
                _that.hideOnline = !_that.hideOnline;
            } else if (typeof data.msg !== 'undefined') {
                alert(data.msg);
            } else {
                alert(data);
            }
        }, function (error) {
            alert('We could not change your status!');
        });
    };

    this.setSettingAjax = function (attr, val, attrObj) {
        LiveHelperChatFactory.setSettingAjax(attr,val).then(function (data) {
            _that[attrObj] = val;
        }, function (error) {
            alert('We could not change your sound settings!');
        });
    };

    angular.forEach(this.widgetsItems, function (listId) {
        _that[listId + '_all_departments'] = _that.restoreLocalSetting(listId + '_all_departments', 'false', false) != 'false';
        _that[listId + '_hide_hidden'] = _that.restoreLocalSetting(listId + '_hide_hidden', 'false', false) != 'false';
        _that[listId + '_hide_disabled'] = _that.restoreLocalSetting(listId + '_hide_disabled', 'false', false) != 'false';
        _that[listId + '_only_online'] = _that.restoreLocalSetting(listId + '_only_online', 'false', false) != 'false';
        _that[listId + '_only_explicit_online'] = _that.restoreLocalSetting(listId + '_only_explicit_online', 'false', false) != 'false';
    });

    this.storeLocalSetting = function (variable, value) {
        if (localStorage) {
            try {
                var value = localStorage.setItem(variable, value);
            } catch (e) {
            }
        }
    };

    this.removeLocalSetting = function (listId) {
        if (localStorage) {
            try {
                localStorage.removeItem(listId);
            } catch (err) {
            }
            ;
        }
    };

    this.toggleList = function (variable) {
        $scope[variable] = !$scope[variable];
        if (localStorage) {
            try {
                localStorage.setItem(variable, $scope[variable]);
            } catch (err) {
            }
        }
    };

    this.toggleWidgetData = [];

    this.toggleWidget = function (variable, forceReload) {
        _that.toggleWidgetData[variable] = typeof _that.toggleWidgetData[variable] !== 'undefined' ? !_that.toggleWidgetData[variable] : true;

        if (localStorage) {
            try {
                localStorage.setItem(variable, _that.toggleWidgetData[variable]);
            } catch (err) {
            }
        }

        if (typeof forceReload !== 'undefined' && forceReload == true) {
            $scope.loadChatList();
        }
    };

    this.toggleWidgetSort = function (variable, val, val_desc, forceReload) {
        _that.toggleWidgetData[variable] = typeof _that.toggleWidgetData[variable] === 'undefined' ? val : (_that.toggleWidgetData[variable] == val ? val_desc : val);

        if (localStorage) {
            try {
                localStorage.setItem(variable, _that.toggleWidgetData[variable]);
            } catch (err) {
            }
            ;
        }
        ;

        if (typeof forceReload !== 'undefined' && forceReload == true) {
            $scope.loadChatList();
        }
    };

    this.getToggleWidget = function (variable, defaultValue) {
        this.toggleWidgetData[variable] = this.restoreLocalSetting(variable, (typeof defaultValue === 'undefined' ? 'false' : defaultValue), false) == 'false' ? false : true;
    };

    this.getToggleWidgetSort = function (variable) {
        this.toggleWidgetData[variable] = this.restoreLocalSetting(variable, '', false);
    };

    $scope.getSyncFilter = function () {
        _that.custom_extension_filter = '';

        var filter = '/(limita)/' + parseInt(_that.limita);
        filter += '/(limitu)/' + parseInt(_that.limitu);
        filter += '/(limitp)/' + parseInt(_that.limitp);
        filter += '/(limito)/' + parseInt(_that.limito);
        filter += '/(limitc)/' + parseInt(_that.limitc);
        filter += '/(limitd)/' + parseInt(_that.limitd);
        filter += '/(limitmc)/' + parseInt(_that.limitmc);
        filter += '/(limitb)/' + parseInt(_that.limitb);

        if (typeof _that.activeu == 'object' && _that.activeu.length > 0) {
            filter += '/(activeu)/' + _that.activeu.join('/');
        }

        if (typeof _that.pendingu == 'object' && _that.pendingu.length > 0) {
            filter += '/(pendingu)/' + _that.pendingu.join('/');
        }

        if (typeof _that.actived_dpgroups == 'object' && _that.actived_dpgroups.length > 0) {
            filter += '/(adgroups)/' + _that.actived_dpgroups.join('/');
        }

        if (typeof _that.pendingd_dpgroups == 'object' && _that.pendingd_dpgroups.length > 0) {
            filter += '/(pdgroups)/' + _that.pendingd_dpgroups.join('/');
        }

        if (typeof _that.closedd_dpgroups == 'object' && _that.closedd_dpgroups.length > 0) {
            filter += '/(cdgroups)/' + _that.closedd_dpgroups.join('/');
        }

        if (typeof _that.mcd_dpgroups == 'object' && _that.mcd_dpgroups.length > 0) {
            filter += '/(mdgroups)/' + _that.mcd_dpgroups.join('/');
        }

        if (typeof _that.unreadd_dpgroups == 'object' && _that.unreadd_dpgroups.length > 0) {
            filter += '/(udgroups)/' + _that.unreadd_dpgroups.join('/');
        }

        if (typeof _that.departmentd_dpgroups == 'object' && _that.departmentd_dpgroups.length > 0) {
            filter += '/(ddgroups)/' + _that.departmentd_dpgroups.join('/');
        }

        if (typeof _that.operatord_dpgroups == 'object' && _that.operatord_dpgroups.length > 0) {
            filter += '/(odpgroups)/' + _that.operatord_dpgroups.join('/');
        }

        if (typeof _that.actived == 'object') {
            if (_that.actived.length > 0) {
                filter += '/(actived)/' + _that.actived.join('/');
            } else {
                var itemsFilter = _that.manualFilterByFilter('actived');
                if (itemsFilter.length > 0) {
                    filter += '/(actived)/' + itemsFilter.join('/');
                }
            }
        }

        if (typeof _that.toggleWidgetData['only_user'] !== 'undefined' && _that.toggleWidgetData['only_user'] == true) {
            filter += '/(ouser)/true';
        }

        if (typeof _that.mcd == 'object') {
            if (_that.mcd.length > 0) {
                filter += '/(mcd)/' + _that.mcd.join('/');
            } else {
                var itemsFilter = _that.manualFilterByFilter('mcd');
                if (itemsFilter.length > 0) {
                    filter += '/(mcd)/' + itemsFilter.join('/');
                }
            }
        }

        if (typeof _that.unreadd == 'object') {
            if (_that.unreadd.length > 0) {
                filter += '/(unreadd)/' + _that.unreadd.join('/');
            } else {
                var itemsFilter = _that.manualFilterByFilter('unreadd');
                if (itemsFilter.length > 0) {
                    filter += '/(unreadd)/' + itemsFilter.join('/');
                }
            }
        }

        if (typeof _that.pendingd == 'object') {
            if (_that.pendingd.length > 0) {
                filter += '/(pendingd)/' + _that.pendingd.join('/');
            } else {
                var itemsFilter = _that.manualFilterByFilter('pendingd');
                if (itemsFilter.length > 0) {
                    filter += '/(pendingd)/' + itemsFilter.join('/');
                }
            }

            if (typeof _that.toggleWidgetData['pending_chats_sort'] !== 'undefined' && _that.toggleWidgetData['pending_chats_sort'] == true) {
                filter += '/(psort)/asc';
            }
        }

        if (typeof _that.operatord == 'object') {
            if (_that.operatord.length > 0) {
                filter += '/(operatord)/' + _that.operatord.join('/');
            } else {
                var itemsFilter = _that.manualFilterByFilter('operatord');
                if (itemsFilter.length > 0) {
                    filter += '/(operatord)/' + itemsFilter.join('/');
                }
            }
        }

        if (typeof _that.closedd == 'object' && _that.closedd.length > 0) {
            filter += '/(closedd)/' + _that.closedd.join('/');
        }

        if (typeof _that.departmentd == 'object') {
            if (_that.departmentd.length > 0) {
                filter += '/(departmentd)/' + _that.departmentd.join('/');
            } else {
                var itemsFilter = _that.manualFilterByFilter('departmentd');
                if (itemsFilter.length > 0) {
                    filter += '/(departmentd)/' + itemsFilter.join('/');
                }
            }
        }

        if (typeof _that.actived_products == 'object' && _that.actived_products.length > 0) {
            filter += '/(activedprod)/' + _that.actived_products.join('/');
        }

        if (typeof _that.pendingd_ugroups == 'object' && _that.pendingd_ugroups.length > 0) {
            filter += '/(pugroups)/' + _that.pendingd_ugroups.join('/');
        }

        if (typeof _that.actived_ugroups == 'object' && _that.actived_ugroups.length > 0) {
            filter += '/(augroups)/' + _that.actived_ugroups.join('/');
        }

        if (typeof _that.mcd_products == 'object' && _that.mcd_products.length > 0) {
            filter += '/(mcdprod)/' + _that.mcd_products.join('/');
        }

        if (typeof _that.unreadd_products == 'object' && _that.unreadd_products.length > 0) {
            filter += '/(unreaddprod)/' + _that.unreadd_products.join('/');
        }

        if (typeof _that.pendingd_products == 'object' && _that.pendingd_products.length > 0) {
            filter += '/(pendingdprod)/' + _that.pendingd_products.join('/');
        }

        if (typeof _that.closedd_products == 'object' && _that.closedd_products.length > 0) {
            filter += '/(closeddprod)/' + _that.closedd_products.join('/');
        }

        if (typeof _that.toggleWidgetData['active_chats_sort'] !== 'undefined' && _that.toggleWidgetData['active_chats_sort'] !== '') {
            filter += '/(acs)/' + _that.toggleWidgetData['active_chats_sort'];
        }

        if (typeof _that.toggleWidgetData['closed_chats_sort'] !== 'undefined' && _that.toggleWidgetData['closed_chats_sort'] !== '') {
            filter += '/(clcs)/' + _that.toggleWidgetData['closed_chats_sort'];
        }

        if (typeof _that.toggleWidgetData['onop_sort'] !== 'undefined' && _that.toggleWidgetData['onop_sort'] !== '') {
            filter += '/(onop)/' + _that.toggleWidgetData['onop_sort'];
        }

        ee.emitEvent('eventGetSyncFilter', [_that, $scope]);

        filter += _that.custom_extension_filter;

        var openedChats = _that.getOpenedChatIds();

        if (openedChats.length > 0) {
            filter += '/(exchat)/' + openedChats.join('/');
        }

        return filter;
    }

    $scope.$watch('lhc.limita', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limita', newVal);
            $scope.loadChatList();
        }
    });

    this.manualFilterByFilter = function (listId) {
        if (_that[listId + '_only_explicit_online'] == true || _that[listId + '_hide_hidden'] == true || _that[listId + '_hide_disabled'] == true || _that[listId + '_only_online'] == true) {

            if (_that.userDepartments.length > 0) {
                var listDepartments = [];
                angular.forEach(_that.userDepartments, function (department) {
                    if (
                        (_that[listId + '_only_explicit_online'] == false || (_that[listId + '_only_explicit_online'] == true && department.oexp == true)) &&
                        (_that[listId + '_hide_hidden'] == false || (_that[listId + '_hide_hidden'] == true && department.hidden == false)) &&
                        (_that[listId + '_hide_disabled'] == false || (_that[listId + '_hide_disabled'] == true && department.disabled == false)) &&
                        (_that[listId + '_only_online'] == false || (_that[listId + '_only_online'] == true && department.ogen == true))
                    ) {
                        listDepartments.push(department.id);
                    }
                });

                if (listDepartments.length == 0) {
                    listDepartments.push(-1);
                }

                return listDepartments;
            }
        }

        return [];
    };

    this.setDepartmentNames = function (listId) {
        _that[listId + 'Names'] = [];
        angular.forEach(_that[listId], function (value) {
            if (typeof _that.userDepartmentsNames !== 'undefined' && typeof _that.userDepartmentsNames[value] !== 'undefined') {
                _that[listId + 'Names'].push(_that.userDepartmentsNames[value]);
            } else if (typeof _that.userDepartmentsNames !== 'undefined') {
                _that[listId].splice(_that[listId].indexOf(value), 1);
                _that.departmentChanged(listId);
            }
        });
    };

    this.departmentChanged = function (listId) {
        if (_that[listId].length > 0) {

            _that[listId + '_all_departments'] = false;
            _that.allDepartmentsChanged(listId, false);

            var listValue = _that[listId].join("/");

            if (listValue != '') {
                _that.storeLocalSetting(listId, listValue);
                _that.setDepartmentNames(listId);
            }

        } else {
            if (localStorage) {
                try {
                    localStorage.removeItem(listId);
                } catch (err) {
                }
            }
        }

        _that.isListLoaded = false;
        $scope.loadChatList();
    };

    this.productChanged = function (listId) {

        if (_that[listId].length > 0) {

            var listValue = _that[listId].join("/");

            if (listValue != '') {
                _that.storeLocalSetting(listId, listValue);
            }

        } else {
            if (localStorage) {
                try {
                    localStorage.removeItem(listId);
                } catch (err) {
                }
            }
        }
        _that.isListLoaded = false;
        $scope.loadChatList();
    };

    this.allDepartmentsChanged = function (listId, loadlList) {

        if (_that[listId + '_all_departments'] == true) {
            _that.storeLocalSetting(listId + '_all_departments', true);
        } else {
            _that.removeLocalSetting(listId + '_all_departments');
        }

        if (_that[listId + '_hide_hidden'] == true) {
            _that.storeLocalSetting(listId + '_hide_hidden', true);
        } else {
            _that.removeLocalSetting(listId + '_hide_hidden');
        }

        if (_that[listId + '_hide_disabled'] == true) {
            _that.storeLocalSetting(listId + '_hide_disabled', true);
        } else {
            _that.removeLocalSetting(listId + '_hide_disabled');
        }

        if (_that[listId + '_only_online'] == true) {
            _that.storeLocalSetting(listId + '_only_online', true);
        } else {
            _that.removeLocalSetting(listId + '_only_online');
        }

        if (_that[listId + '_all_departments'] == true) {
            _that[listId] = [];
            angular.forEach(_that.userDepartments, function (department) {
                if (
                    (_that[listId + '_only_explicit_online'] == false || (_that[listId + '_only_explicit_online'] == true && department.oexp == true)) &&
                    (_that[listId + '_hide_hidden'] == false || (_that[listId + '_hide_hidden'] == true && department.hidden == false)) &&
                    (_that[listId + '_hide_disabled'] == false || (_that[listId + '_hide_disabled'] == true && department.disabled == false)) &&
                    (_that[listId + '_only_online'] == false || (_that[listId + '_only_online'] == true && department.ogen == true))
                ) {
                    _that[listId].push(department.id);
                }
            });

            if (_that[listId].length == 0) {
                _that[listId].push(-1);
            }

        } else {
            if (loadlList == true) {
                _that[listId] = [];
            }
        }

        if (loadlList == true) {
            _that.isListLoaded = false;
            $scope.loadChatList();
        }
    };

    $scope.$watch('lhc.limitu', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limitu', newVal);
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.limitc', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limitc', newVal);
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.limitp', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limitp', newVal);
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.limito', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limito', newVal);
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.limitmc', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limitmc', newVal);
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.limitd', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('limitd', newVal);
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.activeu', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('activeu', newVal);
            _that.isListLoaded = false;
            $scope.loadChatList();
        }
        ;
    });

    $scope.$watch('lhc.pendingu', function (newVal, oldVal) {
        if (newVal != oldVal) {
            _that.storeLocalSetting('pendingu', newVal);
            _that.isListLoaded = false;
            $scope.loadChatList();
        }
        ;
    });

    this.startChatBackground = function (chat_id) {
        if (this.backgroundChats.indexOf(chat_id) === -1) {
            this.backgroundChats.push(chat_id);
            this.startChatDashboard(chat_id,{'background':true,'unread':true});
            ee.emitEvent('chatStartBackground', [chat_id]);
            return true;
        }
        return false;
    };

    $scope.startChatScope = function(chat_id,params){
        _that.startChatDashboard(chat_id,params);
    }

    $scope.hideMessagesNotifications = function(){
        angular.forEach(_that.notificationsArrayMessages, function( index, chat_id ) {
            if (typeof _that.notificationsArrayMessages[chat_id] !== 'undefined') {
                if (window.webkitNotifications) {
                    _that.notificationsArrayMessages[chat_id].cancel();
                } else {
                    _that.notificationsArrayMessages[chat_id].close();
                }
                delete _that.notificationsArrayMessages[chat_id];
            }
        });
    }

    // Notifications attributes
    this.notificationsArrayMessages = [];
    this.notificationsArray = [];
    this.soundIsPlaying = false;
    this.soundPlayedTimes = 0;
    this.audio = new Audio();
    this.audio.autoplay = 'autoplay';


    this.playNewChatAudio = function () {
        clearTimeout(this.soundIsPlaying);
        this.soundPlayedTimes++;
        if (Modernizr.audio) {

            this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.ogg' :
                Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_chat.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_chat.wav';
            this.audio.load();

            if (confLH.repeat_sound > this.soundPlayedTimes) {
                var inst = this;
                this.soundIsPlaying = setTimeout(function () {
                    inst.playNewChatAudio();
                }, confLH.repeat_sound_delay * 1000);
            }
        }
        ;
    };

    this.isBlinking = false;

    this.startBlinking = function () {
        if (this.isBlinking == false) {
            var inst = this;
            var newExcitingAlerts = (function () {
                var oldTitle = document.title;
                var msg = "!!! " + document.title;
                var timeoutId;
                var blink = function () {
                    document.title = document.title == msg ? ' ' : msg;
                };
                var clear = function () {
                    clearInterval(timeoutId);
                    document.title = oldTitle;
                    window.onmousemove = null;
                    timeoutId = null;
                    inst.isBlinking = false;
                };
                return function () {
                    if (!timeoutId) {
                        timeoutId = setInterval(blink, 1000);
                        window.onmousemove = clear;
                    }
                };
            }());
            newExcitingAlerts();
            this.isBlinking = true;
        }
    };

    this.playSoundNewAction = function (identifier, chat_id, nick, message, nt) {

        if (this.backgroundChats.indexOf(parseInt(chat_id)) != -1) {
            return;
        }

        if (_that.n_chat_snd == 1 && (confLH.sn_off == 1 || _that.hideOnline == false) && (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered')) {
            this.soundPlayedTimes = 0;
            this.playNewChatAudio();
        }

        if (!$("textarea[name=ChatMessage]").is(":focus") && (confLH.sn_off == 1 || _that.hideOnline == false) && (identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered')) {
            this.startBlinking();
        }

        if ((identifier == 'pending_chat' || identifier == 'transfer_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered') && (confLH.sn_off == 1 || _that.hideOnline == false) && window.Notification && window.Notification.permission == 'granted') {

            var notification = new Notification(nick, {
                icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png',
                body: message,
                requireInteraction: true
            });

            notification.onclick = function () {
                if (identifier == 'pending_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered') {
                    if ($('#tabs').length > 0) {
                        window.focus();
                        _that.startChatDashboard(chat_id);
                    } else {
                        _that.startChatNewWindow(chat_id);
                    }
                } else {
                    _that.startChatNewWindowTransferByTransfer(chat_id, nt);
                }
                notification.close();
            };

            if (identifier != 'pending_transfered') {
                if (this.notificationsArray[chat_id] !== 'undefined') {
                    notification.close();
                }
                this.notificationsArray[chat_id] = notification;
            }
        }

        if (identifier == 'transfer_chat' && confLH.show_alert_transfer == 1) {
            if (confirm(confLH.transLation.transfered + "\n\n" + message)) {
                _that.startChatNewWindowTransferByTransfer(chat_id, nt);
            }
        }

        if (confLH.show_alert == 1) {
            if (confirm(confLH.transLation.new_chat + "\n\n" + message)) {
                if (identifier == 'pending_chat' || identifier == 'unread_chat' || identifier == 'pending_transfered') {
                    if ($('#tabs').length > 0) {
                        window.focus();
                        _that.startChatDashboard(chat_id);
                    } else {
                        _that.startChatNewWindow(chat_id);
                    }
                } else {
                    _that.startChatNewWindowTransferByTransfer(chat_id, nt);
                }
            }
        }
    };

    $scope.loadChatList = function () {

        if (localStorage) {
            try {
                $scope.pending_chats_expanded = localStorage.getItem('pending_chats_expanded') != 'false';
                $scope.active_chats_expanded = localStorage.getItem('active_chats_expanded') != 'false';
                $scope.my_active_chats_expanded = localStorage.getItem('my_active_chats_expanded') != 'false';
                $scope.closed_chats_expanded = localStorage.getItem('closed_chats_expanded') != 'false';
                $scope.unread_chats_expanded = localStorage.getItem('unread_chats_expanded') != 'false';
                $scope.my_chats_expanded = localStorage.getItem('my_chats_expanded') != 'false';

                // Just for extension reserved keywords
                $scope.custom_list_1_expanded = localStorage.getItem('custom_list_1_expanded') != 'false';
                $scope.custom_list_2_expanded = localStorage.getItem('custom_list_2_expanded') != 'false';
                $scope.custom_list_3_expanded = localStorage.getItem('custom_list_3_expanded') != 'false';
                $scope.custom_list_4_expanded = localStorage.getItem('custom_list_4_expanded') != 'false';
            } catch (err) {

            }
        }

        if (_that.blockSync == true) {
            clearTimeout($scope.timeoutControl);
            $scope.timeoutControl = setTimeout(function () {
                $scope.loadChatList();
            }, confLH.back_office_sinterval);
            return;
        }

        clearTimeout($scope.timeoutControl);
        LiveHelperChatFactory.loadChatList($scope.getSyncFilter()).then(function (data) {

            ee.emitEvent('eventLoadChatList', [data, $scope, _that]);

            if (typeof data.items_processed == 'undefined') {

                var currentStatusNotifications = []; // Holds current status of chat's list,  _that.statusNotifications previous status

                var chatsToNotify = []; // Holds chat's to notify about for particular last_id_identifier item

                var notificationsData = [], notificationDataAccept = []; // Holds chat's to notify for all lists

                var tabs = $('#tabs');

                angular.forEach(data.result, function (item, key) {

                    $scope[key] = item;

                    if (tabs.length > 0) {
                        if (key == 'pending_chat' || key == 'my_chats') {
                            item.list.forEach(function (chat) {
                                if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_chats == 1 && chat.status !== 1) {
                                    if (_that.syncChatsOpen.indexOf(chat.id) === -1) {
                                        _that.startChatBackground(chat.id);
                                        notificationDataAccept.push(chat.id);
                                    }
                                }
                            });
                        }
                    }

                    if (item.last_id_identifier) {
                        chatsToNotify = [];

                        currentStatusNotifications = [];

                        var chatsSkipped = 0; // Do not show notification for chats if they appear at the bottom, only applies to unassigned chats

                        var itemsList = item.list;
                        if (item.last_id_identifier == 'pending_chat' && typeof _that.toggleWidgetData['pending_chats_sort'] !== 'undefined' && _that.toggleWidgetData['pending_chats_sort'] == true) {
                            itemsList = item.list.slice().reverse();
                        }

                        angular.forEach(itemsList, function (itemList, keyItem) {

                            var userId = (typeof itemList.user_id !== 'undefined' ? itemList.user_id : 0);

                            var identifierElement = itemList.id + '_' + userId;

                            currentStatusNotifications.push(identifierElement);

                            if (typeof _that.statusNotifications[item.last_id_identifier] == 'undefined') {
                                _that.statusNotifications[item.last_id_identifier] = new Array();
                            }

                            if (_that.isListLoaded == true && (chatsSkipped == 0 || itemList.status_sub_sub === 2) && ((_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == 0 && confLH.ownntfonly == 0) || (_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == confLH.user_id))) {
                                if (lhinst.chatsSynchronising.indexOf(parseInt(itemList.id)) === -1) { // Don't show notification if chat is under sync already
                                    chatsToNotify.push(itemList.id);
                                }
                            } else {
                                chatsSkipped++;
                            }
                            ;
                        });

                        if (chatsToNotify.length > 0) {
                            chatsToNotify.unshift(item.last_id_identifier);
                            notificationsData.push(chatsToNotify.join("/"));
                        }
                        ;

                        if (_that.isListLoaded == true) {
                            _that.compareNotificationsAndHide(_that.statusNotifications[item.last_id_identifier], currentStatusNotifications);
                        }

                        _that.statusNotifications[item.last_id_identifier] = currentStatusNotifications;
                    }
                });

                if (notificationDataAccept.length > 0) {
                    notificationDataAccept.unshift('active_chat');
                    LiveHelperChatFactory.getNotificationsData(notificationDataAccept.join("/")).then(function (data) {
                        angular.forEach(data, function (item, key) {
                            _that.backgroundChats.splice(_that.backgroundChats.indexOf(item.last_id), 1);
                            _that.playSoundNewAction(item.last_id_identifier, parseInt(item.last_id), (item.nick ? item.nick : 'Live Help'), (item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
                            //_that.setMetaData(item.last_id,'lmsg',(item.msg ? item.msg : confLH.transLation.new_chat));
                            _that.backgroundChats.push(item.last_id);
                        });
                    });
                }

                if (notificationsData.length > 0) {
                    LiveHelperChatFactory.getNotificationsData(notificationsData.join("/")).then(function (data) {
                        angular.forEach(data, function (item, key) {
                            _that.playSoundNewAction(item.last_id_identifier, parseInt(item.last_id), (item.nick ? item.nick : 'Live Help'), (item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
                            //_that.setMetaData(item.last_id,'lmsg',(item.msg ? item.msg : confLH.transLation.new_chat));
                        });
                    });
                }
            }

            if (typeof data.ou !== 'undefined') {
                eval(data.ou);
            }

            if (typeof data.fs !== 'undefined' && data.fs.length > 0) {
                angular.forEach(data.fs, function (item, key) {
                    _that.playSoundNewAction('pending_transfered', parseInt(item.id), (item.nick ? item.nick : 'Live Help'), confLH.transLation.transfered, item.nt, item.uid);
                });
            }

            _that.hideOnline = data.ho == 1;
            _that.hideInvisible = data.im == 1;

            if (_that.lhcVersion != data.v) {
                _that.lhcVersion = data.v;
                _that.lhcPendingRefresh = true;
                _that.versionChanged();
            }

            if ($scope.setTimeoutEnabled == true) {
                $scope.timeoutControl = setTimeout(function () {
                    $scope.loadChatList();
                }, confLH.back_office_sinterval);
            }

            _that.startSyncChats(_that.isListLoaded);

            _that.isListLoaded = true;

        }, function (error) {
            console.log(error);
            $scope.timeoutControl = setTimeout(function () {
                $scope.loadChatList();
            }, confLH.back_office_sinterval);
        });
    };

    this.versionChanged = function () {
        var _that = this;
        $interval(function () {
            _that.lhcVersionCounter = _that.lhcVersionCounter - 1;
            if (_that.lhcVersionCounter == 0) {
                document.location.reload(true);
            }
        }, 1000);
    };

    this.compareNotificationsAndHide = function (oldStatus, newStatus) {
        if (typeof oldStatus !== 'undefined') {
            for (var i = oldStatus.length - 1; i >= 0; i--) {
                var key = oldStatus[i];
                if (-1 === newStatus.indexOf(key)) {
                    lhinst.hideNotification(key.split('_')[0]);
                }
            }
        }
    };

    this.previewChat = function (chat_id) {
        lhc.previewChat(chat_id);
    };
    this.previewChatArchive = function (archive_id, chat_id) {
        lhc.previewChatArchive(archive_id, chat_id);
    };

    this.redirectContact = function (chat_id, message) {
        return lhinst.redirectContact(chat_id, message);
    };

    this.isDashboard = false;

    this.activateDashboard = function() {
        if (this.isDashboard == true) {
            this.currentPanel = 'dashboard';
            this.current_chat_id = 0;
        } else {
            document.location = WWW_DIR_JAVASCRIPT;
        }
    };
    
    this.deleteChat = function (chat_id, tabs, hidetab) {
        return lhinst.deleteChat(chat_id, tabs, hidetab);
    };

    this.startChatNewWindowTransfer = function (chat_id, name, transfer_id) {

        LiveHelperChatFactory.acceptTransfer(transfer_id,false).then(function (data) {
            ee.emitEvent('operatorAcceptedTransfer', [chat_id]);
        });

        return this.startChatNewWindow(chat_id,name);
    };

    this.startChatTransfer = function (chat_id, name, transfer_id) {
        LiveHelperChatFactory.acceptTransfer(transfer_id,false).then(function (data) {
            _that.startChatDashboard(chat_id);
            ee.emitEvent('operatorAcceptedTransfer', [chat_id]);
        });
    };

    this.startChatNewWindowTransferByTransfer = function(chat_id) {
        LiveHelperChatFactory.acceptTransfer(chat_id,true).then(function (data) {
            if ($('#tabs').length > 0) {
                _that.startChatDashboard(data.chat_id);
            } else {
                _that.startChatNewWindow(data.chat_id);
            }
            ee.emitEvent('operatorAcceptedTransfer', [data.chat_id]);
        })
    }

    this.startChatOperator = function (user_id) {
        LiveHelperChatFactory.getActiveOperatorChat(user_id).then(function (data) {
            if (data.has_chat === true) {
                this.startChatDashboard(data.chat_id,{'remember' : true});
            } else {
                lhc.revealModal({'url': WWW_DIR_JAVASCRIPT + 'chat/startchatwithoperator/' + user_id});
            }
        });
    };

    this.addEvent = (function () {
        var _that = this;
        if (document.addEventListener) {
            return function (el, type, fn) {
                if (el && el.nodeName || el === window) {
                    el.addEventListener(type, fn, false);
                } else if (el && el.length) {
                    for (var i = 0; i < el.length; i++) {
                        _that.addEvent(el[i], type, fn);
                    }
                }
            };
        } else {
            return function (el, type, fn) {
                if (el && el.nodeName || el === window) {
                    el.attachEvent('on' + type, function () {
                        return fn.call(el, window.event);
                    });
                } else if (el && el.length) {
                    for (var i = 0; i < el.length; i++) {
                        _that.addEvent(el[i], type, fn);
                    }
                }
            };
        }
    })();

    this.setupActivityMonitoring = function () {

        var _that = this;

        var resetTimeout = function () {
            _that.resetTimeoutActivity();
        };

        this.addEvent(window, 'mousemove', resetTimeout);
        this.addEvent(document, 'mousemove', resetTimeout);
        this.addEvent(window, 'mousedown', resetTimeout);
        this.addEvent(window, 'click', resetTimeout);
        this.addEvent(window, 'scroll', resetTimeout);
        this.addEvent(window, 'keypress', resetTimeout);
        this.addEvent(window, 'load', resetTimeout);
        this.addEvent(document, 'scroll', resetTimeout);
        this.addEvent(document, 'touchstart', resetTimeout);
        this.addEvent(document, 'touchend', resetTimeout);

        this.resetTimeoutActivity();
    };

    $scope.resetActivityFromChild = function () {
        _that.resetTimeoutActivity();
    }

    this.resetTimeoutActivity = function () {
        var opener = window.opener;
        if (opener) {
            try {
                // Forward action to parent window and do not set offline mode from child window
                var lhcController = opener.angular.element('body').scope();
                lhcController.resetActivityFromChild();
            } catch (e) {
                console.log(e);
            }
        } else {
            if (this.blockSync == false) {
                clearTimeout(this.timeoutActivity);
                var _that = this;

                this.timeoutActivity = setTimeout(function () {

                    _that.blockSync = true;
                    lhinst.disableSync = true;

                    LiveHelperChatFactory.setInactive('true').then(function (data) {
                        lhc.revealModal({
                            'url': WWW_DIR_JAVASCRIPT + 'user/wentinactive/false', hidecallback: function () {
                                LiveHelperChatFactory.setInactive('false');

                                _that.isListLoaded = false; // Because inactive visitor can be for some quite time, make sure new chat's does not trigger flood of sound notifications
                                _that.blockSync = false;	// Unblock sync
                                _that.resetTimeoutActivity(); // Start monitoring activity again
                                lhinst.disableSync = false;

                                $scope.loadChatList();
                            }
                        });
                    });

                }, _that.timeoutActivityTime * 1000);
            }
        }
    };

    this.getOpenedChatIds = function () {
        if (localStorage) {
            try {
                var achat_id = localStorage.getItem('achat_id');

                if (achat_id !== null && achat_id !== '') {
                    return achat_id_array = achat_id.split(',');
                }
            } catch(e) {

            }
        }
        return [];
    };

    this.verifyFilters = function () {

        var userList = [], userGroups = [], userDepartmentsGroups = [], userProductNames = [];

        angular.forEach(_that.userList, function (value) {
            userList.push(value.id);
        });

        angular.forEach(_that.userGroups, function (value) {
            userGroups.push(value.id);
        });

        angular.forEach(_that.userDepartmentsGroups, function (value) {
            userDepartmentsGroups.push(value.id);
        });

        angular.forEach(_that.userProductNames, function (value) {
            userProductNames.push(value.id);
        });

        var verifyCombinations = {
            'activeu': userList,
            'actived_products': userProductNames,
            'actived_ugroups': userGroups,
            'actived_dpgroups': userDepartmentsGroups,

            'pendingu': userList,
            'pendingd_ugroups': userGroups,
            'pendingd_dpgroups': userDepartmentsGroups,
            'pendingd_products': userProductNames,

            'departmentd_dpgroups': userDepartmentsGroups,

            'closedd_products': userProductNames,
            'closedd_dpgroups': userDepartmentsGroups,

            'unreadd_dpgroups': userDepartmentsGroups,
            'unreadd_products': userProductNames,

            'mcd_products': userProductNames,
            'mcd_dpgroups': userDepartmentsGroups
        };

        angular.forEach(verifyCombinations, function (list, index) {
            angular.forEach(_that[index], function (value) {
                if (list.indexOf(value) === -1) {
                    _that[index].splice(_that[index].indexOf(value), 1);
                    _that.productChanged(index);
                }
                ;
            });
        });
    };

    this.truncateScope = function(nick)
    {
        return LiveHelperChatFactory.truncate(nick, 10)
    }

    // Bootstraps initial attributes
    this.initLHCData = function () {

        var appendURL = '';
        var openedChats = this.getOpenedChatIds();

        if ($('#tabs').length > 0 && lhinst.disableremember == false && openedChats.length > 0) {
            appendURL = '/(chatopen)/' + openedChats.join('/');
        }

        LiveHelperChatFactory.loadInitialData(appendURL).then(function (data) {
            _that.userDepartmentsNames = data.dp_names;
            _that.userDepartments = data.dep_list;
            _that.userProductNames = data.pr_names;
            _that.userDepartmentsGroups = data.dp_groups;
            _that.userGroups = data.user_groups;
            _that.userList = data.user_list;
            _that.hideInvisible = data.im;
            _that.hideOnline = data.ho;
            _that.lhcVersion = data.v;
            _that.additionalColumns = data.col;
            _that.n_chat_snd = data.n_chat_snd;
            _that.n_msg_snd = data.n_msg_snd;

            angular.forEach(_that.widgetsItems, function (listId) {
                _that.setDepartmentNames(listId);
            });

            if (data.track_activity == true) {
                _that.timeoutActivityTime = data.timeout_activity;
                _that.setupActivityMonitoring();
            }

            /*angular.forEach(data.copen, function (chatOpen) {
                lhinst.startChat(chatOpen.id, $('#tabs'), LiveHelperChatFactory.truncate(chatOpen.nick, 10), false);
            });*/

            angular.forEach(data.cdel, function (chatOpen) {
                _that.forgetChat(chatOpen);
            });

            ee.emitEvent('eventLoadInitialData', [data, $scope, _that]);

            // Verify that filter attribute are existing
            // Let say some user was removed, but visitor still had it as filter.
            // This would couse situtation then filter is applied but operator cannot remove it
            // We have to take care of this situtations.
            _that.verifyFilters();

            $scope.loadChatList();
        });
    }

    this.initLHCData();


    /**
     * New dashboard functions
     * */
    this.syncChats = [];
    this.syncChatsMsg = [];
    this.syncChatsOpen = [];
    this.backgroundChats = [];

    // Current Chat Id
    this.current_chat_id = 0;

    this.underMessageAdd = false;
    this.syncroRequestSend = false;

    this.hideNotification = function (chat_id) {
        if (typeof this.notificationsArray[chat_id] !== 'undefined' && this.backgroundChats.indexOf(chat_id) == -1) {
            this.notificationsArray[chat_id].close();
            delete this.notificationsArray[chat_id];
        }

        clearTimeout(this.soundIsPlaying);
    }

    this.removeBackgroundChat = function (chat_id) {
        var index = this.backgroundChats.indexOf(chat_id);
        if (index !== -1) {
            this.backgroundChats.splice(index, 1);
        }
    }

    $scope.removeOpenedChat = function(chat_id){

        if ($('#CSChatMessage-'+chat_id).length != 0){
            $('#CSChatMessage-'+chat_id).unbind('keydown', function(){});
            $('#CSChatMessage-'+chat_id).unbind('keyup', function(){});
        }

        var index = _that.syncChatsOpen.indexOf(chat_id);
        if (index !== -1) {
            _that.syncChatsOpen.splice(index, 1);
        }

        var index = _that.syncChats.indexOf(chat_id);

        if (index !== -1) {
            _that.syncChats.splice(index, 1);
            _that.syncChatsMsg.splice(index, 1);
            ee.emitEvent('removeSynchroChat', [chat_id]);
        }

        if (chat_id == _that.current_chat_id) {
            _that.current_chat_id = 0;
            if ( _that.syncChats.length > 0) {
                _that.startChatDashboard(_that.syncChats[0]);
            }
        }

        _that.deleteMetaData(chat_id);
    }

    this.forgetChat = function (chat_id) {
        if (localStorage) {
            try {
                chat_id = parseInt(chat_id);

                var achat_id = localStorage.getItem('achat_id');
                var achat_id_array = new Array();

                if (achat_id !== null) {
                    achat_id_array = achat_id.split(',').map(Number);
                }

                if (achat_id_array.indexOf(chat_id) !== -1){
                    achat_id_array.splice(achat_id_array.indexOf(chat_id), 1);
                }

                localStorage.setItem('achat_id',achat_id_array.join(','));
            } catch (e) {
                console.log(e);
            }
        }
    };

    this.rememberTab = function(chat_id) {
        if (localStorage) {
            try{
                chat_id = parseInt(chat_id);

                var achat_id = localStorage.getItem('achat_id');
                var achat_id_array = new Array();

                if (achat_id !== null) {
                    var achat_id_array = achat_id.split(',').map(Number);
                }

                if (achat_id_array.indexOf(chat_id) === -1) {
                    achat_id_array.push(chat_id);
                }

                localStorage.setItem('achat_id',achat_id_array.join(','));
            } catch (e) {
                console.log(e);
            }
        }
    }

    this.startChat = function (chat_id, name) {
        if ($('#tabs').length > 0) {
            this.startChatDashboard(chat_id,{'remember' : true});
            return;
        } else {
            this.startChatNewWindow(chat_id, name);
        }
    }

    this.startChatNewWindow = function(chat_id,name)
    {
        window.open(WWW_DIR_JAVASCRIPT + 'chat/single/'+chat_id,'chatwindow-chat-id',"menubar=1,resizable=1,width=800,height=650").focus();
        var inst = this;
        setTimeout(function(){
            $scope.loadChatList();
        },1000);

        ee.emitEvent('chatStartOpenWindow', [chat_id]);
    };

    this.closeExternalChat = function(chat) {
        _that.forgetChat(chat.id);
        $scope.removeOpenedChat(chat.id);
        $scope.loadChatList();
    }

    $scope.debugLists = function() {
        console.log(localStorage.getItem('achat_id'));
        console.log(_that.syncChatsOpen);
        console.log(_that.syncChats);
        console.log(_that.syncChatsMsg);
        console.log(_that.chatMetaData);
    }

    this.weekDays = {1 : 'Mon', 2 : 'Tue', 3 : 'Wed', 4 : 'Thur', 5 : 'Fri', 6 : 'Sat', 7 : 'Sun'};

    this.getCreateTime = function (time) {
        if (time.f == 1) {
            return this.weekDays[time.v];
        } else {
            return time.v;
        }
    }

    this.currentPanel = 'dashboard';

    this.startChatDashboard = function (chat_id, params) {

        chat_id = parseInt(chat_id);

        if (typeof params === 'undefined') {
            var params = {};
        }

        // Remember tabs which are not in my active chats tabs
        if (typeof params['remember'] !== 'undefined' && params['remember'] == true && this.syncChats.indexOf(chat_id) === -1) {
            this.rememberTab(chat_id);
        }

        if (typeof params['reload'] !== 'undefined' && params['reload'] == true) {
            this.forgetChat(chat_id);
            $scope.removeOpenedChat(chat_id);
        }

        var args = '';

        if (typeof params['background'] === 'undefined' || params['background'] === false) {
            this.removeBackgroundChat(chat_id);
            this.hideNotification(chat_id);
            this.current_chat_id = chat_id;
            this.currentPanel = null;
            this.setMetaData(chat_id, 'mn', 0);
            $('#tabs a[href="#chatdashboard"]').tab('show');
        } else if (typeof params['background'] !== 'undefined' && params['background'] === true) {
            args = '/(arg)/background';
        }

        if (typeof params['unread'] !== 'undefined' && params['unread'] === true) {
            this.setMetaData(chat_id, 'mn', 1);
        }

        if (this.syncChatsOpen.indexOf(chat_id) === -1) {
            this.syncChatsOpen.push(chat_id);

            LiveHelperChatFactory.loadChat(chat_id + args).then(function (data) {

                var cHtml = $compile(data)($scope);

                $('#chat-content-' + chat_id).html(cHtml);

                if (typeof params['background'] === 'undefined' || params['background'] === false) {
                    setTimeout(function(){
                        $('#CSChatMessage-' + chat_id).focus();
                    },2);
                }

                lhinst.addQuateHandler(chat_id);
                lhinst.loadMainData(chat_id);
                ee.emitEvent('chatTabLoaded', [chat_id]);
            });

            $scope.loadChatList();

        } else {
            if (typeof params['background'] === 'undefined' || params['background'] === false) {
                setTimeout(function () {
                    $('#CSChatMessage-' + chat_id).focus();
                }, 2);

                setTimeout(function () {
                    $('#messagesBlock-' + chat_id).stop(true, false).animate({scrollTop: $('#messagesBlock-' + chat_id).prop('scrollHeight')}, 5);
                    ee.emitEvent('chatTabClicked', [chat_id]);
                }, 500);
            }
        }

        if (typeof params['background'] === 'undefined' || params['background'] === false) {
            ee.emitEvent('chatStartTab', [chat_id]);
        }

        this.syncChatsProcess();
    }

    this.toHex = function(val) {

        var colorSettings = {color: null};

        ee.emitEvent('setCircleColor', [val, colorSettings]);

        if (colorSettings.color === null) {
            string = (((typeof val.online_user_id !== 'undefined') ? val.online_user_id.toString().split('').reverse().join('') : '') + val.nick);

            var hash = 0;
            if (string.length === 0) return hash;
            for (var i = 0; i < string.length; i++) {
                hash = string.charCodeAt(i) + ((hash << 5) - hash);
                hash = hash & hash;
            }

            color = '#';
            for (var i = 0; i < 3; i++) {
                var value = (hash >> (i * 8)) & 255;
                color += ('00' + value.toString(16)).substr(-2);
            }
        } else {
            color = colorSettings.color;
        }

        return color;
    }

    this.startSyncChats = function (initial) {

        var currentChatsId = [];

        angular.forEach(['my_chats','my_open_chats'],function (listIdentifier) {
            if (typeof $scope[listIdentifier] !== 'undefined' && Array.isArray($scope[listIdentifier].list)) {
                angular.forEach($scope[listIdentifier].list, function (val, index) {

                    if (listIdentifier == 'my_open_chats' && currentChatsId.indexOf(val.id) !== -1) {
                        _that.forgetChat(val.id);
                        $scope.removeOpenedChat(val.id);
                        $scope.loadChatList();
                        _that.startChatDashboard(val.id,val.last_msg_id);
                        return ;
                    }

                    if (_that.syncChats.indexOf(val.id) === -1) {
                        _that.syncChats.push(val.id);
                        _that.syncChatsMsg.push(val.id + ',' + val.last_msg_id);

                        /*var nickParts = val.nick.split(' ');
                        if (nickParts.length == 1) {
                            _that.setMetaData(val.id, 'ctit', nickParts[0].substr(0,2).toUpperCase());
                        } else {
                            _that.setMetaData(val.id, 'ctit', nickParts[0].substr(0,1).toUpperCase() + nickParts[1].substr(0,1).toUpperCase());
                        }*/

                        _that.setMetaData(val.id, 'ctit', _that.getChatShortNick(val));
                        _that.setMetaData(val.id, 'cbg', _that.toHex(val));
                    }
                    currentChatsId.push(val.id);
                });
            }
        })


        // Remove chats which are gone from list
        angular.forEach(_that.syncChats, function (chat_id, index) {
            if (currentChatsId.indexOf(chat_id) === -1) {
                _that.forgetChat(chat_id);
                $scope.removeOpenedChat(chat_id);
            }
        })

        angular.forEach(_that.chatMetaData, function(index, chat_id){
            if (_that.syncChats.indexOf(parseInt(chat_id)) === -1) {
                delete _that.chatMetaData[chat_id];
            }
        });

        if (initial === false) {

            angular.forEach(this.syncChats, function(chat_id){
                ee.emitEvent('chatTabMonitor', [chat_id]);
            });

            this.syncChatsProcess();
        }
    }

    this.getChatShortNick = function(val) {

        var chatSettings = {nick: null};

        ee.emitEvent('setCircleNick', [val, chatSettings]);

        if (chatSettings.nick !== null) {
            return chatSettings.nick
        }

        var nickParts = val.nick.split(' ');
        if (nickParts.length == 1) {
           return nickParts[0].substr(0,2).toUpperCase();
        } else {
           return nickParts[0].substr(0,1).toUpperCase() + nickParts[1].substr(0,1).toUpperCase();
        }
    }

    this.setImage = function(val) {
        setTimeout(function(){
            var element = document.getElementById('chat-icon-img-'+val.id);
            if (element !== null) {
                $(element).attr('data-jdenticon-value',_that.getIcon(val)).jdenticon();
            }
        },500);
    }

    this.syncChatsProcess = function () {
        this.loadChatMessages();
        clearTimeout($scope.timeoutControlSync);
        $scope.timeoutControlSync = setTimeout(function () {
            _that.loadChatMessages();
        }, confLH.chat_message_sinterval);
    }

    // Not used at the moment
    /*this.getChatIndexById = function (id) {
        if (typeof $scope['my_chats'] !== 'undefined' && Array.isArray($scope['my_chats'].list)) {
            angular.forEach($scope['my_chats'].list, function (val, index) {
                if (val.id == id) {
                    return index;
                }
            });
        }

        return -1;
    }*/

    this.chatMetaData = {};

    this.setMetaData = function (chat_id, attr, val) {
        if (typeof this.chatMetaData[chat_id] === 'undefined') {
            this.chatMetaData[chat_id] = {};
        }

        if (typeof this.chatMetaData[chat_id][attr] === 'undefined') {
            this.chatMetaData[chat_id][attr] = val;
        } else {
            this.chatMetaData[chat_id][attr] = val;
        }
    }

    this.getIcon = function(chat){
        var hash = ((typeof chat.online_user_id !== 'undefined') ? chat.online_user_id : '') + chat.nick;
        return hash;
    }

    this.getMetaData = function (chat_id, attr) {
        if (typeof this.chatMetaData[chat_id] === 'undefined') {
            return false;
        }

        if (typeof this.chatMetaData[chat_id][attr] !== 'undefined') {
            return this.chatMetaData[chat_id][attr];
        }

        return false;
    }

    this.deleteMetaData = function(chat_id) {
        if (typeof this.chatMetaData[chat_id] !== 'undefined') {
            delete this.chatMetaData[chat_id];
        }
    }

    this.playNewMessageSound = function () {
        if (Modernizr.audio) {
            this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.ogg' :
                Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/new_message.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/new_message.wav';
            this.audio.load();
        }

        if (!$("textarea[name=ChatMessage]").is(":focus")) {
            this.startBlinking();
        }
    };

    this.notificationsArrayMessages = [];

    this.showNewMessageNotification = function (chat_id, message, nick) {
        try {

            if (window.Notification && focused == false && window.Notification.permission == 'granted') {
                if (typeof this.notificationsArrayMessages[chat_id] !== 'undefined') {
                    this.notificationsArrayMessages[chat_id].close();
                    delete this.notificationsArrayMessages[chat_id];
                }

                var notification = new Notification(nick, {
                    icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png',
                    body: message
                });

                var _that = this;

                notification.onclick = function () {
                    window.focus();
                    notification.close();
                    delete _that.notificationsArrayMessages[chat_id];
                };

                notification.onclose = function () {
                    if (typeof _that.notificationsArrayMessages[chat_id] !== 'undefined') {
                        delete _that.notificationsArrayMessages[chat_id];
                    }
                };

                this.notificationsArrayMessages[chat_id] = notification;
                this.scheduleNewMessageClose(notification, chat_id);
            }
        } catch (err) {
            console.log(err);
        }
    };

    this.scheduleNewMessageClose = function (notification, chat_id) {
        var _that = this;
        setTimeout(function () {
            if (window.webkitNotifications) {
                notification.cancel();
            } else {
                notification.close();
            }

            if (typeof _that.notificationsArrayMessages[chat_id] !== 'undefined') {
                delete _that.notificationsArrayMessages[chat_id];
            }

        }, 10 * 1000);
    };

    $scope.loadchatMessagesScope = function(){
        _that.loadChatMessages();
    }

    this.loadChatMessages = function () {
        if (this.syncChats.length > 0) {
            if (this.underMessageAdd == false && this.syncroRequestSend == false) {

                this.syncroRequestSend = true;

                clearTimeout($scope.timeoutControlSync);

                var paramsCall = [];

                angular.forEach(this.syncChats, function (chat_id, index) {
                    paramsCall.push(_that.syncChatsMsg[index] + ',' + (_that.syncChatsOpen.indexOf(chat_id) !== -1 ? '1' : '0'));
                });

                $.postJSON(WWW_DIR_JAVASCRIPT + '/chat/syncadmin', {'chats[]': paramsCall}, function (data) {

                    if (typeof data.error_url !== 'undefined') {
                        document.location.replace(data.error_url);
                    }

                    try {
                        // If no error
                        if (data.error == 'false') {
                            if (data.result != 'false') {
                                var playSound = false

                                $.each(data.result, function (i, item) {
                                    var messageBlock = $('#messagesBlock-' + item.chat_id);
                                    var scrollHeight = messageBlock.prop("scrollHeight");
                                    var isAtTheBottom = Math.abs((scrollHeight - messageBlock.prop("scrollTop")) - messageBlock.prop("clientHeight"));

                                    messageBlock.find('.pending-storage').remove();
                                    messageBlock.append(item.content);

                                    lhinst.addQuateHandler(item.chat_id);

                                    if (isAtTheBottom < 20) {
                                        messageBlock.stop(true, false).animate({scrollTop: scrollHeight}, 500);
                                    }

                                    _that.updateChatLastMessageID(parseInt(item.chat_id), parseInt(item.message_id));

                                    if (_that.current_chat_id != item.chat_id) {
                                        var mn = _that.getMetaData(item.chat_id, 'mn');
                                        _that.setMetaData(item.chat_id, 'mn', mn !== false ? (mn + item.mn) : item.mn);
                                    } else {
                                        _that.setMetaData(item.chat_id, 'mn', 0);
                                    }

                                    if (playSound == false && data.uw == 'false' && (typeof item.ignore === 'undefined' || typeof item.ignore === false)) {
                                        playSound = true;
                                    }

                                    if (confLH.new_message_browser_notification == 1 && data.uw == 'false' && (typeof item.ignore === 'undefined' || typeof item.ignore === false)) {
                                        _that.showNewMessageNotification(item.chat_id, item.msg, item.nck);
                                    }

                                    if (item.msfrom > 0) {
                                        if ($('#msg-' + item.msfrom).attr('data-op-id') != item.msop) {
                                            $('#msg-' + item.msfrom).next().addClass('operator-changes');
                                        }
                                    }

                                    ee.emitEvent('eventSyncAdmin', [item, i]);
                                });

                                if (_that.n_msg_snd == 1 && data.uw == 'false' && playSound == true) {
                                    _that.playNewMessageSound();
                                }
                            }

                            if (data.result_status != 'false') {
                                $.each(data.result_status, function (i, item) {

                                    _that.setMetaData(item.chat_id, 'ucs', item.us);

                                    if (item.lmsgtxt !== '') {
                                        _that.setMetaData(item.chat_id, 'lmsg', item.lmsgtxt);
                                    }

                                    _that.setMetaData(item.chat_id, 'um', item.um);

                                    if (_that.syncChatsOpen.indexOf(item.chat_id) !== -1) {
                                        if (item.tp == 'true') {
                                            $('#user-is-typing-' + item.chat_id).html(item.tx).css('visibility', 'visible');
                                        } else {
                                            $('#user-is-typing-' + item.chat_id).css('visibility', 'hidden');
                                        }

                                        $('#chat-duration-' + item.chat_id).text(item.cdur);

                                        $('#last-msg-chat-' + item.chat_id).text(item.lmsg);

                                        var statusel = $('#chat-id-' + item.chat_id + '-mds');

                                        if (statusel.attr('data-chat-status') != item.cs || statusel.attr('data-chat-user') != item.co) {
                                            lhinst.updateVoteStatus(item.chat_id);
                                        }

                                        if (item.um == 1) {
                                            statusel.removeClass('chat-active');
                                            statusel.addClass('chat-unread');
                                        } else {
                                            statusel.removeClass('chat-unread');
                                            statusel.addClass('chat-active');
                                        }

                                        if (item.lp !== false) {
                                            statusel.attr('title', item.lp + ' s.');
                                        } else {
                                            statusel.attr('title', '');
                                        }

                                        if (typeof item.oad != 'undefined') {
                                            eval(item.oad);
                                        }
                                    }

                                });
                            }
                        }
                    } catch (err) {
                        console.log(err);
                    }

                    _that.syncroRequestSend = false;

                    clearTimeout($scope.timeoutControlSync);

                    $scope.timeoutControlSync = setTimeout(function () {
                        _that.loadChatMessages();
                    }, confLH.chat_message_sinterval);
                });
            }
        }
    }

    this.updateChatLastMessageID = function (chat_id, message_id) {
        this.syncChatsMsg[this.syncChats.indexOf(chat_id)] = chat_id + ',' + message_id;
    };

    // Preload sound
    this.playPreloadSound = function() {
        if (Modernizr.audio) {
            this.audio.src = Modernizr.audio.ogg ? WWW_DIR_JAVASCRIPT_FILES + '/silence.ogg' :
                Modernizr.audio.mp3 ? WWW_DIR_JAVASCRIPT_FILES + '/silence.mp3' : WWW_DIR_JAVASCRIPT_FILES + '/silence.wav';
            this.audio.load();
        }
    };

    function preloadSound() {
        _that.playPreloadSound();
        jQuery(document).off("click", preloadSound);
        jQuery(document).off("touchstart", preloadSound);
    }

    jQuery(document).on("click", preloadSound);
    jQuery(document).on("touchstart", preloadSound);
    preloadSound();

}]);

