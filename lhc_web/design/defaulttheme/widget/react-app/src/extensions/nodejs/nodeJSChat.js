import { helperFunctions } from "../../lib/helperFunctions";
import { fetchMessages, checkChatStatus, updateMessage } from "../../actions/chatActions"

class _nodeJSChat {
    constructor() {
        this.socket = null;

        // On chat close event close connection
        helperFunctions.eventEmitter.addListener('endedChat', () => {
            if (this.socket !== null) {
                this.socket.destroy();
            }
        });
    }

    bootstrap(params, dispatch, getState) {

        const state = getState();
        const chatId = state.chatwidget.getIn(['chatData','id']);
        const chatHash = state.chatwidget.getIn(['chatData','hash']);
        const syncDefault = state.chatwidget.getIn(['chat_ui','sync_interval']);

        var socketOptions = {
            hostname: params.hostname,
            path: params.path,
            autoReconnectOptions: {initialDelay: 5000, randomness: 5000}
        }

        if (params.port != '') {
            socketOptions.port = parseInt(params.port);
        }

        if (params.secure == 1) {
            socketOptions.secure = true;
        }

        var chanelName;

        if (params.instance_id > 0) {
            chanelName = ('chat_'+params.instance_id+'_'+chatId);
        } else{
            chanelName = ('chat_'+chatId);
        }

        var socketCluster = require('socketcluster-client');

        var socket = this.socket = socketCluster.connect(socketOptions);
        
        var sampleChannel = null;
        
        socket.on('error', function (err) {
            console.error(err);
        });

       function visitorTypingListener(data)
       {
            if (data.status == true){
                if (params.instance_id > 0) {
                    socket.publish('chat_'+params.instance_id+'_'+chatId,{'op':'vt','msg':data.msg});
                } else {
                    socket.publish('chat_'+chatId,{'op':'vt','msg':data.msg});
                }
            } else {
                if (params.instance_id > 0) {
                    socket.publish('chat_'+params.instance_id+'_'+chatId,{'op':'vts'});
                } else {
                    socket.publish('chat_'+chatId,{'op':'vts'});
                }
            }
       }

       function messageSend(data)
       {
            if (params.instance_id > 0) {
                socket.publish('chat_'+params.instance_id+'_'+chatId, {'op':'vt','msg':'✉️ ' + data.msg});
            } else {
                socket.publish('chat_'+chatId,{'op':'vt', 'msg':'✉️ ' + data.msg});
            }
        }

       function messageSendError(data)
       {
            if (params.instance_id > 0) {
                socket.publish('chat_'+params.instance_id+'_'+chatId,{'op':'vt','msg':'📕️ error happened while sending visitor message, please inform your administrator!'});
            } else {
                socket.publish('chat_'+chatId, {'op':'vt','msg':'📕️ error happened while sending visitor message, please inform your administrator!'});
            }
        }

        function disconnect() {
            if (sampleChannel !== null) {
                try {
                    sampleChannel.destroy();
                } catch (e) {

                }
            }

            helperFunctions.eventEmitter.removeListener('visitorTyping', visitorTypingListener);
            helperFunctions.eventEmitter.removeListener('messageSend', messageSend);
            helperFunctions.eventEmitter.removeListener('messageSendError', messageSendError);

            dispatch({
                'type': 'CHAT_UI_UPDATE',
                'data': {sync_interval: syncDefault}
            });

            dispatch({
                'type': 'CHAT_REMOVE_OVERRIDE',
                'data': "typing"
            });
        }

        socket.on('close', function() {
            disconnect();
        });

        function connectVisitor(){
            if (params.instance_id > 0) {
                sampleChannel = socket.subscribe('chat_'+params.instance_id+'_'+chatId);
            } else {
                sampleChannel = socket.subscribe('chat_' + chatId);
            }

            sampleChannel.on('subscribeFail', function (err) {
                console.error('Failed to subscribe to the sample channel due to error: ' + err);
            });

            sampleChannel.on('subscribe', function () {
                socket.publish((params.instance_id > 0 ? 'chat_'+params.instance_id+'_'+chatId : 'chat_'+chatId), {'op':'vi_online', status: true});
            });

            sampleChannel.watch(function (op) {
                if (op.op == 'ot') { // Operator Typing Message
                    if (op.data.status == true) {
                        dispatch({
                            'type': 'chat_status_changed',
                            'data': {text: op.data.ttx}
                        });
                    } else {
                        dispatch({
                            'type': 'chat_status_changed',
                            'data': {text: ''}
                        });
                    }
                } else if (op.op == 'cmsg' || op.op == 'schange') {
                    const state = getState();
                    if (state.chatwidget.hasIn(['chatData','id'])){
                        dispatch(fetchMessages({
                            'chat_id': state.chatwidget.getIn(['chatData','id']),
                            'hash' : state.chatwidget.getIn(['chatData','hash']),
                            'lmgsid' : state.chatwidget.getIn(['chatLiveData','lmsgid']),
                            'theme' : state.chatwidget.get('theme')
                        }));
                    }
                } else if (op.op == 'umsg') {
                    const state = getState();
                    if (state.chatwidget.hasIn(['chatData','id'])) {
                        updateMessage({'msg_id' :  op.msid,'id' : state.chatwidget.getIn(['chatData','id']), 'hash' : state.chatwidget.getIn(['chatData','hash'])})(dispatch, getState);
                    }
                } else if (op.op == 'schange' || op.op == 'cclose') {
                    const state = getState();
                    if (state.chatwidget.hasIn(['chatData','id'])){
                        dispatch(checkChatStatus({
                            'chat_id': state.chatwidget.getIn(['chatData','id']),
                            'hash' : state.chatwidget.getIn(['chatData','hash']),
                            'mode' : state.chatwidget.get('mode'),
                            'theme' : state.chatwidget.get('theme')
                        }));
                    }
                } else if (op.op == 'vo') {
                    const state = getState();
                    if (state.chatwidget.hasIn(['chatData','id'])) {
                        socket.publish((params.instance_id > 0 ? 'chat_'+params.instance_id+'_'+state.chatwidget.getIn(['chatData','id']) : 'chat_'+state.chatwidget.getIn(['chatData','id'])) ,{'op':'vi_online', status: true});
                    }
                }
            });

            helperFunctions.eventEmitter.addListener('visitorTyping', visitorTypingListener);
            helperFunctions.eventEmitter.addListener('messageSend', messageSend);
            helperFunctions.eventEmitter.addListener('messageSendError', messageSendError);

            dispatch({
                'type': 'CHAT_UI_UPDATE',
                'data': {sync_interval: 10000}
            });

            dispatch({
                'type': 'CHAT_ADD_OVERRIDE',
                'data': "typing"
            });
        }

        socket.on('deauthenticate', function(){
            const state = getState();
            let chat_id = state.chatwidget.getIn(['chatData','id']);
            window.lhcAxios.post(window.lhcChat['base_url'] + "nodejshelper/tokenvisitor/"+chat_id+"/"+state.chatwidget.getIn(['chatData','hash']), null, {headers : {'Content-Type': 'application/x-www-form-urlencoded'}}).then((response) => {
                socket.emit('login', {hash:response.data, chanelName: (params.instance_id > 0 ? ('chat_'+params.instance_id+'_'+chat_id) : ('chat_'+chat_id)) }, function (err) {
                    if (err) {
                        console.log(err);
                        disconnect();
                    }
                });
            });
        });

        socket.on('connect', function (status) {
            if (status.isAuthenticated && chatId > 0) {
                connectVisitor();
            } else {
                const state = getState();
                let chat_id = state.chatwidget.getIn(['chatData','id']);
                window.lhcAxios.post(window.lhcChat['base_url'] + "nodejshelper/tokenvisitor/"+chat_id+"/"+state.chatwidget.getIn(['chatData','hash']), null, {headers : {'Content-Type': 'application/x-www-form-urlencoded'}}).then((response) => {
                    socket.emit('login', {hash: response.data, chanelName: (params.instance_id > 0 ? ('chat_'+params.instance_id+'_'+chat_id) : ('chat_'+chat_id)) }, function (err) {
                        if (err) {
                            console.log(err);
                            socket.destroy();
                        } else {
                            connectVisitor();
                        }
                    });
                });
            }
        });
    }
}

const nodeJSChat = new _nodeJSChat();
export { nodeJSChat };