import { helperFunctions } from "../../lib/helperFunctions";
import { fetchMessages, checkChatStatus, updateMessage } from "../../actions/chatActions"

import socketCluster from "socketcluster-client";


class _nodeJSChat {
    constructor() {
        this.socket = null;

        // On chat close event close connection
        helperFunctions.eventEmitter.addListener('endedChat', () => {
            if (this.socket !== null) {
                this.socket.disconnect();
            }
        });
    }

    bootstrap(params, dispatch, getState) {

        const state = getState();
        const chatId = state.chatwidget.getIn(['chatData','id']);
        const chatHash = state.chatwidget.getIn(['chatData','hash']);
        const syncDefault = state.chatwidget.getIn(['chat_ui','sync_interval']);

        var socketOptions = {
            protocolVersion: 1,
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

        var socket = this.socket = socketCluster.create(socketOptions);

        var sampleChannel = null;

        (async () => {
            try {
                for await (let status of socket.listener('connect')) {
                    doActionByConnectionStatus(status);
                }
            } catch (e) {
                let status = await socket.listener('connect').once();
                doActionByConnectionStatus(status);
            }
        })();

        function doActionByConnectionStatus(status) {
            if (status.isAuthenticated && chatId > 0) {
                connectVisitor();
            } else {
                authentificate();
            }
        }

        function authentificate() {
            const state = getState();
            let chat_id = state.chatwidget.getIn(['chatData','id']);
            window.lhcAxios.post(window.lhcChat['base_url'] + "nodejshelper/tokenvisitor/"+chat_id+"/"+state.chatwidget.getIn(['chatData','hash']), null, {headers : {'Content-Type': 'application/x-www-form-urlencoded'}}).then(async (response) => {
                await Promise.all([
                    socket.invoke('login', {hash: response.data, chanelName: (params.instance_id > 0 ? ('chat_'+params.instance_id+'_'+chat_id) : ('chat_'+chat_id)) }),
                    socket.listener('authenticate').once()
                ]);
                connectVisitor();
            });
        }

       function visitorTypingListener(data)
       {
            if (data.status == true){
                if (params.instance_id > 0) {
                    socket.transmitPublish('chat_'+params.instance_id+'_'+chatId,{'op':'vt','msg':data.msg});
                } else {
                    socket.transmitPublish('chat_'+chatId,{'op':'vt','msg':data.msg});
                }
            } else {
                if (params.instance_id > 0) {
                    socket.transmitPublish('chat_'+params.instance_id+'_'+chatId,{'op':'vts'});
                } else {
                    socket.transmitPublish('chat_'+chatId,{'op':'vts'});
                }
            }
       }

       function messageSend(data)
       {
            if (params.instance_id > 0) {
                socket.transmitPublish('chat_'+params.instance_id+'_'+chatId, {'op':'vt','msg':'✉️ ' + data.msg});
            } else {
                socket.transmitPublish('chat_'+chatId,{'op':'vt', 'msg':'✉️ ' + data.msg});
            }
        }

       function messageSendError(data)
       {
            if (params.instance_id > 0) {
                socket.transmitPublish('chat_'+params.instance_id+'_'+chatId,{'op':'vt','msg':'📕️ error happened while sending visitor message, please inform your administrator!'});
            } else {
                socket.transmitPublish('chat_'+chatId, {'op':'vt','msg':'📕️ error happened while sending visitor message, please inform your administrator!'});
            }
        }

        function disconnect() {

            if (sampleChannel !== null) {
                try {
                    sampleChannel.unsubscribe();
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

        (async () => {
            try {
                for await (let event of socket.listener('disconnect')) {
                    disconnect();
                }
            } catch (e) {
                let status = await socket.listener('disconnect').once();
                disconnect();
            }
        })();

        function connectVisitor() {
            var firstRun = sampleChannel == null;

            if (params.instance_id > 0) {
                sampleChannel = socket.subscribe('chat_'+params.instance_id+'_'+chatId);
            } else {
                sampleChannel = socket.subscribe('chat_' + chatId);
            }

            helperFunctions.eventEmitter.addListener('visitorTyping', visitorTypingListener);
            helperFunctions.eventEmitter.addListener('messageSend', messageSend);
            helperFunctions.eventEmitter.addListener('messageSendError', messageSendError);

            dispatch({
                'type': 'CHAT_ADD_OVERRIDE',
                'data': "typing"
            });

            if (firstRun == true)
            {
                (async () => {
                    try {
                        for await (let event of sampleChannel.listener('subscribe')) {
                            socket.transmitPublish((params.instance_id > 0 ? 'chat_' + params.instance_id + '_' + chatId : 'chat_' + chatId), {
                                'op': 'vi_online',
                                status: true
                            });
                            dispatch({
                                'type': 'CHAT_UI_UPDATE',
                                'data': {sync_interval: 10000}
                            });
                        }
                    } catch (e) {
                        let event = await sampleChannel.listener('subscribe').once();
                        socket.transmitPublish((params.instance_id > 0 ? 'chat_' + params.instance_id + '_' + chatId : 'chat_' + chatId), {
                            'op': 'vi_online',
                            status: true
                        });
                    }

                })();
                (async () => {
                    try {
                        for await (let op of sampleChannel) {
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
                                        'theme' : state.chatwidget.get('theme'),
                                        'active_widget':  (((state.chatwidget.get('shown') && state.chatwidget.get('mode') == 'widget') || (state.chatwidget.get('mode') != 'widget' && document.hasFocus())) && window.lhcChat['is_focused'] == true)
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
                                    socket.transmitPublish((params.instance_id > 0 ? 'chat_'+params.instance_id+'_'+state.chatwidget.getIn(['chatData','id']) : 'chat_'+state.chatwidget.getIn(['chatData','id'])) ,{'op':'vi_online', status: true});
                                }
                            }
                        }
                    } catch (e) {
                        // Shut up old browsers
                    }
                })();
            }
       }

       (async () => {
            try {
                for await (let event of socket.listener('deauthenticate')) {
                    authentificate();
                }
            } catch (e) {
                let event = await socket.listener('deauthenticate').once();
                authentificate();
            }
       })();
    }
}

const nodeJSChat = new _nodeJSChat();
export { nodeJSChat };