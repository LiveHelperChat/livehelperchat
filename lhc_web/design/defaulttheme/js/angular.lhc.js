$( document ).ready(function() {
	var hash = window.location.hash;	
	if (hash != '') {
		$('ul[role="tablist"] a[href="' + hash.replace("#/","#") + '"]').tab('show');
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
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/syncadmininterface' + filter).success(function(data) {
			 if (typeof data.error_url !== 'undefined') {
				 document.location = data.error_url;
			 } else {
				 deferred.resolve(data);
			 }			 
		}).error(function(){
			deferred.reject('error');
		});		
		return deferred.promise;
	};

	this.loadInitialData = function() {
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/loadinitialdata').success(function(data) {
			 if (typeof data.error_url !== 'undefined') {
				 document.location = data.error_url;
			 } else {
				 deferred.resolve(data);
			 }			 
		}).error(function(){
			deferred.reject('error');
		});		
		return deferred.promise;
	};

	this.loadActiveChats = function() {
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/loadactivechats').success(function(data) {
			 if (typeof data.error_url !== 'undefined') {
				 document.location = data.error_url;
			 } else {
				 deferred.resolve(data);
			 }			 
		}).error(function(){
			deferred.reject('error');
		});		
		return deferred.promise;
	};
	
	this.getNotificationsData = function(id) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'chat/getnotificationsdata/(id)/' + id).success(function(data) {
            if (typeof data.error_url !== 'undefined') {
                document.location = data.error_url;
            } else {
                deferred.resolve(data);
            }
        }).error(function(){
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
		
	// Just for extension reserved keywords
	$scope.custom_list_1_expanded = true;
	$scope.custom_list_2_expanded = true;
	$scope.custom_list_3_expanded = true;
	$scope.custom_list_4_expanded = true;
	
	// Parameters for back office sync
	
	var _that = this;
	
	this.restoreLocalSetting = function(variable,defaultValue,split) {
		try {
			if (localStorage) {
				var value = localStorage.getItem(variable);
				if (value !== null){
					if (split == true){
						return value.split('/');
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
			
	// Active chat limit
	this.limita = this.restoreLocalSetting('limita',10,false);
	this.limitu = this.restoreLocalSetting('limitu',10,false);
	this.limitp = this.restoreLocalSetting('limitp',10,false);
	this.limito = this.restoreLocalSetting('limito',10,false);
	this.limitc = this.restoreLocalSetting('limitc',10,false);
	this.limitd = this.restoreLocalSetting('limitd',10,false);
	
	// Active chat's operators filter
	this.activeu = this.restoreLocalSetting('activeu',0,false);
	this.pendingu = this.restoreLocalSetting('pendingu',0,false);
	
	// Main left menu of pagelayout
	$scope.lmtoggle = this.restoreLocalSetting('lmtoggle','false',false) != 'false';
	
	// Stores last ID of unread/pending chat id
	this.lastidEvent = 0;
	
	// User departments
	this.userDepartments = [];
	this.userProductNames = [];
	
	this.departmentd = this.restoreLocalSetting('departmentd',[],true);
	this.departmentdNames = [];	
	
	this.operatord = this.restoreLocalSetting('operatord',[],true);
	this.operatordNames = [];

	// Chats with products filters
	this.actived = this.restoreLocalSetting('actived',[],true);
	this.actived_products = this.restoreLocalSetting('actived_products',[],true);
	this.activedNames = [];

	this.unreadd = this.restoreLocalSetting('unreadd',[],true);
	this.unreadd_products = this.restoreLocalSetting('unreadd_products',[],true);
	this.unreaddNames = [];

	this.pendingd = this.restoreLocalSetting('pendingd',[],true);
	this.pendingd_products = this.restoreLocalSetting('pendingd_products',[],true);
	this.pendingdNames = [];

	this.closedd = this.restoreLocalSetting('closedd',[],true);
	this.closedd_products = this.restoreLocalSetting('closedd_products',[],true);
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
	
	this.toggleWidget = function(variable,forceReload) {
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
	
	this.getToggleWidget = function(variable) {
		this.toggleWidgetData[variable] = this.restoreLocalSetting(variable,'false',false) == 'false' ? false : true;
	};
	
	$scope.getSyncFilter = function()
	{
		var filter = '/(limita)/'+parseInt(_that.limita);
		filter += '/(limitu)/'+parseInt(_that.limitu);
		filter += '/(limitp)/'+parseInt(_that.limitp);
		filter += '/(limito)/'+parseInt(_that.limito);
		filter += '/(limitc)/'+parseInt(_that.limitc);
		filter += '/(limitd)/'+parseInt(_that.limitd);
		
		if (parseInt(_that.activeu) > 0) {
			filter += '/(activeu)/'+_that.activeu;
		}
		
		if (parseInt(_that.pendingu) > 0) {
			filter += '/(pendingu)/'+_that.pendingu;
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
	
	this.setUpListNames = function(lists) {				
		angular.forEach(lists, function(listId) {
			_that[listId + 'Names'] = [];
			angular.forEach(_that[listId], function(value) {
				 _that[listId + 'Names'].push(_that.userDepartmentsNames[value]);
			});
		});		
	};
	
	this.setDepartmentNames = function(listId) {
		_that[listId + 'Names'] = [];			
		angular.forEach(_that[listId], function(value) {
			 _that[listId + 'Names'].push(_that.userDepartmentsNames[value]);
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
	
	$scope.loadChatList = function() {
		
		if (localStorage) {
			try {
				$scope.pending_chats_expanded = localStorage.getItem('pending_chats_expanded') != 'false';
				$scope.active_chats_expanded = localStorage.getItem('active_chats_expanded') != 'false';
				$scope.my_active_chats_expanded = localStorage.getItem('my_active_chats_expanded') != 'false';
				$scope.closed_chats_expanded = localStorage.getItem('closed_chats_expanded') != 'false';
				$scope.unread_chats_expanded = localStorage.getItem('unread_chats_expanded') != 'false';
				
				// Just for extension reserved keywords
				$scope.custom_list_1_expanded = localStorage.getItem('custom_list_1_expanded') != 'false';
				$scope.custom_list_2_expanded = localStorage.getItem('custom_list_2_expanded') != 'false';
				$scope.custom_list_3_expanded = localStorage.getItem('custom_list_3_expanded') != 'false';
				$scope.custom_list_4_expanded = localStorage.getItem('custom_list_4_expanded') != 'false';
			} catch(err) { 
				
			};
		}
				
		clearTimeout($scope.timeoutControl);
		LiveHelperChatFactory.loadChatList($scope.getSyncFilter()).then(function(data){	
																	
				ee.emitEvent('eventLoadChatList', [data, $scope, _that]);
				
				if (typeof data.items_processed == 'undefined') {
						              
	                var currentStatusNotifications = []; // Holds current status of chat's list,  _that.statusNotifications previous status
	                
	                var chatsToNotify = []; // Holds chat's to notify about for particular last_id_identifier item
	                
	                var notificationsData = []; // Holds chat's to notify for all lists

					angular.forEach(data.result, function(item, key) {

						$scope[key] = item;					
						
						if ( item.last_id_identifier ) {							
		                    chatsToNotify = [];		                     
		                    												
							currentStatusNotifications = [];
							
							var chatsSkipped = 0; // Do not show notification for chats if they appear at the bottom, only applies to unassigned chats
																					
							angular.forEach(item.list, function(itemList, keyItem) {
	
		                        var userId = (typeof itemList.user_id !== 'undefined' ? itemList.user_id : 0);
		                       		                        
		                        var identifierElement = itemList.id + '_' + userId;
		                        		
		                        currentStatusNotifications.push(identifierElement);
		                  	
		                        if (typeof _that.statusNotifications[item.last_id_identifier] == 'undefined') {
		                        	_that.statusNotifications[item.last_id_identifier] = new Array();
		                        };
		                        
		                        if (_that.isListLoaded == true && chatsSkipped == 0 && ((_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == 0) || (_that.statusNotifications[item.last_id_identifier].indexOf(identifierElement) == -1 && userId == confLH.user_id)) ) {
		                        	chatsToNotify.push(itemList.id);	
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
					
					angular.forEach(data.mac, function(item, key) {
						lhinst.startChatBackground(item.id,tabs,LiveHelperChatFactory.truncate(item.nick,10),false);
					});
				}
				
				if ($scope.setTimeoutEnabled == true) {
					$scope.timeoutControl = setTimeout(function(){
						$scope.loadChatList();
					},confLH.back_office_sinterval);
				};
				
				_that.isListLoaded = true;
				
		},function(error){
			console.log(error);
				$scope.timeoutControl = setTimeout(function(){
					$scope.loadChatList();
				},confLH.back_office_sinterval);
		});
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
	
	this.redirectContact = function(chat_id,message) {	
		return lhinst.redirectContact(chat_id,message);				
	};
	
	this.startChatNewWindow = function(chat_id,name) {
		return lhinst.startChatNewWindow(chat_id,name);	
	};
		
	this.deleteChat = function(chat_id, tabs, hidetab) {
		return lhinst.deleteChat(chat_id, tabs, hidetab);
	};
	
	this.startChat = function (chat_id,name) {	
		if ($('#tabs').size() > 0){
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
	
	this.startChatOperator = function(user_id) {	
		window.open(WWW_DIR_JAVASCRIPT + 'chat/startchatwithoperator/'+user_id,'operatorchatwindow-'+user_id,'menubar=1,resizable=1,width=780,height=450');
	};
	
	// Bootstraps initial attributes
	this.initLHCData = function() {
		LiveHelperChatFactory.loadInitialData().then(function(data) {	
			_that.userDepartmentsNames=data.dp_names;
			_that.userDepartments=data.dep_list;
			_that.userProductNames=data.pr_names;

			angular.forEach(_that.widgetsItems, function(listId) {
				_that.setDepartmentNames(listId);
			});

			$scope.loadChatList();
		})
	}	
	
	this.initLHCData();
	
}]);

