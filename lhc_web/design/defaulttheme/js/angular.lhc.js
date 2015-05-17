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
	
	// Just for extension reserved keywords
	$scope.custom_list_1_expanded = true;
	$scope.custom_list_2_expanded = true;
	$scope.custom_list_3_expanded = true;
	$scope.custom_list_4_expanded = true;
	
	// Parameters for back office sync
	
	var _that = this;
	
	this.restoreLocalSetting = function(variable,defaultValue,split) {
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
	};
			
	// Active chat limit
	this.limita = this.restoreLocalSetting('limita',10,false);
	this.limitu = this.restoreLocalSetting('limitu',10,false);
	this.limitp = this.restoreLocalSetting('limitp',10,false);
	this.limito = this.restoreLocalSetting('limito',10,false);
	this.limitc = this.restoreLocalSetting('limitc',10,false);
	this.limitd = this.restoreLocalSetting('limitd',10,false);
	
	this.actived = this.restoreLocalSetting('actived',[],true);
	this.activedNames = [];	
	
	this.departmentd = this.restoreLocalSetting('departmentd',[],true);
	this.departmentdNames = [];	
		
	this.unreadd = this.restoreLocalSetting('unreadd',[],true);
	this.unreaddNames = [];
	
	this.pendingd = this.restoreLocalSetting('pendingd',[],true);
	this.pendingdNames = [];
	
	this.operatord = this.restoreLocalSetting('operatord',[],true);
	this.operatordNames = [];
	
	this.closedd = this.restoreLocalSetting('closedd',[],true);
	this.closeddNames = [];
	
	this.storeLocalSetting = function(variable, value) {
		if (localStorage) {
			var value = localStorage.setItem(variable, value);			
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
	
	$scope.getSyncFilter = function()
	{
		var filter = '/(limita)/'+parseInt(_that.limita);
		filter += '/(limitu)/'+parseInt(_that.limitu);
		filter += '/(limitp)/'+parseInt(_that.limitp);
		filter += '/(limito)/'+parseInt(_that.limito);
		filter += '/(limitc)/'+parseInt(_that.limitc);
		filter += '/(limitd)/'+parseInt(_that.limitd);
				
		if (typeof _that.actived == 'object' && _that.actived.length > 0) {			
			filter += '/(actived)/'+_that.actived.join('/');
		}
		
		if (typeof _that.unreadd == 'object' && _that.unreadd.length > 0) {	
			filter += '/(unreadd)/'+_that.unreadd.join('/');
		}
		
		if (typeof _that.pendingd == 'object' && _that.pendingd.length > 0) {	
			filter += '/(pendingd)/'+_that.pendingd.join('/');
		}
		
		if (typeof _that.operatord == 'object' && _that.operatord.length > 0) {	
			filter += '/(operatord)/'+_that.operatord.join('/');
		}
		
		if (typeof _that.closedd == 'object' && _that.closedd.length > 0) {	
			filter += '/(closedd)/'+_that.closedd.join('/');
		}
		
		if (typeof _that.departmentd == 'object' && _that.departmentd.length > 0) {	
			filter += '/(departmentd)/'+_that.departmentd.join('/');
		}
				
		return filter;
	}
	
	$scope.$watch('lhc.limita', function(newVal,oldVal) {       
		if (newVal != oldVal) {							
			_that.storeLocalSetting('limita',newVal);
			$scope.loadChatList();
		};
	});
	
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
		if (_that[listId].length > 0){
			var listValue = _that[listId].join("/");
			if (listValue != ''){
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
		
		$scope.loadChatList();
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
						
				var hasPendingItems = false;
				var lastNotifiedId = 0;
				
				angular.forEach(data.result, function(item, key){					
					$scope[key] = item;					
					if ( item.last_id_identifier ) {
	                    if (lhinst.trackLastIDS[item.last_id_identifier] == undefined ) {
	                    	lhinst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);
	                    } else if (lhinst.trackLastIDS[item.last_id_identifier] < parseInt(item.last_id)) {
	                    	lhinst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);
	                    	if (lastNotifiedId != lhinst.trackLastIDS[item.last_id_identifier]) {
	                    		lastNotifiedId = lhinst.trackLastIDS[item.last_id_identifier];
	                    		lhinst.playSoundNewAction(item.last_id_identifier,parseInt(item.last_id),(item.nick ? item.nick : 'Live Help'),(item.msg ? item.msg : confLH.transLation.new_chat));
	                    	}
	                    } else if (lhinst.trackLastIDS[item.last_id_identifier] > parseInt(item.last_id)) {
	                    	lhinst.trackLastIDS[item.last_id_identifier] = parseInt(item.last_id);
	                    };
	                    
	                    if (item.last_id == 0) {
	                    	lhinst.trackLastIDS[item.last_id_identifier] = 0;
	                    };
	                    
	                    if (parseInt(item.last_id) > 0) {
	                    	hasPendingItems = true;                        	
	                    };	                   
	                };
				});
				
				if (hasPendingItems == false) {
					lhinst.hideNotifications();
                };
                
                if ($scope.pending_chats.length == 0) {
                	clearTimeout(lhinst.soundIsPlaying);
				};
		
				if ($scope.setTimeoutEnabled == true) {
					$scope.timeoutControl = setTimeout(function(){
						$scope.loadChatList();
					},confLH.back_office_sinterval);
				};
				
		},function(error){
				$scope.timeoutControl = setTimeout(function(){
					$scope.loadChatList();
				},confLH.back_office_sinterval);
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
	
	$scope.loadChatList();
}]);