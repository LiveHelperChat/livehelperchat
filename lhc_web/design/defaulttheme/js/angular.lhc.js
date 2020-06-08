$( document ).ready(function() {
	var hash = window.location.hash;	
	if (hash != '') {
		$('ul[role="tablist"] a[href="' + hash.replace("#!","") + '"]').tab('show');
	}
});

var phonecatApp = angular.module('lhcApp', [
  'lhcAppServices',
  'lhcAppControllers'
]);

var services = angular.module('lhcAppServices', []);
var lhcAppControllers = angular.module('lhcAppControllers', ["checklist-model"]);

angular.element(document).ready(function(){
    var element = angular.element(document.querySelector("form"));
    element.triggerHandler("$destroy");
    //‌​
});


services.factory('LiveHelperChatFactory', ['$http','$q',function ($http, $q) {
	
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

	this.loadInitialData = function(appendURL) {
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/loadinitialdata' + appendURL).then(function(data) {
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


lhcAppControllers.controller('LiveHelperChatCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','LiveHelperChatFactory', function($scope, $http, $location, $rootScope, $log, $interval,LiveHelperChatFactory) {

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

	// Just for extension reserved keywords
	$scope.custom_list_1_expanded = true;
	$scope.custom_list_2_expanded = true;
	$scope.custom_list_3_expanded = true;
	$scope.custom_list_4_expanded = true;

	$scope.current_user_id = confLH.user_id;

	// Parameters for back office sync
	
	var _that = this;
	
	this.restoreLocalSetting = function(variable,defaultValue,split) {
		try {
			if (localStorage) {
				var value = localStorage.getItem(variable);
				if (value !== null){
					if (split == true){
						
						var values = value.split('/');
						var valuesInt = new Array();
						
						angular.forEach(values, function(val) {
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
		} catch(e) {}
		return defaultValue;
	};

	this.custom_extension_filter = '';

	// Active chat limit
	this.limitb = this.restoreLocalSetting('limitb','10',false);
	this.limita = this.restoreLocalSetting('limita','10',false);
	this.limitu = this.restoreLocalSetting('limitu','10',false);
	this.limitp = this.restoreLocalSetting('limitp','10',false);
	this.limito = this.restoreLocalSetting('limito',confLH.dlist.op_n,false);
	this.limitc = this.restoreLocalSetting('limitc','10',false);
	this.limitd = this.restoreLocalSetting('limitd','10',false);
	this.limitmc = this.restoreLocalSetting('limitmc','10',false);
	this.limitgc = this.restoreLocalSetting('limitgc','10',false);

	// Active chat's operators filter
	this.activeu = this.restoreLocalSetting('activeu',[],true);
	this.pendingu = this.restoreLocalSetting('pendingu',[],true);
	
	// Main left menu of pagelayout
	$scope.lmtoggle = this.restoreLocalSetting('lmtoggle','false',false) != 'false';
	$scope.lmtoggler = this.restoreLocalSetting('lmtoggler','false',false) != 'false';

    this.lhcVersion = 0;
    this.lhcVersionCounter = 8;
    this.lhcPendingRefresh = false;
    this.lhcConnectivityProblem = false;
    this.lhcConnectivityProblemExplain = '';

	// Stores last ID of unread/pending chat id
	this.lastidEvent = 0;
	
	// User departments
	this.userDepartments = [];
	this.userProductNames = [];
	this.userDepartmentsGroups = [];
	this.userGroups = [];
	this.userList = [];
	this.widgets = [];
	this.additionalColumns = [];

	this.departmentd = this.restoreLocalSetting('departmentd',[],true);
	this.departmentd_dpgroups = this.restoreLocalSetting('departmentd_dpgroups',[],true);
	this.departmentdNames = [];	
	
	this.operatord = this.restoreLocalSetting('operatord',[],true);
	this.operatord_dpgroups = this.restoreLocalSetting('operatord_dpgroups',[],true);
	this.operatordNames = [];

	// Chats with products filters
	this.actived = this.restoreLocalSetting('actived',[],true);
	this.actived_products = this.restoreLocalSetting('actived_products',[],true);
	this.actived_dpgroups = this.restoreLocalSetting('actived_dpgroups',[],true);
	this.actived_ugroups = this.restoreLocalSetting('actived_ugroups',[],true);
	this.activedNames = [];

	
	this.mcd = this.restoreLocalSetting('mcd',[],true);
	this.mcd_products = this.restoreLocalSetting('mcd_products',[],true);
	this.mcd_dpgroups = this.restoreLocalSetting('mcd_dpgroups',[],true);
	this.mcdNames = [];

	this.unreadd = this.restoreLocalSetting('unreadd',[],true);
	this.unreadd_products = this.restoreLocalSetting('unreadd_products',[],true);
	this.unreadd_dpgroups = this.restoreLocalSetting('unreadd_dpgroups',[],true);
	this.unreaddNames = [];

	this.pendingd = this.restoreLocalSetting('pendingd',[],true);
	this.pendingd_products = this.restoreLocalSetting('pendingd_products',[],true);
	this.pendingd_dpgroups = this.restoreLocalSetting('pendingd_dpgroups',[],true);
	this.pendingd_ugroups = this.restoreLocalSetting('pendingd_ugroups',[],true);
	this.pendingdNames = [];

	this.botd = this.restoreLocalSetting('botd',[],true);
	this.botd_products = this.restoreLocalSetting('botd_products',[],true);
	this.botd_dpgroups = this.restoreLocalSetting('botd_dpgroups',[],true);
	this.botd_ugroups = this.restoreLocalSetting('botd_ugroups',[],true);
	this.botdNames = [];

	this.closedd = this.restoreLocalSetting('closedd',[],true);
	this.closedd_products = this.restoreLocalSetting('closedd_products',[],true);
	this.closedd_dpgroups = this.restoreLocalSetting('closedd_dpgroups',[],true);
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
    this.alwaysOnline = false;
    this.bot_st = {};

    this.changeVisibility = function(e) {

        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        LiveHelperChatFactory.changeVisibility(!_that.hideInvisible == true ? 'true' : 'false').then( function(data) {
            if (data.error === false) {
            	_that.hideInvisible = !_that.hideInvisible;
            } else if (typeof data.msg !== 'undefined') {
                alert(data.msg);
            } else {
                alert(data);
            }
        },function(error) {
            alert('We could not change your status!');
        });
	};

    this.changeAlwaysOnline = function(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        LiveHelperChatFactory.setAlwaysOnlineMode(!_that.alwaysOnline == true ? 'true' : 'false').then(function(data){
            if (data.error === false) {
                _that.alwaysOnline = !_that.alwaysOnline;
            } else if (typeof data.msg !== 'undefined') {
                alert(data.msg);
            } else {
                alert(data);
            }
        },function(error) {
            alert('We could not change your status! ' + error);
        });
    };

    this.changeOnline = function(e) {

        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        LiveHelperChatFactory.setOnlineMode(!_that.hideOnline == true ? 'true' : 'false').then(function(data){
        	if (data.error === false) {
                _that.hideOnline = !_that.hideOnline;
			} else if (typeof data.msg !== 'undefined') {
        		alert(data.msg);
			} else {
                alert(data);
			}
		},function(error) {
            alert('We could not change your status! ' + error);
        });
	};

	angular.forEach(this.widgetsItems, function(listId) {
		_that[listId + '_all_departments'] = _that.restoreLocalSetting(listId + '_all_departments','false',false) != 'false';
		_that[listId + '_hide_hidden'] = _that.restoreLocalSetting(listId + '_hide_hidden','false',false) != 'false';
		_that[listId + '_hide_disabled'] = _that.restoreLocalSetting(listId + '_hide_disabled','false',false) != 'false';
		_that[listId + '_only_online'] = _that.restoreLocalSetting(listId + '_only_online','false',false) != 'false';
		_that[listId + '_only_explicit_online'] = _that.restoreLocalSetting(listId + '_only_explicit_online','false',false) != 'false';
	});
	
	this.storeLocalSetting = function(variable, value) {
		if (localStorage) {
			try {
				var value = localStorage.setItem(variable, value);	
			} catch(e) {}
		}
	};
	
	this.removeLocalSetting = function(listId) {
		if (localStorage) {
			try {
				localStorage.removeItem(listId);
			} catch(err) {
			};
		}
	};
	
	this.toggleList = function(variable) {
		$scope[variable] = !$scope[variable];		
		if (localStorage) {
    		try {
    			localStorage.setItem(variable,$scope[variable]);
    		} catch(err) {    			   		
    		};
    	}		
	};
	
	this.toggleWidgetData = [];
	
	this.toggleWidget = function(variable, forceReload) {
		_that.toggleWidgetData[variable] = typeof _that.toggleWidgetData[variable] !== 'undefined' ? !_that.toggleWidgetData[variable] : true;

		if (localStorage) {
    		try {
    			localStorage.setItem(variable,_that.toggleWidgetData[variable]);
    		} catch(err) {    			   		
    		};
    	};
		
		if (typeof forceReload !== 'undefined' && forceReload == true) {
			$scope.loadChatList();
		}
	};
	
	this.toggleWidgetSort = function(variable, val, val_desc, forceReload) {
		_that.toggleWidgetData[variable] = typeof _that.toggleWidgetData[variable] === 'undefined' ? val : (_that.toggleWidgetData[variable] == val ? val_desc : val);
		
		if (localStorage) {
    		try {
    			localStorage.setItem(variable, _that.toggleWidgetData[variable]);
    		} catch(err) {    			   		
    		};
    	};
		
		if (typeof forceReload !== 'undefined' && forceReload == true) {
			$scope.loadChatList();
		}
	};
	
	this.getToggleWidget = function(variable, defaultValue) {
		this.toggleWidgetData[variable] = this.restoreLocalSetting(variable,(typeof defaultValue === 'undefined' ? 'false' : defaultValue), false) == 'false' ? false : true;
	};
	
	this.getToggleWidgetSort = function(variable) {
		this.toggleWidgetData[variable] = this.restoreLocalSetting(variable,'',false);
	};
	
	$scope.getSyncFilter = function()
	{
		_that.custom_extension_filter = '';

		var filter = '/(limita)/'+parseInt(_that.limita);
		filter += '/(limitu)/'+parseInt(_that.limitu);
		filter += '/(limitp)/'+parseInt(_that.limitp);
		filter += '/(limito)/'+parseInt(_that.limito);
		filter += '/(limitc)/'+parseInt(_that.limitc);
		filter += '/(limitd)/'+parseInt(_that.limitd);
		filter += '/(limitmc)/'+parseInt(_that.limitmc);
		filter += '/(limitb)/'+parseInt(_that.limitb);
		filter += '/(limitgc)/'+parseInt(_that.limitgc);

        if (typeof _that.widgetsActive == 'object' && _that.widgetsActive.length > 0) {
            filter += '/(w)/'+_that.widgetsActive.join('/');
        }

		if (typeof _that.activeu == 'object' && _that.activeu.length > 0) {
			filter += '/(activeu)/'+_that.activeu.join('/');			
		}

		if (typeof _that.pendingu == 'object' && _that.pendingu.length > 0) {
			filter += '/(pendingu)/'+_that.pendingu.join('/');			
		}
		
		if (typeof _that.actived_dpgroups == 'object' && _that.actived_dpgroups.length > 0) {
			filter += '/(adgroups)/'+_that.actived_dpgroups.join('/');			
		}
		
		if (typeof _that.pendingd_dpgroups == 'object' && _that.pendingd_dpgroups.length > 0) {
			filter += '/(pdgroups)/'+_that.pendingd_dpgroups.join('/');			
		}
		
		if (typeof _that.closedd_dpgroups == 'object' && _that.closedd_dpgroups.length > 0) {
			filter += '/(cdgroups)/'+_that.closedd_dpgroups.join('/');			
		}
		
		if (typeof _that.mcd_dpgroups == 'object' && _that.mcd_dpgroups.length > 0) {
			filter += '/(mdgroups)/'+_that.mcd_dpgroups.join('/');			
		}
		
		if (typeof _that.unreadd_dpgroups == 'object' && _that.unreadd_dpgroups.length > 0) {
			filter += '/(udgroups)/'+_that.unreadd_dpgroups.join('/');			
		}
		
		if (typeof _that.departmentd_dpgroups == 'object' && _that.departmentd_dpgroups.length > 0) {
			filter += '/(ddgroups)/'+_that.departmentd_dpgroups.join('/');			
		}
		
		if (typeof _that.operatord_dpgroups == 'object' && _that.operatord_dpgroups.length > 0) {
			filter += '/(odpgroups)/'+_that.operatord_dpgroups.join('/');			
		}
		
		if (typeof _that.actived == 'object') {	
			if (_that.actived.length > 0) {
				filter += '/(actived)/'+_that.actived.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('actived');
				if (itemsFilter.length > 0) {
					filter += '/(actived)/'+itemsFilter.join('/');
				}
			}
		}
		
		if (typeof _that.mcd == 'object') {	
			if (_that.mcd.length > 0) {
				filter += '/(mcd)/'+_that.mcd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('mcd');
				if (itemsFilter.length > 0) {
					filter += '/(mcd)/'+itemsFilter.join('/');
				}
			}
		}

		if (typeof _that.unreadd == 'object') {
			if (_that.unreadd.length > 0) {
				filter += '/(unreadd)/'+_that.unreadd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('unreadd');
				if (itemsFilter.length > 0) {
					filter += '/(unreadd)/'+itemsFilter.join('/');
				}
			}
		}

		if (typeof _that.pendingd == 'object') {	
			if (_that.pendingd.length > 0) {
				filter += '/(pendingd)/'+_that.pendingd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('pendingd');
				if (itemsFilter.length > 0) {
					filter += '/(pendingd)/'+itemsFilter.join('/');					
				}
			}
			
			if (typeof _that.toggleWidgetData['pending_chats_sort'] !== 'undefined' && _that.toggleWidgetData['pending_chats_sort'] == true) {
				filter += '/(psort)/asc';	
			}
		}
		
		if (typeof _that.operatord == 'object') {
			if (_that.operatord.length > 0) {
				filter += '/(operatord)/'+_that.operatord.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('operatord');
				if (itemsFilter.length > 0) {
					filter += '/(operatord)/'+itemsFilter.join('/');					
				}
			}
		}
		
		if (typeof _that.closedd == 'object' && _that.closedd.length > 0) {	
			filter += '/(closedd)/'+_that.closedd.join('/');
		}
		
		if (typeof _that.departmentd == 'object') {				
			if (_that.departmentd.length > 0) {
				filter += '/(departmentd)/'+_that.departmentd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('departmentd');
				if (itemsFilter.length > 0) {
					filter += '/(departmentd)/'+itemsFilter.join('/');					
				}
			}
		}
		
		if (typeof _that.actived_products == 'object' && _that.actived_products.length > 0) {
			filter += '/(activedprod)/'+_that.actived_products.join('/');
		}

		if (typeof _that.pendingd_ugroups == 'object' && _that.pendingd_ugroups.length > 0) {
			filter += '/(pugroups)/'+_that.pendingd_ugroups.join('/');
		}

		if (typeof _that.actived_ugroups == 'object' && _that.actived_ugroups.length > 0) {
			filter += '/(augroups)/'+_that.actived_ugroups.join('/');
		}
		
		if (typeof _that.mcd_products == 'object' && _that.mcd_products.length > 0) {
			filter += '/(mcdprod)/'+_that.mcd_products.join('/');
		}

		if (typeof _that.unreadd_products == 'object' && _that.unreadd_products.length > 0) {
			filter += '/(unreaddprod)/'+_that.unreadd_products.join('/');
		}

		if (typeof _that.pendingd_products == 'object' && _that.pendingd_products.length > 0) {
			filter += '/(pendingdprod)/'+_that.pendingd_products.join('/');
		}

		if (typeof _that.closedd_products == 'object' && _that.closedd_products.length > 0) {
			filter += '/(closeddprod)/'+_that.closedd_products.join('/');
		}
		
		if (typeof _that.toggleWidgetData['track_open_chats'] !== 'undefined' && _that.toggleWidgetData['track_open_chats'] == true) {
			filter += '/(topen)/true';
		}
		
		if (typeof _that.toggleWidgetData['active_chats_sort'] !== 'undefined' && _that.toggleWidgetData['active_chats_sort'] !== '') {
			filter += '/(acs)/'+_that.toggleWidgetData['active_chats_sort'];
		}

		if (typeof _that.toggleWidgetData['closed_chats_sort'] !== 'undefined' && _that.toggleWidgetData['closed_chats_sort'] !== '') {
			filter += '/(clcs)/'+_that.toggleWidgetData['closed_chats_sort'];
		}

		if (typeof _that.toggleWidgetData['onop_sort'] !== 'undefined' && _that.toggleWidgetData['onop_sort'] !== '') {
			filter += '/(onop)/'+_that.toggleWidgetData['onop_sort'];
		}

		ee.emitEvent('eventGetSyncFilter', [_that, $scope]);

        filter += _that.custom_extension_filter;

		return filter;
	}
	
	$scope.$watch('lhc.limita', function(newVal,oldVal) {       
		if (newVal != oldVal) {							
			_that.storeLocalSetting('limita',newVal);
			$scope.loadChatList();
		};
	});
	
	this.manualFilterByFilter = function(listId) {
		if (_that[listId+'_only_explicit_online'] == true || _that[listId+'_hide_hidden'] == true || _that[listId+'_hide_disabled'] == true || _that[listId+'_only_online'] == true) {
			
			if (_that.userDepartments.length > 0) {
				var listDepartments = [];						
				angular.forEach(_that.userDepartments, function(department) {
					if (
						(_that[listId+'_only_explicit_online'] == false || (_that[listId+'_only_explicit_online'] == true && department.oexp == true)) && 
						(_that[listId+'_hide_hidden'] == false || (_that[listId+'_hide_hidden'] == true && department.hidden == false)) &&
						(_that[listId+'_hide_disabled'] == false || (_that[listId+'_hide_disabled'] == true && department.disabled == false)) && 
						(_that[listId+'_only_online'] == false || (_that[listId+'_only_online'] == true && department.ogen == true))
					) {
						listDepartments.push(department.id);					
					}
				});
				
				if (listDepartments.length == 0) {
					listDepartments.push(-1);
				};
							
				return listDepartments;
			}
		}
		
		return [];
	};

	this.setDepartmentNames = function(listId) {
		_that[listId + 'Names'] = [];			
		angular.forEach(_that[listId], function(value) {
            if (typeof _that.userDepartmentsNames !== 'undefined' && typeof _that.userDepartmentsNames[value] !== 'undefined') {
				_that[listId + 'Names'].push(_that.userDepartmentsNames[value]);
            } else if (typeof _that.userDepartmentsNames !== 'undefined') {
                _that[listId].splice(_that[listId].indexOf(value),1);
                _that.departmentChanged(listId);
            }
		});	
	};
			
	this.departmentChanged = function(listId) {		
		if (_that[listId].length > 0) {
			
			_that[listId + '_all_departments'] = false;
			_that.allDepartmentsChanged(listId,false);

			var listValue = _that[listId].join("/");

			if (listValue != '') {
				_that.storeLocalSetting(listId,listValue);			
				_that.setDepartmentNames(listId);	
			}
			
		} else {
			if (localStorage) {
	    		try {
	    			localStorage.removeItem(listId);
	    		} catch(err) {    			   		
	    		};
	    	}	
		}
		
		_that.isListLoaded = false;
		$scope.loadChatList();
	};

	this.productChanged = function(listId) {
		
		if (_that[listId].length > 0) {

			var listValue = _that[listId].join("/");

			if (listValue != '') {
				_that.storeLocalSetting(listId,listValue);
			}

		} else {
			if (localStorage) {
	    		try {
	    			localStorage.removeItem(listId);
	    		} catch(err) {
	    		};
	    	}
		}
		_that.isListLoaded = false;
		$scope.loadChatList();
	};

	this.allDepartmentsChanged = function(listId, loadlList) {
		
		if (_that[listId + '_all_departments'] == true) {
			_that.storeLocalSetting(listId + '_all_departments', true);	
		} else {
			_that.removeLocalSetting(listId + '_all_departments');
		}
		
		if (_that[listId+'_hide_hidden'] == true) {
			_that.storeLocalSetting(listId + '_hide_hidden', true);	
		} else {
			_that.removeLocalSetting(listId + '_hide_hidden');
		}
		
		if (_that[listId+'_hide_disabled'] == true) {
			_that.storeLocalSetting(listId + '_hide_disabled', true);	
		} else {
			_that.removeLocalSetting(listId + '_hide_disabled');
		}

		if (_that[listId+'_only_online'] == true) {
			_that.storeLocalSetting(listId + '_only_online', true);	
		} else {
			_that.removeLocalSetting(listId + '_only_online');
		}

		if (_that[listId + '_all_departments'] == true)
		{
			_that[listId] = [];
			angular.forEach(_that.userDepartments, function(department) {
				if (
					(_that[listId+'_only_explicit_online'] == false || (_that[listId+'_only_explicit_online'] == true && department.oexp == true)) && 
					(_that[listId+'_hide_hidden'] == false || (_that[listId+'_hide_hidden'] == true && department.hidden == false)) &&
					(_that[listId+'_hide_disabled'] == false || (_that[listId+'_hide_disabled'] == true && department.disabled == false)) && 
					(_that[listId+'_only_online'] == false || (_that[listId+'_only_online'] == true && department.ogen == true))
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

	$scope.$watch('lhc.limitu', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
			_that.storeLocalSetting('limitu',newVal);
			$scope.loadChatList();
		};
	});
	
	$scope.$watch('lhc.limitc', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
			_that.storeLocalSetting('limitc',newVal);
			$scope.loadChatList();
		};
	});
			
	$scope.$watch('lhc.limitp', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
			_that.storeLocalSetting('limitp',newVal);
			$scope.loadChatList();
		};
	});
	
	$scope.$watch('lhc.limito', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
			_that.storeLocalSetting('limito',newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitmc', function(newVal,oldVal) {
		if (newVal != oldVal) {
			_that.storeLocalSetting('limitmc',newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitgc', function(newVal,oldVal) {
		if (newVal != oldVal) {
			_that.storeLocalSetting('limitgc',newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitd', function(newVal,oldVal) {
		if (newVal != oldVal) {
			_that.storeLocalSetting('limitd',newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.activeu', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
			_that.storeLocalSetting('activeu',newVal);
			_that.isListLoaded = false;
			$scope.loadChatList();
		};
	});
	
	$scope.$watch('lhc.pendingu', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
			_that.storeLocalSetting('pendingu',newVal);
			_that.isListLoaded = false;
			$scope.loadChatList();
		};
	});

	$scope.syncDisabled = function(disabled) {
        _that.blockSync = disabled;
    }

	$scope.loadChatList = function() {
		
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
			} catch(err) { 
				
			};
		}			
	
		if (_that.blockSync == true) {
			clearTimeout($scope.timeoutControl);
			$scope.timeoutControl = setTimeout(function(){
				$scope.loadChatList();
			},confLH.back_office_sinterval);
			return;
		}
		
		clearTimeout($scope.timeoutControl);
		LiveHelperChatFactory.loadChatList($scope.getSyncFilter()).then(function(data){

                if (_that.blockSync == true) {
                    clearTimeout($scope.timeoutControl);
                    $scope.timeoutControl = setTimeout(function(){
                        $scope.loadChatList();
                    },confLH.back_office_sinterval);
                    return;
                }

		        if (_that.lhcConnectivityProblem == true) {
                    _that.lhcConnectivityProblem = false;
                }

				ee.emitEvent('eventLoadChatList', [data, $scope, _that]);
				
				if (typeof data.items_processed == 'undefined') {
						              
	                var currentStatusNotifications = []; // Holds current status of chat's list,  _that.statusNotifications previous status
	                
	                var chatsToNotify = []; // Holds chat's to notify about for particular last_id_identifier item
	                
	                var notificationsData = [], notificationDataAccept = []; // Holds chat's to notify for all lists

					var tabs = $('#tabs');

					angular.forEach(data.result, function(item, key) {

						$scope[key] = item;

                        if (tabs.length > 0) {
							if (key == 'pending_chat' || key == 'my_chats') {
								item.list.forEach(function (chat) {
									if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_chats == 1 && chat.status !== 1) {
										if ($('#chat-tab-link-' + chat.id).length == 0) {

											if (tabs.length > 0 && lhinst.disableremember == false) {
                                                lhinst.removeSynchroChat(chat.id);
                                                lhinst.startChatBackground(chat.id, tabs, LiveHelperChatFactory.truncate(chat.nick, 10));
											}

											if (lhinst.disableremember == false) {
                                                notificationDataAccept.push(chat.id);
											}
										}
									}
								});
							} else if (key == 'group_chats') {
                                if (tabs.length > 0 && confLH.auto_join_private  == 1) {
                                    item.list.forEach(function (chat) {
                                        if (chat.type == 1 && chat.jtime == 0 && $('#chat-tab-link-gc' + chat.id).length == 0) {
                                            lhinst.startGroupChat(chat.id,tabs,LiveHelperChatFactory.truncate(chat.name,10),true);
                                        }
                                    });
                                }
                            }
                        }

						if ( item.last_id_identifier ) {
		                    chatsToNotify = [];		                     
		                    												
							currentStatusNotifications = [];
							
							var chatsSkipped = 0; // Do not show notification for chats if they appear at the bottom, only applies to unassigned chats

							var itemsList = item.list;
							if (item.last_id_identifier == 'pending_chat' && typeof _that.toggleWidgetData['pending_chats_sort'] !== 'undefined' && _that.toggleWidgetData['pending_chats_sort'] == true) {
                                itemsList = item.list.slice().reverse();
							}

							angular.forEach(itemsList, function(itemList, keyItem) {
	
		                        var userId = (typeof itemList.user_id !== 'undefined' ? itemList.user_id : 0);
		                       		                        
		                        var identifierElement = itemList.id + '_' + userId;

		                        // No need to store anything as chat is still not notifable
		                        if (item.last_id_identifier == 'bot_chats') {
		                            if (!(itemList.msg_v && itemList.msg_v > _that.bot_st.msg_nm && _that.bot_st.bot_notifications == 1)) {
                                        return;
                                    }
                                }

		                        currentStatusNotifications.push(identifierElement);
		                  	
		                        if (typeof _that.statusNotifications[item.last_id_identifier] == 'undefined') {
		                        	_that.statusNotifications[item.last_id_identifier] = new Array();
		                        };

		                        if (_that.isListLoaded == true && (chatsSkipped == 0 || itemList.status_sub_sub === 2) && ((_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == 0 && confLH.ownntfonly == 0) || (_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == confLH.user_id)) ) {
		                        	if (lhinst.chatsSynchronising.indexOf(parseInt(itemList.id)) === -1) { // Don't show notification if chat is under sync already
                                         chatsToNotify.push(itemList.id);
		                        	}
		                        } else {
		                        	chatsSkipped++;
		                        };
	                        });
							
							if (chatsToNotify.length > 0) {
								chatsToNotify.unshift(item.last_id_identifier);								
								notificationsData.push(chatsToNotify.join("/"));
							};

							if (_that.isListLoaded == true) {
								_that.compareNotificationsAndHide(_that.statusNotifications[item.last_id_identifier],currentStatusNotifications);
							}

							_that.statusNotifications[item.last_id_identifier] = currentStatusNotifications;
						}
					});

                    if (notificationDataAccept.length > 0) {
                        notificationDataAccept.unshift('active_chat');
                        LiveHelperChatFactory.getNotificationsData(notificationDataAccept.join("/")).then(function (data) {
                            angular.forEach(data, function (item, key) {
                                lhinst.removeBackgroundChat(parseInt(item.last_id));
                                lhinst.playSoundNewAction(item.last_id_identifier,parseInt(item.last_id),(item.nick ? item.nick : 'Live Help'),(item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
                                lhinst.backgroundChats.push(parseInt(item.last_id));
                            });
                        });
                    }

					if (notificationsData.length > 0) {
	                    LiveHelperChatFactory.getNotificationsData(notificationsData.join("/")).then(function (data) {
	                        angular.forEach(data, function (item, key) {
	                            lhinst.playSoundNewAction(item.last_id_identifier,parseInt(item.last_id),(item.nick ? item.nick : 'Live Help'),(item.msg ? item.msg : confLH.transLation.new_chat), item.nt);
	                        });
	                    });
	                }
				}
						
				if (typeof data.ou !== 'undefined') {
					eval(data.ou);
				}
		
				if (typeof data.fs !== 'undefined' && data.fs.length > 0) {
					angular.forEach(data.fs, function(item, key) {
						lhinst.playSoundNewAction('pending_transfered',parseInt(item.id),(item.nick ? item.nick : 'Live Help'), confLH.transLation.transfered, item.nt, item.uid);
					});
				}
				
				if (typeof data.mac !== 'undefined' && data.mac.length > 0) {
					var tabs = $('#tabs');

					if (tabs.length > 0 && lhinst.disableremember == false) {
						angular.forEach(data.mac, function(item, key) {
							lhinst.startChatBackground(item.id,tabs,LiveHelperChatFactory.truncate(item.nick,10),false);
						});
					}
				}

				_that.hideOnline = data.ho == 1;
				_that.hideInvisible = data.im == 1;
                _that.alwaysOnline = data.a_on == 1;

				if (_that.lhcVersion != data.v) {
                    _that.lhcVersion = data.v;
                    _that.lhcPendingRefresh = true;
					_that.versionChanged();
				}

				if ($scope.setTimeoutEnabled == true) {
					$scope.timeoutControl = setTimeout(function(){
						$scope.loadChatList();
					},confLH.back_office_sinterval);
				};
				
				_that.isListLoaded = true;
				
		},function(error){

                _that.lhcConnectivityProblem = true;
                _that.lhcConnectivityProblemExplain = error;

				$scope.timeoutControl = setTimeout(function(){
					$scope.loadChatList();
				},confLH.back_office_sinterval);
		});
	};

    this.versionChanged = function() {
		var _that = this;
        $interval(function() {
            _that.lhcVersionCounter = _that.lhcVersionCounter - 1;
            console.log(_that.lhcVersionCounter);
            if (_that.lhcVersionCounter == 0) {
                document.location.reload(true);
            }
        }, 1000);
	};

	this.compareNotificationsAndHide = function(oldStatus, newStatus) {
		if (typeof oldStatus !== 'undefined') {			
			for (var i = oldStatus.length - 1; i >= 0; i--) {
			  var key = oldStatus[i];
			  if (-1 === newStatus.indexOf(key)) {				
				  lhinst.hideNotification(key.split('_')[0]);
			  }
			}
		}
	};
	
	this.appendActiveChats = function(){
		LiveHelperChatFactory.loadActiveChats().then(function(data) {
			
			var tabs = $('#tabs');
			angular.forEach(data.result, function(item, key) {
				lhinst.startChatBackground(item.id, tabs, LiveHelperChatFactory.truncate(item.nick,10));
			});
			
			setTimeout(function(){
				lhinst.syncadmininterfacestatic();
     	    },1000);
		});
	};

	this.previewChat = function(chat_id){		
		lhc.previewChat(chat_id);
	};
	this.previewChatArchive = function(archive_id, chat_id){
		lhc.previewChatArchive(archive_id, chat_id);
	};
	
	this.redirectContact = function(chat_id,message) {	
		return lhinst.redirectContact(chat_id,message);				
	};
	
	this.startChatNewWindow = function(chat_id,name) {
		return lhinst.startChatNewWindow(chat_id,name);	
	};
		
	this.deleteChat = function(chat_id, tabs, hidetab) {
		return lhinst.deleteChat(chat_id, tabs, hidetab);
	};

	this.startGroupChat = function (chat_id, name) {
        if ($('#tabs').length > 0) {
            return lhinst.startGroupChat(chat_id,$('#tabs'),LiveHelperChatFactory.truncate(name,10));
        }
    }

	this.startChat = function (chat_id,name) {	
		if ($('#tabs').length > 0){
			return lhinst.startChat(chat_id,$('#tabs'),LiveHelperChatFactory.truncate(name,10));	
		} else {
			lhinst.startChatNewWindow(chat_id,name);	
		}
	};
	
	this.startChatNewWindowTransfer = function(chat_id,name,transfer_id) {
		return lhinst.startChatNewWindowTransfer(chat_id,name,transfer_id);
	};
		
	this.startChatTransfer = function(chat_id,name,transfer_id) {
		return lhinst.startChatTransfer(chat_id,$('#tabs'),name,transfer_id);
	};

	$scope.startChatOperatorPublic = function(user_id){
        _that.startChatOperator(user_id);
    }

    this.startChatOperator = function(user_id) {
		LiveHelperChatFactory.getActiveOperatorChat(user_id).then(function(data) {
		    lhinst.startGroupChat(data.id,$('#tabs'),LiveHelperChatFactory.truncate(data.name,10));
		});
	};

	this.openModal = function(url) {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+url,hidecallback: function() { $scope.loadChatList(); }});
    }

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
		        el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
		      } else if (el && el.length) {
		        for (var i = 0; i < el.length; i++) {
		        	_that.addEvent(el[i], type, fn);
		        }
		      }
		    };
		  }
	})();
	
	this.setupActivityMonitoring = function() {	
		
		var _that = this;
		
		var resetTimeout = function() {
			_that.resetTimeoutActivity();
        }; 
                
        this.addEvent(window,'mousemove',resetTimeout);     
        this.addEvent(document,'mousemove',resetTimeout);
        this.addEvent(window,'mousedown',resetTimeout);
        this.addEvent(window,'click',resetTimeout);
        this.addEvent(window,'scroll',resetTimeout);        
        this.addEvent(window,'keypress',resetTimeout);
        this.addEvent(window,'load',resetTimeout);    
        this.addEvent(document,'scroll',resetTimeout);        
        this.addEvent(document,'touchstart',resetTimeout);
        this.addEvent(document,'touchend',resetTimeout);
        
        this.resetTimeoutActivity();
	};

    $scope.resetActivityFromChild = function() {
    	_that.resetTimeoutActivity();
	}

	this.resetTimeoutActivity = function() {
        var opener = window.opener;
        if (opener) {
            try {
				// Forward action to parent window and do not set offline mode from child window
				var lhcController = opener.angular.element('body').scope();
				lhcController.resetActivityFromChild();
            } catch(e) {
				console.log(e);
        	}
		} else {
            if (this.blockSync == false)
            {
                clearTimeout(this.timeoutActivity);
                var _that = this;

                this.timeoutActivity = setTimeout(function(){

                    _that.blockSync = true;
                    lhinst.disableSync = true;

                    LiveHelperChatFactory.setInactive('true').then(function (data) {
                        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/wentinactive/false',hidecallback: function() {
                                LiveHelperChatFactory.setInactive('false');

                                _that.isListLoaded = false; // Because inactive visitor can be for some quite time, make sure new chat's does not trigger flood of sound notifications
                                _that.blockSync = false;	// Unblock sync
                                _that.resetTimeoutActivity(); // Start monitoring activity again
                                lhinst.disableSync = false;

                                $scope.loadChatList();
                            }});
                    });

                }, _that.timeoutActivityTime*1000);
            }
        }
    };

	this.getOpenedChatIds = function (listId) {
        if (localStorage) {
        	try {
				var achat_id = localStorage.getItem(listId);

				if (achat_id !== null && achat_id !== '') {
					return achat_id_array = achat_id.split(',');
				}
        	} catch(e) {

			}
        }
        return [];
    };

    $scope.loadchatMessagesScope = function(){
        lhinst.syncadmincall();
    };

	this.verifyFilters = function () {

		var userList = [], userGroups = [], userDepartmentsGroups = [], userProductNames = [];

        angular.forEach(_that.userList, function(value) {
            userList.push(value.id);
        });

        angular.forEach(_that.userGroups, function(value) {
            userGroups.push(value.id);
        });

        angular.forEach(_that.userDepartmentsGroups, function(value) {
            userDepartmentsGroups.push(value.id);
        });

        angular.forEach(_that.userProductNames, function(value) {
            userProductNames.push(value.id);
        });

		var verifyCombinations = {
			'activeu' : userList,
			'actived_products' : userProductNames,
			'actived_ugroups' : userGroups,
			'actived_dpgroups' : userDepartmentsGroups,

            'pendingu' : userList,
            'pendingd_ugroups' : userGroups,
			'pendingd_dpgroups' : userDepartmentsGroups,
			'pendingd_products' : userProductNames,

			'departmentd_dpgroups' : userDepartmentsGroups,

			'closedd_products' : userProductNames,
			'closedd_dpgroups' : userDepartmentsGroups,

			'unreadd_dpgroups' : userDepartmentsGroups,
			'unreadd_products' : userProductNames,

			'mcd_products' : userProductNames,
			'mcd_dpgroups' : userDepartmentsGroups
		};

        angular.forEach(verifyCombinations, function(list, index) {
            angular.forEach(_that[index], function(value) {
                if (list.indexOf(value) === -1) {
                    _that[index].splice(_that[index].indexOf(value),1);
                    _that.productChanged(index);
                };
			});
        });
    };

	this.rejectGroupChat = function (groupChatId) {
        LiveHelperChatFactory.rejectGroupChat(groupChatId).then(function(data) {
            $scope.loadChatList();
        })
    }

    this.startNewGroupChat = function (groupName, publicChat) {
        LiveHelperChatFactory.newGroupChat(groupName,publicChat).then(function(data) {
            lhinst.startGroupChat(data.id,$('#tabs'),LiveHelperChatFactory.truncate(data.name,10));
            $scope.loadChatList();
            _that.new_group_name = "";
            _that.new_group_type = "";
        })
    }

	// Bootstraps initial attributes
	this.initLHCData = function() {

		var appendURL = '';
		var openedChats = this.getOpenedChatIds('achat_id');
		var openedgChats = this.getOpenedChatIds('gachat_id');

		if ($('#tabs').length > 0 && lhinst.disableremember == false && openedChats.length > 0) {
            appendURL = '/(chatopen)/' + openedChats.join('/');
		}

		if ($('#tabs').length > 0 && lhinst.disableremember == false && openedgChats.length > 0) {
            appendURL += '/(chatgopen)/' + openedgChats.join('/');
		}

		LiveHelperChatFactory.loadInitialData(appendURL).then(function(data) {
			_that.userDepartmentsNames=data.dp_names;
			_that.userDepartments=data.dep_list;
			_that.userProductNames=data.pr_names;
			_that.userDepartmentsGroups=data.dp_groups;
			_that.userGroups = data.user_groups;
			_that.userList = data.user_list;
            _that.hideInvisible = data.im;
            _that.hideOnline = data.ho;
            _that.lhcVersion = data.v;
            _that.alwaysOnline = data.a_on;
            _that.additionalColumns = data.col;
            _that.widgetsActive = data.widgets;
            _that.bot_st = data.bot_st;

			angular.forEach(_that.widgetsItems, function(listId) {
				_that.setDepartmentNames(listId);
			});

			if (data.track_activity == true)
			{			
				_that.timeoutActivityTime = data.timeout_activity;
				_that.setupActivityMonitoring();
			}

            angular.forEach(data.copen, function(chatOpen) {
                lhinst.startChat(chatOpen.id,$('#tabs'),LiveHelperChatFactory.truncate(chatOpen.nick,10), false, 0, chatOpen.status);
            });

            angular.forEach(data.cgopen, function(chatOpen) {
                lhinst.startGroupChat(chatOpen.id,$('#tabs'),LiveHelperChatFactory.truncate(chatOpen.nick,10));
            });

            angular.forEach(data.cdel, function(chatOpen) {
                lhinst.forgetChat(chatOpen,'achat_id');
            });

            angular.forEach(data.cgdel, function(chatOpen) {
                lhinst.forgetChat(chatOpen,'gachat_id');
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
	
}]);

