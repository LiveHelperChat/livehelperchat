var revealM = {
		cancelcolorbox : function() {
			$('#myModal').foundation('reveal', 'close');
		},

		initializeModal : function(selector) {
			var modelSelector = selector != undefined ? selector : 'myModal';
			if ($('#'+modelSelector).length == 0) {

				var prependTo = null;
				if ($('#widget-layout').length == 0) {
					prependTo = $('body');
				} else {
					prependTo = $('#widget-layout');
				};
				prependTo.prepend('<div id="'+modelSelector+'" class="modal bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"></div>');
			};
		},

        hideCallback : false,

        modalInstance : null,

        previousHideListener : null,
    
        previousShowListener : null,

		revealModal : function(params) {

			if (revealM.modalInstance) {
                revealM.modalInstance.hide();
			}

            if (typeof params['hidecallback'] !== 'undefined') {
                revealM.hideCallback = true;
            } else {
                revealM.hideCallback = false;
            }

			revealM.initializeModal('myModal');

            var mparams = {'show':true, 'focus': !($('#admin-body').length > 0), 'backdrop': (!($('#admin-body').length > 0) || (typeof params.backdrop !== 'undefined' && params.backdrop == true)) };

			if (typeof params['iframe'] === 'undefined') {

				if (typeof params['loadmethod'] !== 'undefined' && params['loadmethod'] == 'post')
				{
					jQuery.post(params['url'], params['datapost'], function(data) {
                        if (data != "") {
                            $('#myModal').html(data);
                            revealM.modalInstance = new bootstrap.Modal('#myModal', mparams);
                            revealM.setShowHideCallbacks(params);
                            revealM.modalInstance.show();
                            revealM.setCenteredDraggable();
                        } else if (typeof params['on_empty'] !== 'undefined') {
                            params['on_empty']();
                        } else {
                            alert('Empty content was returned!');
                        }
					}).fail(function(jqXHR, textStatus, errorThrown) {
                        alert('There was an error processing your request: ' + '[' + jqXHR.status + '] [' + jqXHR.statusText + '] [' + jqXHR.responseText + '] ' + errorThrown);
                    })
				} else {
					jQuery.get(params['url'], function(data){
                        if (data != "") {
                            $('#myModal').html(data);//.modal(mparams).show();
                            revealM.modalInstance = new bootstrap.Modal('#myModal', mparams);
                            revealM.setShowHideCallbacks(params);
                            revealM.modalInstance.show();
                            revealM.setCenteredDraggable();
                        } else if (typeof params['on_mepty'] !== 'undefined') {
                            params['on_mepty']();
                        } else {
                            alert('Empty content was returned!');
                        }
					}).fail(function(jqXHR, textStatus, errorThrown) {
                        alert('There was an error processing your request: ' + '[' + jqXHR.status + '] [' + jqXHR.statusText + '] [' + jqXHR.responseText + '] ' + errorThrown);
                    });
				}
			} else {
				var header = '';
				var prependeBody = '';
				if (typeof params['hideheader'] === 'undefined') {
					header = '<div class="modal-header"><h4 class="modal-title" id="myModalLabel"><span class="material-icons">info</span>'+(typeof params['title'] === 'undefined' ? '' : params['title'])+'</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>';
				} else {
					prependeBody = (typeof params['title'] === 'undefined' ? '' : '<b>'+params['title']+'</b>') + '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
				}
				var additionalModalBody = typeof params['modalbodyclass'] === 'undefined' ? '' : ' '+params['modalbodyclass'];

				$('#myModal').html('<div class="modal-dialog modal-dialog-scrollable modal-xl"><div class="modal-content">'+header+'<div class="modal-body'+additionalModalBody+'">'+prependeBody+'<iframe src="'+params['url']+'" frameborder="0" style="width:100%" height="'+params['height']+'" /></div></div></div>');
                revealM.modalInstance = new bootstrap.Modal('#myModal', mparams);
                revealM.setShowHideCallbacks(params);
                revealM.modalInstance.show();

                revealM.setCenteredDraggable();
				
			}
		},

        setShowHideCallbacks : function(params) {
            // Remove old listeners
            if (revealM.previousHideListener && document.getElementById('myModal')) {
                document.getElementById('myModal').removeEventListener('hide.bs.modal', revealM.previousHideListener);
                revealM.previousHideListener = null;
            }

            if (revealM.previousShowListener && document.getElementById('myModal')) {
                document.getElementById('myModal').removeEventListener('show.bs.modal', revealM.previousShowListener);
                revealM.previousShowListener = null;
            }

            // Attach new listeners
            if (typeof params['showcallback'] !== 'undefined' && document.getElementById('myModal')) {
                document.getElementById('myModal').addEventListener('show.bs.modal', params['showcallback']);
                revealM.previousShowListener = params['showcallback'];
            }

            if (typeof params['hidecallback'] !== 'undefined' && document.getElementById('myModal')) {
                revealM.previousHideListener = params['hidecallback'];
                document.getElementById('myModal').addEventListener('hide.bs.modal', params['hidecallback']);
            }
        },

        setCenteredDraggable : function(){
            if ($('#admin-body').length > 0 && !$('html').attr('data-mobile')) {
                var modalContent = $('#myModal .modal-dialog');

                var prevPos = revealM.rememberPositions();
                var positions = revealM.getPositions();

                if (prevPos === null || parseInt(prevPos[1]) > positions.width || parseInt(prevPos[0]) > positions.height || parseInt(prevPos[0]) < 0 || (modalContent.width() + parseInt(prevPos[1])) < 0 ) {
                    prevPos = [((positions.height - modalContent.height()) / 2),((positions.width - modalContent.width()) / 2)];
                }

                modalContent.draggabilly({
                    handle: ".modal-header",
                    containment: '#admin-body'
                }).css({
                    top: parseInt(prevPos[0]),
                    left: parseInt(prevPos[1])
                }).on( 'dragEnd', function( event, pointer ) {
                    revealM.rememberPositions(modalContent.position().top, modalContent.position().left);
                });
            }
        },

        rememberPositions : function(top, left) {
		    if (sessionStorage) {
                if (top && left) {
                    try {
                        var value = sessionStorage.setItem('mpos', top+','+left);
                    } catch(e) {}
                } else {
                    try {
                        var value = sessionStorage.getItem('mpos');
                        if (value !== null) {
                            return value.split(',');
                        }
                    } catch(e) {}
                }
            }
		    return null;
        },

        getPositions : function() {
		    return {
		        width: (window.innerWidth
                    || document.documentElement.clientWidth
                    || document.body.clientWidth
                    || 0),
                height: (window.innerHeight
                    || document.documentElement.clientHeight
                    || document.body.clientHeight
                    || 0)
            }
        }

};

module.exports = revealM;