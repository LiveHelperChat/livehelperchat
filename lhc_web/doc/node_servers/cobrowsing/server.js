var http = require('http').createServer(handler)
, config = require('./settings'); 

var io = require(config.socketiopath).listen(http);

//enable all transports (optional if you want flashsocket support, please note that some hosting
//providers do not allow you to create servers that listen on a port different than 80 or their
//default port)
io.set('transports',[
                     'websocket',
                     'polling',
                     'xhr-polling',
                     'jsonp-polling',
                     'polling']);

http.listen(config.web.port,config.web.host);
console.log('LHC Co-browsing Server listening on '+config.web.host+':'+config.web.port);

function handler(req, res) {
	res.writeHead(200);
	res.end();
}

console.log("Debug enabled - "+config.debug.output);

io.sockets.on('connection', function (socket) {

	socket.on('usermessage', function (data) {
		try {
			if (config.debug.output == true) {
				console.log('usermessage:'+data.chat_id + JSON.stringify(data.msg)); 	
			};  		
			socket.broadcast.to('chat_room_'+data.chat_id).emit('usermessage', data.msg); 
		} catch (e) {
			if (config.debug.output == true) {
				throw e;
			}
		} 	
	});

	socket.on('remotecommand', function (data) {
		try {
			if (config.debug.output == true) {
				console.log('remotecommand:'+data.chat_id + JSON.stringify(data.cmd)); 	
			};  		
			socket.broadcast.to('chat_room_'+data.chat_id).emit('remotecommand', data.cmd); 
		} catch (e) {
			if (config.debug.output == true) {
				throw e;
			}
		}   	
	});

	socket.on('userleft', function (data) {
		try {
			if (config.debug.output == true) {
				console.log('userleft:'+data.chat_id);
			};
			socket.leave('chat_room_'+data.chat_id);
			socket.broadcast.to('chat_room_'+data.chat_id).emit('userleft', data.chat_id);
		} catch (e) {
			if (config.debug.output == true) {
				throw e;
			}
		} 
	});

	socket.on('join', function (data) {
		try { 
			if (config.debug.output == true) {
				console.log('join:'+data.chat_id);  
			};		
			socket.join('chat_room_'+data.chat_id);
			socket.broadcast.to('chat_room_'+data.chat_id).emit('userjoined', data.chat_id);
		} catch (e) {
			if (config.debug.output == true) {
				throw e;
			}
		} 
	});

	socket.on('joinadmin', function (data) {
		try { 
			if (config.debug.output == true) {
				console.log('joinadmin:'+data.chat_id);  
			};		
			socket.join('chat_room_'+data.chat_id);
		} catch (e) {
			if (config.debug.output == true) {
				throw e;
			}
		}   
	});

	socket.on('leave', function (data) {
		try {
			if (config.debug.output == true) {
				console.log('leave:'+data.chat_id);
			};
			socket.leave('chat_room_'+data.chat_id);   
		} catch (e) {
			if (config.debug.output == true) {
				throw e;
			}
		} 		
	});
});