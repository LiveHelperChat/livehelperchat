(window.webpackJsonp=window.webpackJsonp||[]).push([[2],{2:function(a,o){var l={cancelcolorbox:function(){$("#myModal").foundation("reveal","close")},initializeModal:function(a){var o=null!=a?a:"myModal";if(0==$("#"+o).length){(0==$("#widget-layout").length?$("body"):$("#widget-layout")).prepend('<div id="'+o+'" style="padding-right:0px !important;" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"></div>')}},revealModal:function(a){var o=!1;$("body").hasClass("modal-open")?(o=!0,$("#myModal").modal("hide")):$("#myModal").modal("dispose"),l.initializeModal("myModal");var d={show:!0};if(a.mparams,Object.assign(d,a.mparams),console.log(d),void 0===a.iframe)void 0!==a.loadmethod&&"post"==a.loadmethod?jQuery.post(a.url,a.datapost,(function(l){!1===o?(void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(l).modal("show")):setTimeout((function(){$("#myModal").modal("dispose"),void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(l).modal("show")}),500)})):jQuery.get(a.url,(function(l){!1===o?(void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(l).modal("show")):setTimeout((function(){$("#myModal").modal("dispose"),void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback),$("#myModal").html(l).modal("show")}),500)}));else{var i="",s="";void 0===a.hideheader?i='<div class="modal-header"><h4 class="modal-title" id="myModalLabel"><span class="material-icons">info</span>'+(void 0===a.title?"":a.title)+'</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>':s=(void 0===a.title?"":"<b>"+a.title+"</b>")+'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';var e=void 0===a.modalbodyclass?"":" "+a.modalbodyclass;$("#myModal").html('<div class="modal-dialog modal-xl"><div class="modal-content">'+i+'<div class="modal-body'+e+'">'+s+'<iframe src="'+a.url+'" frameborder="0" style="width:100%" height="'+a.height+'" /></div></div></div>').modal("show"),void 0!==a.showcallback&&$("#myModal").on("shown.bs.modal",a.showcallback),void 0!==a.hidecallback&&$("#myModal").on("hide.bs.modal",a.hidecallback)}}};a.exports=l}}]);