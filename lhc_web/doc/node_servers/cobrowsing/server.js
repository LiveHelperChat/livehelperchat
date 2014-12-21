var http = require('http').createServer(handler)
   , config = require('./settings'); 

var io = require(config.socketiopath).listen(http);

// enable all transports (optional if you want flashsocket support, please note that some hosting
// providers do not allow you to create servers that listen on a port different than 80 or their
// default port)
io.set('transports',[
    'websocket',
    'polling',
    'xhr-polling',
    'jsonp-polling',
    'polling']);

http.listen(config.web.port,config.web.host);
console.log('LHC Co-Browsing Server listening on '+config.web.host+':'+config.web.port);

function handler(req, res) {
  res.writeHead(200);
  res.end();
}
console.log(config.debug.output);
io.sockets.on('connection', function (socket) {
       
  socket.on('newmessage', function (data) {
  		if (config.debug.output == true) {
  			console.log('newmessage:'+data.instance_id+'_'+data.chat_id); 	
  		};
  		
  		socket.broadcast.to('chat_room_'+data.instance_id+'_'+data.chat_id).emit('newmessage', data);    	
  });
 
  socket.on('userleftchat', function (data) {
  		if (config.debug.output == true) {
  			console.log('userleftchat:'+data.instance_id+'_'+data.chat_id);
  		}; 	
    	socket.broadcast.to('chat_room_'+data.instance_id+'_'+data.chat_id).emit('userleftchat', data.chat_id);
  });
  
  socket.on('join', function (data) {
	  if (config.debug.output == true) {
		  console.log('join:'+data.instance_id+'_'+data.chat_id);  
	  };		
	  socket.join('chat_room_'+data.instance_id+'_'+data.chat_id);
	  socket.broadcast.to('chat_room_'+data.instance_id+'_'+data.chat_id).emit('userjoined', data.chat_id);	  
  });

  socket.on('leave', function (data) {
  		if (config.debug.output == true) {
  			console.log('leave:'+data.instance_id+'_'+data.chat_id);
  		};
  		socket.leave('chat_room_'+data.instance_id+'_'+data.chat_id);  		
  });
  
  socket.on('syncforce', function (data) { 
  		if (config.debug.output == true) {
  			console.log('syncforce:'+data.instance_id+'_'+data.chat_id); 	
  		};
    	socket.broadcast.to('chat_room_'+data.instance_id+'_'+data.chat_id).emit('syncforce', data.chat_id);
  });
  
  socket.on('disconnect', function() {
	  
  }); 
  
});