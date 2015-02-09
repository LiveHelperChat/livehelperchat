var revealM = {
		cancelcolorbox : function() {
			$('#myModal').foundation('reveal', 'close');
		},
		
		initializeModal : function(selector) {		
			var modelSelector = selector != undefined ? selector : 'myModal';
			if ($('#'+modelSelector).size() == 0) {
				$('body').prepend('<div id="'+modelSelector+'" class="reveal-modal large"><a class="close-reveal-modal">&#215;</a></div>');
				$("#"+modelSelector).on("opened", function(){
					$(document).foundation('section', 'reflow')					
				});
			};	
		},
		
		revealModal : function(url,closePrevious) {
			if (closePrevious !== undefined && closePrevious === true){
				$('#myModal').remove(); 	
				$('.reveal-modal-bg').remove();	
			}
			
			revealM.initializeModal('myModal');			
			$('#myModal').foundation('reveal', 'open',{'url':url}); 		
		},
		
		revealIframe : function(url,height) {
			revealM.initializeModal();
			$('#myModal').html('<a class="close-reveal-modal">&#215;</a><iframe src="'+url+'" frameborder="0" style="width:100%" height="'+height+'" />');
			$('#myModal').foundation('reveal', 'open'); 			
		}
};

module.exports = revealM;