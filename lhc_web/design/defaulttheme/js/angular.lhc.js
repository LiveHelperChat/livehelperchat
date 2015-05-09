var phonecatApp = angular.module('lhcApp', [
  'lhcAppServices',
  'lhcAppControllers'
]);

var services = angular.module('lhcAppServices', []);
var lhcAppControllers = angular.module('lhcAppControllers', []);

angular.element(document).ready(function(){
    var element = angular.element(document.querySelector("form"));
    element.triggerHandler("$destroy");
    //‌​
});


services.factory('LiveHelperChatFactory', ['$http','$q',function ($http, $q) {
	
	this.loadChatList = function(params){
		var deferred = $q.defer();		
		$http.get(WWW_DIR_JAVASCRIPT + 'chat/syncadmininterface').success(function(data) {
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
	
	this.toggleList = function(variable) {
		$scope[variable] = !$scope[variable];		
		if (localStorage) {
    		try {
    			localStorage.setItem(variable,$scope[variable]);
    		} catch(err) {    			   		
    		};
    	}		
	};
	
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
		LiveHelperChatFactory.loadChatList().then(function(data){	
						
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