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
			 deferred.resolve(data);			 
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
	$scope.active_chats = {};
	$scope.closed_chats = {};
	$scope.unread_chats = {};
	$scope.transfer_dep_chats = {};
	$scope.transfer_chats = {};
	$scope.timeoutControl = null;
	$scope.setTimeoutEnabled = true;
	
	
	$scope.loadChatList = function() {
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
		
				/*setTimeout(function(){
					$(document).foundation('section', 'resize');
				},500);*/	
				
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
	
	/*this.initializeModal = function() {
		if ($('#myModal').size() == 0) {
			$('body').prepend('<div id="myModal" class="reveal-modal medium"><a class="close-reveal-modal">&#215;</a></div>');
			$("#myModal").on("opened", function(){
				$(document).foundation('section', 'reflow')					
			});
		};	
	};*/
	
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
		return lhinst.startChat(chat_id,$('#tabs'),LiveHelperChatFactory.truncate(name,10));				
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