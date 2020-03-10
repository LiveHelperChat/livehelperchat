import { helperFunctions } from "../../lib/helperFunctions";
import { fetchMessages, checkChatStatus } from "../../actions/chatActions"

class _nodeJSChat {
    constructor() {
        this.socket = null;
    }

    bootstrap(params, dispatch, getState) {

        const state = getState();
        const chatId = state.chatwidget.getIn(['chatData','id']);
        const chatHash = state.chatwidget.getIn(['chatData','hash']);
        const syncDefault = state.chatwidget.getIn(['chat_ui','sync_interval']);

        var socketOptions = {
            hostname: params.hostname,
            path: params.path
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

        var socket= socketCluster.connect(socketOptions);
        
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

        socket.on('close', function(){

            if (sampleChannel !== null) {
                sampleChannel.destroy();
            }

            helperFunctions.eventEmitter.removeListener('visitorTyping', visitorTypingListener);

            dispatch({
                'type': 'CHAT_UI_UPDATE',
                'data': {sync_interval: syncDefault}
            });

            dispatch({
                'type': 'CHAT_REMOVE_OVERRIDE',
                'data': "typing"
            });

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
                } else if (op.op == 'schange') {
                    const state = getState();
                    if (state.chatwidget.hasIn(['chatData','id'])){
                        dispatch(checkChatStatus({
                            'chat_id': state.chatwidget.getIn(['chatData','id']),
                            'hash' : state.chatwidget.getIn(['chatData','hash']),
                            'mode' : state.chatwidget.get('mode'),
                            'theme' : state.chatwidget.get('theme')
                        }));
                    }
                }
            });

            helperFunctions.eventEmitter.addListener('visitorTyping', visitorTypingListener);

            dispatch({
                'type': 'CHAT_UI_UPDATE',
                'data': {sync_interval: 10000}
            });

            dispatch({
                'type': 'CHAT_ADD_OVERRIDE',
                'data': "typing"
            });
        }

        socket.on('connect', function (status) {
            if (status.isAuthenticated && chatId > 0) {
                connectVisitor();
            } else {
                socket.emit('login', {hash:params.hash, chanelName: chanelName}, function (err) {
                    if (err) {
                        console.log(err);
                    } else {
                        connectVisitor();
                    }
                });
            }
        });
    }
}

const nodeJSChat = new _nodeJSChat();
export { nodeJSChat };