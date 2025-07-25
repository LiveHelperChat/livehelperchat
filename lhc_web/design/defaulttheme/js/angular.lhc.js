try {

var phonecatApp = angular.module('lhcApp', [
  'lhcAppServices',
  'lhcAppControllers'
]);

var services = angular.module('lhcAppServices', []);
var lhcAppControllers = angular.module('lhcAppControllers', ["checklist-model"]);

lhcAppControllers.config(['$compileProvider', function ($compileProvider) {
    $compileProvider.debugInfoEnabled(false);
}]);

lhcAppControllers.run(['$http', function ($http) {
    $http.defaults.headers.common['X-CSRFToken'] = confLH.csrf_token;
}]);

angular.element(document).ready(function(){
    var element = angular.element(document.querySelector("form"));
    element.triggerHandler("$destroy");
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

	this.getChatData = function(id) {
		var deferred = $q.defer();
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/getchatdata/' + id).then(function(data) {
            deferred.resolve(data.data);
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

    // Continue here
    this.setLocalSettings = function(attr,val) {
        var deferred = $q.defer();
        $http.post(WWW_DIR_JAVASCRIPT + 'front/settings',{"attr":attr,"val":val}).then(function(data) {
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

var $_LHC_API = {};

lhcAppControllers.controller('LiveHelperChatCtrl',['$scope','$http','$location','$rootScope', '$log','$interval','LiveHelperChatFactory', function($scope, $http, $location, $rootScope, $log, $interval,LiveHelperChatFactory) {

	$scope.predicate = 'last_visit';
	$scope.pending_chats = {};
	$scope.pending_chats_expanded = true;

	$scope.pending_mails = {};
	$scope.pending_mails_expanded = true;

    $scope.active_mails = {};
    $scope.active_mails_expanded = true;

	$scope.active_chats = {};
	$scope.active_chats_expanded = true;
	$scope.my_active_chats_expanded = true;
	$scope.my_mails_expanded = true;
	$scope.closed_chats = {};
	$scope.closed_chats_expanded = true;
	$scope.unread_chats = {};
	$scope.unread_chats_expanded = true;
	$scope.transfer_dep_chats = {};
	$scope.transfer_chats = {};
	$scope.timeoutControl = null;
	$scope.setTimeoutEnabled = true;
	$scope.lmtoggle = false;
	$scope.rmtoggle = false;

	// Just for extension reserved keywords
	$scope.custom_list_1_expanded = true;
	$scope.custom_list_2_expanded = true;
	$scope.custom_list_3_expanded = true;
	$scope.custom_list_4_expanded = true;

	$scope.current_user_id = confLH.user_id;

	// Parameters for back office sync
    lhinst.channel =
        this.channel =
        new BroadcastChannel('lhc_dashboard');

    this.channel.addEventListener("message", function(event) {
        if (event.isTrusted && event.data.action) {

            if (event.data.action == 'went_active') {
                $('#myModal').modal('hide');
                _that.setActiveInterface();
                return;
            }

            var tabs = $('#tabs');
            if (event.data.args.chat_id && lhinst.chatsSynchronising.indexOf(event.data.args.chat_id) !== -1) {
                if (event.data.action == 'close_chat') {
                    tabs.length > 0 && lhinst.removeDialogTab(event.data.args.chat_id,$('#tabs'),true);
                } else if (event.data.action == 'update_chat' || event.data.action == 'startbackground_chat') {
                    tabs.length > 0 && lhinst.updateVoteStatus(event.data.args.chat_id, true);
                } else if (event.data.action == 'reload_chat') {
                    lhinst.addOpenTrace('channel_message_reload');
                    tabs.length > 0 && lhinst.reloadTab(event.data.args.chat_id, $('#tabs'), event.data.args.nick, true);
                }
            } else if (event.data.action == 'startbackground_chat') {
                lhinst.addOpenTrace('channel_message_open');
                (tabs.length > 0 && lhinst.startChatBackground(event.data.args.chat_id, $('#tabs'), event.data.args.nick)) || ee.emitEvent('chatTabPreload', [event.data.args.chat_id, {focus: false}]);;
            } else if (event.data.action == 'close_chat') {
                ee.emitEvent('removeSynchroChat', [parseInt(event.data.args.chat_id)]);
            } else if (event.data.args.mail_id) {
                if (event.data.action == 'close_mail') {
                    lhinst.removeDialogTabMail(event.data.args.mail_id,$('#tabs'), true, true);
                }
           }
        }
    });

	var _that = this;

    this.restoreSettingByString = function(value,split) {
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
    };

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
	this.limitpm = "10";
	this.limitam = "10";
	this.limitalm = "10";
	this.limitmm = "10";
	this.limitb = "10";
	this.limita = "10";
	this.limitu = "10";
	this.limitp = "10";
	this.limito = confLH.dlist.op_n; this.restoreLocalSetting('limito',confLH.dlist.op_n,false);
	this.limitc = "10";
	this.limitd = "10";
	this.limitmc = "10";
	this.limitgc = "10";
	this.limits = "10";

	// Active chat's operators filter
	this.pendingmu = [];
	this.activemu = [];
	this.alarmmu = [];
	this.activeu = [];
	this.pendingu = [];
	this.subjectu = [];
	this.oopu = [];



	// Main left menu of pagelayout
	$scope.lmtoggle = this.restoreLocalSetting('lmtoggle','false',false) != 'false';
	$scope.rmtoggle = this.restoreLocalSetting('rmtoggle','false',false) != 'false';

    this.lhcVersion = 0;
    this.lhcVersionCounter = 8;
    this.lhcPendingRefresh = false;
    this.lhcConnectivityProblem = false;
    this.lhcConnectivityProblemExplain = '';
    this.lhcListRequestInProgress = false;
    this.lhcSettingUpdateProgress = [];
    this.lhcSettingAllSelected = false;

    // Last activity. Like mouse movement etc.
    this.lastd_activity = Math.round(new Date().getTime()/1000);

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
	this.excludeIcons = [];
	this.notifIcons = [];

	this.departmentd = [];
	this.departmentd_dpgroups = [];
	this.departmentdNames = [];

	this.operatord = [];
	this.operatord_dpgroups = [];
    this.operatord_ugroups = [];
	this.operatordNames = [];

	// Chats with products filters
	this.actived = [];
	this.actived_products = [];
	this.actived_dpgroups = [];
	this.actived_ugroups = [];
	this.activedNames = [];

    this.pendingmd = [];
    this.pendingmd_products = [];
    this.pendingmd_dpgroups = [];
    this.pendingmd_ugroups = [];
    this.pendingmdNames = [];

    this.activemd = [];
    this.activemd_products = [];
    this.activemd_dpgroups = [];
    this.activemd_ugroups = [];

    this.activemdNames = [];

    this.alarmmd = [];
    this.alarmmd_products = [];
    this.alarmmd_dpgroups = [];
    this.alarmmd_ugroups = [];
    this.alarmmdNames = [];

	this.mmd = [];
	this.mmd_dpgroups = [];
	this.mmdNames = [];

	this.mcd = [];
	this.mcd_products = [];
	this.mcd_dpgroups = [];
	this.mcdNames = [];

	this.unreadd = [];
	this.unreadd_products = [];
	this.unreadd_dpgroups = [];
	this.unreaddNames = [];

	this.pendingd = [];
	this.pendingd_products = [];
	this.pendingd_dpgroups = [];
	this.pendingd_ugroups = [];
	this.pendingdNames = [];

	this.botd = [];
	this.botd_products = [];
	this.botd_dpgroups = [];
	this.botd_ugroups = [];
	this.botdNames = [];

    this.subjectd = [];
	this.subjectd_products = [];
	this.subjectd_dpgroups = [];
	this.subjectd_ugroups = [];
	this.subjectdNames = [];

	this.closedd = [];
	this.closedd_products = [];
	this.closedd_dpgroups = [];
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
	this.widgetsItems.push('pendingmd');
	this.widgetsItems.push('activemd');
	this.widgetsItems.push('alarmmd');
	this.widgetsItems.push('botd');
	this.widgetsItems.push('subjectd');
	this.widgetsItems.push('mmd');

    _that['departmentd_hide_dep'] = _that.restoreLocalSetting('departmentd_hide_dep','false',false) != 'false';
    _that['departmentd_hide_dgroup'] = _that.restoreLocalSetting('departmentd_hide_dgroup','false',false) != 'false';

	this.timeoutActivity = null;
	this.timeoutActivityTime = 300;
	this.blockSync = false;

	// Sync icons statuses
    this.hideOnline = false;
    this.hideInvisible = false;
    this.alwaysOnline = false;
    this.inActive = false;
    this.bot_st = {};

    ee.addListener('angularSyncDisabled',function (status) {
        $scope.syncDisabled(status);
    });

    ee.addListener('angularLoadChatList',function () {
        // Always process on chat close
        _that.lhcListRequestInProgress = false;
        $scope.loadChatList();
    });

    ee.addListener('angularStartChatOperatorPublic',function (user_id) {
        $scope.startChatOperatorPublic(user_id);
    });

    ee.addListener('angularStartChatbyId',function (chat_id) {
        lhinst.addOpenTrace('view_clicked');
        _that.startChatByID(chat_id);
    });

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
		_that[listId + '_m_h'] = _that.restoreLocalSetting(listId + '_m_h',null,false);
	});

    this.last_actions_index = 0;
    this.last_actions = [];

    this.addAction = function(data) {
        this.last_actions.unshift(data);
        this.last_actions = this.last_actions.slice(0, 5);
    }

    ee.addListener('angularActionHappened',function(data) {
        _that.addAction(data);
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
            _that.isListLoaded = false;
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
            _that.isListLoaded = false;
			$scope.loadChatList();
		}
	};
    
    this.changeWidgetHeight = function(widget,expand) {
        let elm = document.getElementById(widget+'-panel-list');
        if (elm) {
            _that[widget + '_m_h'] = expand === true ? parseInt(elm.offsetHeight + 28) + 'px' : parseInt(elm.offsetHeight - 28) + 'px';
            localStorage.setItem(widget+'_m_h', _that[widget + '_m_h']);
        }
    }

	this.getToggleWidget = function(variable, defaultValue) {
		this.toggleWidgetData[variable] = this.restoreLocalSetting(variable,(typeof defaultValue === 'undefined' ? 'false' : defaultValue), false) == 'false' ? false : true;
	};
	
	this.getToggleWidgetSort = function(variable, defaultValue) {
		this.toggleWidgetData[variable] = this.restoreLocalSetting(variable,(typeof defaultValue === 'undefined' ? '' : defaultValue),false);
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
		filter += '/(limits)/'+parseInt(_that.limits);
		filter += '/(limitmc)/'+parseInt(_that.limitmc);
		filter += '/(limitmm)/'+parseInt(_that.limitmm);
		filter += '/(limitb)/'+parseInt(_that.limitb);
		filter += '/(limitgc)/'+parseInt(_that.limitgc);
		filter += '/(limitpm)/'+parseInt(_that.limitpm);
		filter += '/(limitam)/'+parseInt(_that.limitam);
		filter += '/(limitalm)/'+parseInt(_that.limitalm);

        if (typeof _that.widgetsActive == 'object' && _that.widgetsActive.length > 0) {
            var map = {
                'my_chats' : 0 ,
                'online_operators' : 1,
                'group_chats' : 2,
                'pending_chats' : 3,
                'online_visitors' : 4,
                'unread_chats' : 5,
                'active_chats' : 6,
                'bot_chats' : 7,
                'transfered_chats' : 8,
                'departments_stats' : 9,
                'pmails' : 10,
                'amails' : 11,
                'malarms' : 12,
                'my_mails' : 30,
                'subject_chats' : 20
            }
            var activeWidgets = [];
            angular.forEach(_that.widgetsActive, function(widget) {
                map[widget] && activeWidgets.push(map[widget]);
            })
            filter += '/(w)/'+activeWidgets.join('/');
        }

		if (typeof _that.activeu == 'object' && _that.activeu.length > 0) {
			filter += '/(activeu)/'+_that.activeu.join('/');			
		}

		if (typeof _that.pendingu == 'object' && _that.pendingu.length > 0) {
			filter += '/(pendingu)/'+_that.pendingu.join('/');			
		}

		if (typeof _that.pendingmu == 'object' && _that.pendingmu.length > 0) {
			filter += '/(pendingmu)/'+_that.pendingmu.join('/');
		}

		if (typeof _that.activemu == 'object' && _that.activemu.length > 0) {
			filter += '/(activemu)/'+_that.activemu.join('/');
		}

		if (typeof _that.alarmmu == 'object' && _that.alarmmu.length > 0) {
            filter += '/(alarmmu)/' + _that.alarmmu.join('/');
        }

		if (typeof _that.oopu == 'object' && _that.oopu.length > 0) {
			filter += '/(oopu)/'+_that.oopu.join('/');
		}

		if (typeof _that.subjectu == 'object' && _that.subjectu.length > 0) {
			filter += '/(subjectu)/'+_that.subjectu.join('/');
		}
		
		if (typeof _that.actived_dpgroups == 'object' && _that.actived_dpgroups.length > 0) {
			filter += '/(adgroups)/'+_that.actived_dpgroups.join('/');			
		}

		if (typeof _that.pendingmd_dpgroups == 'object' && _that.pendingmd_dpgroups.length > 0) {
			filter += '/(pmd)/'+_that.pendingmd_dpgroups.join('/');
		}

		if (typeof _that.activemd_dpgroups == 'object' && _that.activemd_dpgroups.length > 0) {
			filter += '/(amd)/'+_that.activemd_dpgroups.join('/');
		}

		if (typeof _that.alarmmd_dpgroups == 'object' && _that.alarmmd_dpgroups.length > 0) {
			filter += '/(almd)/'+_that.alarmmd_dpgroups.join('/');
		}
		
		if (typeof _that.pendingd_dpgroups == 'object' && _that.pendingd_dpgroups.length > 0) {
			filter += '/(pdgroups)/'+_that.pendingd_dpgroups.join('/');			
		}

		if (typeof _that.subjectd_dpgroups == 'object' && _that.subjectd_dpgroups.length > 0) {
			filter += '/(sdgroups)/'+_that.subjectd_dpgroups.join('/');
		}
		
		if (typeof _that.closedd_dpgroups == 'object' && _that.closedd_dpgroups.length > 0) {
			filter += '/(cdgroups)/'+_that.closedd_dpgroups.join('/');			
		}

		if (typeof _that.botd_dpgroups == 'object' && _that.botd_dpgroups.length > 0) {
			filter += '/(bdgroups)/'+_that.botd_dpgroups.join('/');
		}
		
		if (typeof _that.mcd_dpgroups == 'object' && _that.mcd_dpgroups.length > 0) {
			filter += '/(mdgroups)/'+_that.mcd_dpgroups.join('/');			
		}
		
		if (typeof _that.mmd_dpgroups == 'object' && _that.mmd_dpgroups.length > 0) {
			filter += '/(mmdgroups)/'+_that.mmd_dpgroups.join('/');
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

		if (typeof _that.subjectd == 'object') {
			if (_that.subjectd.length > 0) {
				filter += '/(subjectd)/'+_that.subjectd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('subjectd');
				if (itemsFilter.length > 0) {
					filter += '/(subjectd)/'+itemsFilter.join('/');
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

		if (typeof _that.mmd == 'object') {
			if (_that.mmd.length > 0) {
				filter += '/(mmd)/'+_that.mmd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('mmd');
				if (itemsFilter.length > 0) {
					filter += '/(mmd)/'+itemsFilter.join('/');
				}
			}
		}

		if (typeof _that.pendingmd == 'object') {
			if (_that.pendingmd.length > 0) {
				filter += '/(pendingmd)/'+_that.pendingmd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('pendingmd');
				if (itemsFilter.length > 0) {
					filter += '/(pendingmd)/'+itemsFilter.join('/');
				}
			}
		}

		if (typeof _that.activemd == 'object') {
			if (_that.activemd.length > 0) {
				filter += '/(activemd)/'+_that.activemd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('activemd');
				if (itemsFilter.length > 0) {
					filter += '/(activemd)/'+itemsFilter.join('/');
				}
			}
		}

		if (typeof _that.alarmmd == 'object') {
			if (_that.alarmmd.length > 0) {
				filter += '/(alarmmd)/'+_that.alarmmd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('alarmmd');
				if (itemsFilter.length > 0) {
					filter += '/(alarmmd)/'+itemsFilter.join('/');
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

		if (typeof _that.botd == 'object') {
			if (_that.botd.length > 0) {
				filter += '/(botd)/'+_that.botd.join('/');
			} else {
				var itemsFilter = _that.manualFilterByFilter('botd');
				if (itemsFilter.length > 0) {
					filter += '/(botd)/'+itemsFilter.join('/');
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
			
			if (typeof _that.toggleWidgetData['pending_chats_sort'] !== 'undefined' && _that.toggleWidgetData['pending_chats_sort'] !== '') {
				filter += '/(psort)/' + _that.toggleWidgetData['pending_chats_sort'];
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

		if (typeof _that.operatord_ugroups == 'object' && _that.operatord_ugroups.length > 0) {
			filter += '/(oopugroups)/'+_that.operatord_ugroups.join('/');
		}

		if (typeof _that.subjectd_ugroups == 'object' && _that.subjectd_ugroups.length > 0) {
			filter += '/(sugroups)/'+_that.subjectd_ugroups.join('/');
		}

		if (typeof _that.actived_ugroups == 'object' && _that.actived_ugroups.length > 0) {
			filter += '/(augroups)/'+_that.actived_ugroups.join('/');
		}

		if (typeof _that.pendingmd_ugroups == 'object' && _that.pendingmd_ugroups.length > 0) {
			filter += '/(pmug)/'+_that.pendingmd_ugroups.join('/');
		}

		if (typeof _that.activemd_ugroups == 'object' && _that.activemd_ugroups.length > 0) {
			filter += '/(amug)/'+_that.activemd_ugroups.join('/');
		}

		if (typeof _that.alarmmd_ugroups == 'object' && _that.alarmmd_ugroups.length > 0) {
			filter += '/(almug)/'+_that.alarmmd_ugroups.join('/');
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

		if (typeof _that.subjectd_products == 'object' && _that.subjectd_products.length > 0) {
			filter += '/(subjectdprod)/'+_that.subjectd_products.join('/');
		}

        if (typeof _that.botd_products == 'object' && _that.botd_products.length > 0) {
            filter += '/(botdprod)/'+_that.botd_products.join('/');
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

		if (typeof _that.toggleWidgetData['bot_chats_sort'] !== 'undefined' && _that.toggleWidgetData['bot_chats_sort'] !== '') {
			filter += '/(bcs)/'+_that.toggleWidgetData['bot_chats_sort'];
		}

		if (typeof _that.toggleWidgetData['closed_chats_sort'] !== 'undefined' && _that.toggleWidgetData['closed_chats_sort'] !== '') {
			filter += '/(clcs)/'+_that.toggleWidgetData['closed_chats_sort'];
		}

		if (typeof _that.toggleWidgetData['onop_sort'] !== 'undefined' && _that.toggleWidgetData['onop_sort'] !== '') {
			filter += '/(onop)/'+_that.toggleWidgetData['onop_sort'];
		}

		// What subelements of widgets should be hidden
        // At the moment only departments widget users it
		var hsub = [];
		_that.departmentd_hide_dep === true && hsub.push('dhdep');
		_that.departmentd_hide_dgroup === true && hsub.push('dhdepg');

		if (hsub.length > 0) {
            filter += '/(hsub)/'+hsub.join('/');
        }

        // Last dynamic activity
        if (_that.lastd_activity > 0) {
            filter += '/(lda)/'+_that.lastd_activity;
        }

        _that.lastd_activity = 0;

		ee.emitEvent('eventGetSyncFilter', [_that, $scope]);

        filter += _that.custom_extension_filter;

		return filter;
	}
	
	$scope.$watch('lhc.limita', function(newVal,oldVal) {       
		if (newVal != oldVal) {							
            LiveHelperChatFactory.setLocalSettings('limita', newVal);
            _that.isListLoaded = false;
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

    this.removeItemFromProgressList = function(listId) {
        _that.lhcSettingUpdateProgress.splice(_that.lhcSettingUpdateProgress.indexOf(listId),1);
    }

    this.persistenStoreSettings = function(listId, listValue) {
        LiveHelperChatFactory.setLocalSettings(listId, listValue).then(function() {
            _that.removeItemFromProgressList(listId);
        }, function() {
            _that.removeItemFromProgressList(listId);
        });
    }

	this.departmentChanged = function(listId) {

        if (_that.lhcSettingUpdateProgress.indexOf(listId) !== -1) {
            return;
        }

        _that.lhcSettingUpdateProgress.push(listId);

		if (_that[listId].length > 0) {

            if (_that.lhcSettingAllSelected == false) {
                _that[listId + '_all_departments'] = false;
                _that.allDepartmentsChanged(listId,false);
            }

            _that.lhcSettingAllSelected = false;

			var listValue = _that[listId].join("/");

			if (listValue != '') {
                _that.persistenStoreSettings(listId,listValue);
                _that.setDepartmentNames(listId);
            } else {
                _that.removeItemFromProgressList(listId);
            }

		} else {
            _that.persistenStoreSettings(listId, null);
		}
		
		_that.isListLoaded = false;
		$scope.loadChatList();
	};

	this.productChanged = function(listId) {
		
		if (_that[listId].length > 0) {

			var listValue = _that[listId].join("/");

			if (listValue != '') {
                LiveHelperChatFactory.setLocalSettings(listId, listValue);
			}

		} else {
            LiveHelperChatFactory.setLocalSettings(listId, null);
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

		if (typeof _that[listId+'_hide_dep'] !== 'undefined') {
            if (_that[listId+'_hide_dep'] == true) {
                _that.storeLocalSetting(listId + '_hide_dep', true);
            } else {
                _that.removeLocalSetting(listId + '_hide_dep');
            }
        }

		if (typeof _that[listId+'_hide_dgroup'] !== 'undefined') {
            if (_that[listId+'_hide_dgroup'] == true) {
                _that.storeLocalSetting(listId + '_hide_dgroup', true);
            } else {
                _that.removeLocalSetting(listId + '_hide_dgroup');
            }
        }

		if (_that[listId + '_all_departments'] == true)
		{
            var listNew = [];

			angular.forEach(_that.userDepartments, function(department) {
				if (
					(_that[listId+'_only_explicit_online'] == false || (_that[listId+'_only_explicit_online'] == true && department.oexp == true)) && 
					(_that[listId+'_hide_hidden'] == false || (_that[listId+'_hide_hidden'] == true && department.hidden == false)) &&
					(_that[listId+'_hide_disabled'] == false || (_that[listId+'_hide_disabled'] == true && department.disabled == false)) && 
					(_that[listId+'_only_online'] == false || (_that[listId+'_only_online'] == true && department.ogen == true))
				) {
                    listNew.push(department.id);
				}
			});

			if (listNew.length == 0) {
                listNew.push(-1);
			}

            _that.lhcSettingAllSelected = true;

            _that[listId] = listNew;

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
            LiveHelperChatFactory.setLocalSettings('limitu', newVal);
			$scope.loadChatList();
		};
	});
	
	$scope.$watch('lhc.limitc', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
            LiveHelperChatFactory.setLocalSettings('limitc', newVal);
			$scope.loadChatList();
		};
	});
			
	$scope.$watch('lhc.limitp', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
            LiveHelperChatFactory.setLocalSettings('limitp', newVal);
			$scope.loadChatList();
		};
	});
	
	$scope.$watch('lhc.limito', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
            LiveHelperChatFactory.setLocalSettings('limito', newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitmc', function(newVal,oldVal) {
		if (newVal != oldVal) {
            LiveHelperChatFactory.setLocalSettings('limitmc', newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitmm', function(newVal,oldVal) {
		if (newVal != oldVal) {
            LiveHelperChatFactory.setLocalSettings('limitmm', newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitgc', function(newVal,oldVal) {
		if (newVal != oldVal) {
            LiveHelperChatFactory.setLocalSettings('limitgc', newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.limitd', function(newVal,oldVal) {
		if (newVal != oldVal) {
            LiveHelperChatFactory.setLocalSettings('limitd', newVal);
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.activeu', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
            LiveHelperChatFactory.setLocalSettings('activeu', newVal);
			_that.isListLoaded = false;
			$scope.loadChatList();
		};
	});
	
	$scope.$watch('lhc.pendingu', function(newVal,oldVal) {       
		if (newVal != oldVal) {	
            LiveHelperChatFactory.setLocalSettings('pendingu', newVal);
			_that.isListLoaded = false;
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.oopu', function(newVal,oldVal) {
		if (newVal != oldVal) {
            LiveHelperChatFactory.setLocalSettings('oopu', newVal);
			_that.isListLoaded = false;
			$scope.loadChatList();
		};
	});

	$scope.$watch('lhc.subjectu', function(newVal,oldVal) {
		if (newVal != oldVal) {
            LiveHelperChatFactory.setLocalSettings('subjectu', newVal);
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
				$scope.my_mails_expanded = localStorage.getItem('my_mails_expanded') != 'false';

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
                _that.isListLoaded = false;
                _that.lhcListRequestInProgress = false;
				$scope.loadChatList();
			},confLH.back_office_sinterval);
            _that.lhcListRequestInProgress = false;
			return;
		}

        if (_that.lhcListRequestInProgress === true) {
            return;
        }

        _that.lhcListRequestInProgress = true;

		clearTimeout($scope.timeoutControl);
		LiveHelperChatFactory.loadChatList($scope.getSyncFilter()).then(function(data){

                _that.lhcListRequestInProgress = false;

                if (data.logout || data.error_url) {
                    document.location.reload();
                    return;
                }

                if (_that.blockSync == true) {
                    clearTimeout($scope.timeoutControl);
                    $scope.timeoutControl = setTimeout(function(){
                        _that.isListLoaded = false;
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

                        if (tabs.length == 0 && (key == 'pending_chat' || key == 'my_chats')) {
                            item.list.forEach(function (chat) {
                                if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_chats == 1 && (chat.status !== 1 || (chat.status === 1 && chat.hum === true))) {
                                    ee.emitEvent('chatTabPreload', [chat.id, {focus: false}]);
                                }
                            });
                        }

                        if (tabs.length > 0) {
							if (key == 'pending_chat' || key == 'my_chats') {
								item.list.forEach(function (chat) {
									if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_chats == 1 && (chat.status !== 1 || (chat.status === 1 && chat.hum === true))) {
										if ($('#chat-tab-link-' + chat.id).length == 0) {

											if (tabs.length > 0 && lhinst.disableremember == false) {
                                                lhinst.addOpenTrace('auto_accept');
                                                lhinst.removeSynchroChat(chat.id);
                                                lhinst.startChatBackground(chat.id, tabs, LiveHelperChatFactory.truncate((chat.nick || 'Visitor'), 10));
                                                // We auto open only auto assigned chats
                                                _that.channel.postMessage({'action':'startbackground_chat','args':{'nick': LiveHelperChatFactory.truncate((chat.nick || 'Visitor'), 10), 'chat_id' : parseInt(chat.id)}});
											}

											if (lhinst.disableremember == false) {
                                                notificationDataAccept.push(chat.id);
											}
										}
									}
								});
							} else if (key == 'transfer_chats') {
                                item.list.forEach(function (chat) {
                                    if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && (confLH.accept_chats == 1 || $('#chat-tab-link-' + chat.id).length > 0)) {
                                        if (tabs.length > 0 && lhinst.disableremember == false) {
                                            lhinst.addOpenTrace('auto_accept_transfer');
                                            lhinst.startChatTransfer(chat.id,tabs,LiveHelperChatFactory.truncate((chat.nick || 'Visitor'),10),chat.transfer_id, $('#chat-tab-link-' + chat.id).length == 0);

                                            // Auto open transfered chats in all tabs
                                            _that.channel.postMessage({'action':'startbackground_chat','args':{'nick': LiveHelperChatFactory.truncate((chat.nick || 'Visitor'),10), 'chat_id' : parseInt(chat.id)}});
                                        }
                                        if (lhinst.disableremember == false) {
                                            notificationDataAccept.push(chat.id);
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
                            } else if (key == 'support_chats') {
                                if (tabs.length > 0 && confLH.auto_join_private  == 1) {
                                    item.list.forEach(function (chat) {
                                        // Operator does not have this chat in their account yet
                                        if (document.getElementById('chat-tab-li-'+chat.chat_id) === null) {
                                            lhinst.addOpenTrace('support_chat');
                                            _that.startChatByID(chat.chat_id, true);
                                        } else if (!$('#private-chat-tab-link-'+chat.chat_id).attr('private-loaded')) {
                                            $('#private-chat-tab-link-'+chat.chat_id).attr('private-loaded',true);
                                            ee.emitEvent('privateChatStart', [chat.chat_id,{'unread': true}])
                                        } else if (!$('#chat-tab-link-'+chat.chat_id).hasClass('active')) {
                                            $('#chat-tab-link-'+chat.chat_id+' > .whatshot').removeClass('d-none');
                                        } else if (!$('#private-chat-tab-link-'+chat.chat_id).hasClass('active')) {
                                            $('#private-chat-tab-link-'+chat.chat_id+' > .whatshot').removeClass('d-none');
                                        }
                                    });
                                }
                            } else if (key == 'pending_mails' || key == 'my_mails') {
                                if (tabs.length > 0) {
                                    item.list.forEach(function (chat) {
                                        if (typeof chat.user_id !== 'undefined' && chat.user_id == confLH.user_id && confLH.accept_mails == 1 && chat.status !== 1 && document.getElementById('chat-tab-li-mc'+chat.id) === null) {
                                            lhinst.startMailChat(chat.id,tabs,LiveHelperChatFactory.truncate(chat.subject_front,10),true);
                                        }
                                    });
                                }
                            }
                        }

						if ( item.last_id_identifier) {
		                    chatsToNotify = [];		                     
		                    												
							currentStatusNotifications = [];
							
							var chatsSkipped = 0; // Do not show notification for chats if they appear at the bottom, only applies to unassigned chats

							var itemsList = item.list;
							if (item.last_id_identifier == 'pending_chat' && typeof _that.toggleWidgetData['pending_chats_sort'] !== 'undefined' && _that.toggleWidgetData['pending_chats_sort'] == 'id_asc') {
                                itemsList = item.list.slice().reverse();
							}

							angular.forEach(itemsList, function(itemList, keyItem) {
	
		                        var userId = (typeof itemList.user_id !== 'undefined' ? itemList.user_id : 0);
		                       		                        
		                        var identifierElement = itemList.id + '_' + userId;

		                        // No need to store anything as chat is still not notifable
		                        if (item.last_id_identifier == 'bot_chats') {
		                            if (!((itemList.msg_v && itemList.msg_v > _that.bot_st.msg_nm && _that.bot_st.bot_notifications == 1) || itemList.aalert)) {
                                        return;
                                    }
                                }

		                        // Don't show notification for transfered chats
		                        if (item.last_id_identifier == 'transfer_chat_dep') {
		                            return;
                                }

		                        var alertIcons = [];

		                        // Active chats notifications are done by appending alert icons
		                        if (item.last_id_identifier == 'active_chats') {
		                            if (itemList.aicons) {
                                        alertIcons = Object.keys(itemList.aicons);
                                        identifierElement += '_' + alertIcons.join('_');
                                    }
                                }

		                        currentStatusNotifications.push(identifierElement);

		                        if (typeof _that.statusNotifications[item.last_id_identifier] == 'undefined') {
		                        	_that.statusNotifications[item.last_id_identifier] = new Array();
		                        };

		                        if (_that.isListLoaded == true && item.last_id_identifier == 'subject_chats') {
                                    if (_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && lhinst.chatsSynchronising.indexOf(parseInt(itemList.id)) === -1) {
                                        chatsToNotify.push(itemList.id);
                                    }
                                } else if (_that.isListLoaded == true && item.last_id_identifier == 'active_chats') {
                                    if (_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && alertIcons.length > 0 && _that.notifIcons.length > 0) {
                                        var iconsMonitoring = _that.notifIcons.filter(function(n) {
                                            return _that.excludeIcons.indexOf(n) === -1 && alertIcons.indexOf(n) !== -1;
                                        })

                                        // Operator is monitoring this notification icon
                                        if (iconsMonitoring.length > 0) {
                                            chatsToNotify.push(itemList.id + '__' + iconsMonitoring.join('__'));
                                        }
                                    }
                                } else if (_that.isListLoaded == true && (chatsSkipped == 0 || itemList.status_sub_sub === 2) && ((_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && (userId == 0 || item.last_id_identifier == 'amails') && confLH.ownntfonly == 0) || (_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == confLH.user_id)) ) {
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
								_that.compareNotificationsAndHide(_that.statusNotifications[item.last_id_identifier],currentStatusNotifications,item.last_id_identifier);
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
						
				if (typeof data.ou !== 'undefined' && data.ou == 1) {
                    $('#lhc_op_operation').remove();
                    var th = document.getElementsByTagName('head')[0];
                    var s = document.createElement('script');
                    s.setAttribute('id','lhc_op_operation');
                    s.setAttribute('type','text/javascript');
                    s.setAttribute('src',WWW_DIR_JAVASCRIPT + 'chat/loadoperatorjs');
                    th.appendChild(s);
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
							lhinst.startChatBackground(item.id,tabs,LiveHelperChatFactory.truncate((item.nick || 'Visitor'),10),false);
                            _that.addAction({'type':'mac', 'chat_id': item.id, 'nick': item.nick});
						});
					}
				}

				_that.hideOnline = data.ho == 1;
				_that.hideInvisible = data.im == 1;
                _that.alwaysOnline = data.a_on == 1;
                _that.inActive = data.ina == 1;

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
                _that.lhcListRequestInProgress = false;

				$scope.timeoutControl = setTimeout(function(){
					$scope.loadChatList();
				},confLH.back_office_sinterval);
		});
	};

    this.versionChanged = function() {
		var _that = this;
        $interval(function() {
            _that.lhcVersionCounter = _that.lhcVersionCounter - 1;
            if (_that.lhcVersionCounter == 0) {
                document.location.reload(true);
            }
        }, 1000);
	};

	this.compareNotificationsAndHide = function(oldStatus, newStatus, type) {
		if (typeof oldStatus !== 'undefined') {			
			for (var i = oldStatus.length - 1; i >= 0; i--) {
			  var key = oldStatus[i];
			  if (-1 === newStatus.indexOf(key)) {				
				  lhinst.hideNotification(key.split('_')[0], type);
			  }
			}
		}
	};
	
	this.appendActiveChats = function(){
		LiveHelperChatFactory.loadActiveChats().then(function(data) {
			
			var tabs = $('#tabs');
			angular.forEach(data.result, function(item, key) {
				lhinst.startChatBackground(item.id, tabs, LiveHelperChatFactory.truncate((item.nick || 'Visitor'),10));
			});
			
			setTimeout(function(){
				lhinst.syncadmininterfacestatic();
     	    },1000);
		});
	};

	this.previewMail = function(chat_id){
        lhc.previewMail(chat_id);
    };

	this.previewChat = function(chat_id,event){
        if (event) {
            event.stopPropagation();
        }
		lhc.previewChat(chat_id);
	};

	this.previewChatArchive = function(archive_id, chat_id, event){
        if (event) {
            event.stopPropagation();
        }
		lhc.previewChatArchive(archive_id, chat_id);
	};
	this.emitEvent = function(event, args) {
        var _that = this;
        ee.emitEvent(event, [_that, args]);
    }
	this.redirectContact = function(chat_id,message,event) {
        if (event) {
            event.stopPropagation();
        }
		return lhinst.redirectContact(chat_id,message);
	};
	
	this.startChatNewWindow = function(chat_id,name,event) {
	    if (event) {
	        event.stopPropagation();
        }

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

	this.startMailChat = function (chat_id, name) {
        if ($('#tabs').length > 0) {
            return lhinst.startMailChat(chat_id,$('#tabs'),LiveHelperChatFactory.truncate(name || 'Mail',10));
        }
    }

    this.startChatByID = function(chat_id, background) {
	    if (!isNaN(chat_id)) {
            if ($('#tabs').length > 0) {
                $('#menu-chat-options').dropdown('toggle');
                var _that = this;
                LiveHelperChatFactory.getChatData(chat_id).then(function(data) {
                    if (data.r) {
                        document.location = WWW_DIR_JAVASCRIPT + data.r;
                        return;
                    }
                    lhinst.addOpenTrace('start_chat_by_id');
                    if (!background) {
                        _that.startChat(parseInt(chat_id),LiveHelperChatFactory.truncate((data.nick || 'Visitor'),10));
                    } else {
                        lhinst.startChatBackground(parseInt(chat_id), $('#tabs'), LiveHelperChatFactory.truncate((data.nick || 'Visitor'),10),'backgroundid');
                    }
                });
            }
        }
    }

	this.startChat = function (chat_id,name) {
		if ($('#tabs').length > 0){
            lhinst.addOpenTrace('click');
			return lhinst.startChat(chat_id,$('#tabs'),LiveHelperChatFactory.truncate((name || 'Visitor'),10));
		} else {
			lhinst.startChatNewWindow(chat_id,name);	
		}
	};
	
	this.startChatNewWindowTransfer = function(chat_id,name,transfer_id, transfer_scope) {
		return lhinst.startChatNewWindowTransfer(chat_id,name,transfer_id, transfer_scope);
	};
		
	this.startChatTransfer = function(chat_id,name,transfer_id, transfer_scope) {
		return lhinst.startChatTransfer(chat_id,$('#tabs'),name,transfer_id, transfer_scope);
	};

	$scope.startChatOperatorPublic = function(user_id){
        _that.startChatOperator(user_id);
    }

    this.startChatOperator = function(user_id) {
		LiveHelperChatFactory.getActiveOperatorChat(user_id).then(function(data) {
		    lhinst.startGroupChat(data.id,$('#tabs'),LiveHelperChatFactory.truncate(data.name,10));
		});
	};

	this.openModal = function(url, event) {
        if (event) {
            event.stopPropagation();
        }
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
                this.lastd_activity = Math.round(new Date().getTime()/1000);

                clearTimeout(this.timeoutActivity);
                var _that = this;

                this.timeoutActivity = setTimeout(function(){

                    LiveHelperChatFactory.setInactive('true').then(function (data) {

                        // Operator is active in another tab/window
                        if (data.active == true) {
                            _that.resetTimeoutActivity();
                            _that.lastd_activity = 0;
                            return ;
                        }

                        _that.blockSync = true;
                        lhinst.disableSync = true;
                        _that.lhcListRequestInProgress = false; // Request can be send either way

                        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'user/wentinactive/false','backdrop': true, hidecallback: function() {
                            _that.setActiveInterface();
                            _that.channel.postMessage({'action':'went_active','args':{}});
                        }});

                    });

                }, _that.timeoutActivityTime*1000);
            }
        }
    };

    this.setActiveInterface = function() {
        LiveHelperChatFactory.setInactive('false');

        _that.isListLoaded = false; // Because inactive visitor can be for some quite time, make sure new chat's does not trigger flood of sound notifications
        _that.blockSync = false;	// Unblock sync
        _that.lhcListRequestInProgress = false; // Request can be send either way
        _that.resetTimeoutActivity(); // Start monitoring activity again
        lhinst.disableSync = false;

        $scope.loadChatList();
        _that.inActive = false;
    }

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

        angular.forEach(_that.userGroups, function(value) {
            userGroups.push(value.id);
        });

        angular.forEach(_that.userDepartmentsGroups, function(value) {
            userDepartmentsGroups.push(value.id);
        });

        angular.forEach(_that.userProductNames, function(value) {
            userProductNames.push(value.id);
        });

        LiveHelperChatFactory.searchProvider('users_ids',_that.pendingu.join(',') +','+ _that.activeu.join(',')+','+ _that.subjectu.join(',')+','+ _that.pendingmu.join(',')+','+ _that.activemu.join(',')+','+ _that.alarmmu.join(',')+','+ _that.oopu.join(',')).then(function(data){
            _that.userList = data.items;

            angular.forEach(_that.userList, function(value) {
                userList.push(value.id);
            });

            var verifyCombinations = {
                'activeu' : userList,
                'actived_products' : userProductNames,
                'actived_ugroups' : userGroups,
                'actived_dpgroups' : userDepartmentsGroups,

                'pendingu' : userList,
                'oopu' : userList,
                'pendingd_ugroups' : userGroups,
                'pendingd_dpgroups' : userDepartmentsGroups,
                'pendingd_products' : userProductNames,

                'botd_dpgroups' : userDepartmentsGroups,
                'botd_products' : userProductNames,

                'departmentd_dpgroups' : userDepartmentsGroups,

                'closedd_products' : userProductNames,
                'closedd_dpgroups' : userDepartmentsGroups,

                'unreadd_dpgroups' : userDepartmentsGroups,
                'unreadd_products' : userProductNames,

                'mcd_products' : userProductNames,
                'mcd_dpgroups' : userDepartmentsGroups,
                'mmd_dpgroups' : userDepartmentsGroups,
                'operatord_dpgroups' : userDepartmentsGroups,
                'operatord_ugroups' : userGroups
            };

            angular.forEach(verifyCombinations, function(list, index) {
                var originalList = [..._that[index]];
                angular.forEach(originalList, function(value) {
                    if (list.indexOf(value) === -1) {
                        _that[index].splice(_that[index].indexOf(value),1);
                        _that.productChanged(index);
                    };
                });
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

    $scope.$watch('lhc.userFilterText', function(newVal,oldVal) {
        if (newVal != oldVal) {
            LiveHelperChatFactory.searchProvider('users',newVal).then(function(data){
                _that.userList = data.items;
            });
        };
    });

    $scope.$watch('lhc.depFilterText', function(newVal,oldVal) {
        if (newVal != oldVal) {
            LiveHelperChatFactory.searchProvider('depswidget',newVal).then(function(data){
                _that.userDepartments = data.items;
                _that.userDepartmentsNames=data.items_names;
            });
        };
    });

	// Bootstraps initial attributes
	this.initLHCData = function() {

		var appendURL = '';
		var openedChats = this.getOpenedChatIds('achat_id');
		var openedgChats = this.getOpenedChatIds('gachat_id');
		var openedmChats = this.getOpenedChatIds('machat_id');

        var mail_id = chat_id = 0;
        var hash = window.location.hash;
        if (hash != '') {
            var matchData = hash.match(/id-\d+$/);
            if (matchData !== null && matchData[0]) {
                chat_id = parseInt(matchData[0].replace('id-',''));
                if (openedChats.indexOf(chat_id) === -1){
                    openedChats.push(chat_id);
                }
            }

            // Support mail chats hash in URL
            if (matchData == null) {
                var matchData = hash.match(/mc\d+$/);
                if (matchData !== null && matchData[0]) {
                    mail_id = parseInt(matchData[0].replace('mc',''));
                    if (openedmChats.indexOf(mail_id) === -1){
                        openedmChats.push(mail_id);
                    }
                }
            }
        }

        var elm = document.getElementById('load_chat_id');

        if (elm && openedChats.indexOf(elm.value) === -1) {
            chat_id = elm.value;
            openedChats.push(elm.value);
            window.location.hash = '#!#chat-id-'+elm.value;
        }

        var elm = document.getElementById('load_mail_id');

        if (elm && openedmChats.indexOf(elm.value) === -1) {
            mail_id = elm.value;
            openedmChats.push(elm.value);
            window.location.hash = '#!#chat-id-mc'+elm.value;
        }

		if ($('#tabs').length > 0 && lhinst.disableremember == false && openedChats.length > 0) {
            appendURL = '/(chatopen)/' + openedChats.join('/');
		}

		if ($('#tabs').length > 0 && lhinst.disableremember == false && openedgChats.length > 0) {
            appendURL += '/(chatgopen)/' + openedgChats.join('/');
		}

		if ($('#tabs').length > 0 && lhinst.disableremember == false && openedmChats.length > 0) {
            appendURL += '/(chatmopen)/' + openedmChats.join('/');
		}

		LiveHelperChatFactory.loadInitialData(appendURL).then(function(data) {

            if (data.logout || data.error_url) {
                document.location.reload();
                return;
            }

			_that.userDepartmentsNames=data.dp_names;
			_that.userDepartments=data.dep_list;
			_that.userProductNames=data.pr_names;
			_that.userDepartmentsGroups=data.dp_groups;
			_that.userGroups = data.user_groups;
            _that.hideInvisible = data.im;
            _that.hideOnline = data.ho;
            _that.lhcVersion = data.v;
            _that.alwaysOnline = data.a_on;
            _that.additionalColumns = data.col;
            _that.widgetsActive = data.widgets;
            _that.bot_st = data.bot_st;
            _that.excludeIcons = data.exc_ic;
            _that.notifIcons = data.not_ic;

            var arraySettings = [
                'subjectd',
                'subjectd_products',
                'subjectd_dpgroups',
                'subjectd_ugroups',

                'activeu',
                'pendingu',
                'oopu',
                'subjectu',

                'closedd',
                'closedd_products',
                'closedd_dpgroups',

                'botd',
                'botd_products',
                'botd_dpgroups',
                'botd_ugroups',

                'pendingd',
                'pendingd_products',
                'pendingd_dpgroups',
                'pendingd_ugroups',

                'unreadd',
                'unreadd_products',
                'unreadd_dpgroups',

                'mcd',
                'mcd_products',
                'mcd_dpgroups',

                'actived',
                'actived_products',
                'actived_dpgroups',
                'actived_ugroups',

                'departmentd_dpgroups',
                'departmentd',

                'operatord_dpgroups',
                'operatord_ugroups',
                'operatord',

                'mmd',
                'mmd_dpgroups',

                'alarmmd',
                'alarmmd_products',
                'alarmmd_dpgroups',
                'alarmmd_ugroups',

                'activemd',
                'activemd_products',
                'activemd_dpgroups',
                'activemd_ugroups',

                'pendingmd',
                'pendingmd_products',
                'pendingmd_dpgroups',
                'pendingmd_ugroups',

                'pendingmu',
                'activemu',
                'alarmmu',
            ];

            var limitOptions = [
                'limitb',
                'limita',
                'limitu',
                'limitp',
                'limito',
                'limitc',
                'limitd',
                'limitmc',
                'limitgc',
                'limits',
                'limitpm',
                'limitam',
                'limitalm',
                'limitmm',
            ];

            data.dw_filters && Object.keys(data.dw_filters).forEach(key => {
                if (arraySettings.indexOf(key) !== -1) {
                    _that[key] = _that.restoreSettingByString(data.dw_filters[key], true);
                } else if (limitOptions.indexOf(key) !== -1) {
                    _that[key] = data.dw_filters[key];
                }
            })

			angular.forEach(_that.widgetsItems, function(listId) {
				_that.setDepartmentNames(listId);
			});

			if (data.track_activity == true)
			{			
				_that.timeoutActivityTime = data.timeout_activity;
				_that.setupActivityMonitoring();
			}

            angular.forEach(data.copen, function(chatOpen) {
                lhinst.addOpenTrace('opened_chats');
                lhinst.startChat(chatOpen.id,$('#tabs'),LiveHelperChatFactory.truncate((chatOpen.nick || 'Visitor'),10), (chatOpen.id == chat_id), 0, chatOpen.status);
                _that.addAction({'type':'mac_history', 'chat_id': chatOpen.id, 'nick': chatOpen.nick});
                if (chatOpen.id == chat_id) {
                    document.getElementById('tabs').classList.add('chat-tab-selected');
                }
            });

            angular.forEach(data.cgopen, function(chatOpen) {
                lhinst.startGroupChat(chatOpen.id,$('#tabs'),LiveHelperChatFactory.truncate((chatOpen.nick || 'Visitor'),10), true);
            });

            angular.forEach(data.cmopen, function(chatOpen) {
                lhinst.startMailChat(chatOpen.id,$('#tabs'),LiveHelperChatFactory.truncate(chatOpen.subject || 'Mail',10), !(chatOpen.id == mail_id));
            });

            angular.forEach(data.cdel, function(chatOpen) {
                lhinst.forgetChat(chatOpen,'achat_id');
            });

            angular.forEach(data.cgdel, function(chatOpen) {
                lhinst.forgetChat(chatOpen,'gachat_id');
            });

            angular.forEach(data.cmdel, function(chatOpen) {
                lhinst.forgetChat(chatOpen,'machat_id');
            });

            ee.emitEvent('eventLoadInitialData', [data, $scope, _that]);

            $_LHC_API['initial_data'] = data;

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

} catch (e) {
    if (lhcError) lhcError.log(e.message, "angular.lhc.js", e.lineNumber || e.line, e.stack); else throw Error("lhc : " + e.message);
}
