import {helperFunctions} from '../lib/helperFunctions';

class _nodeJSChat {

    constructor() {
        this.params = {};
        this.attributes = null;
        this.chatEvents = null;
    }

    async setParams(params, attributes, chatEvents) {
        this.params = params;
        this.attributes = attributes;
        this.chatEvents = chatEvents;

        const vid = this.attributes.userSession.getVID();

        var socketOptions = {
            protocolVersion: 1,
            hostname: params.hostname,
            path: params.path,
            disconnectOnUnload: false,
            authTokenName: 'socketCluster.authToken_vi'
        }

        if (params.port != '') {
            socketOptions.port = parseInt(params.port);
        }

        if (params.secure == 1) {
            socketOptions.secure = true;
        }

        var socketCluster = require("socketcluster-client");

        var socket = socketCluster.create(socketOptions);

        var chanelName = 'uo_' + vid;
        var instance_id = this.attributes.instance_id;
        var sampleChannel = null;
        // Do not update vars while we are updating it from other browser tabs
        var ignoreVars = false;

        let status = await socket.listener('connect').once();
        if (status.isAuthenticated) {
            connectSiteVisitor();
            attributes.LHC_API.args.check_messages = false;
        } else {
            authentificate();
        }

        try {
            for await (let event of socket.listener('deauthenticate')) {
                authentificate();
            }
        } catch (e) {
            // shut up old browers
        }


        function authentificate() {
            helperFunctions.makeRequest(attributes.LHC_API.args.lhc_base_url + attributes['lang'] + "nodejshelper/tokenvisitor", { params: {ts: (new Date()).getTime()}}, async (data) => {
                instance_id = data.instance_id;
                await Promise.all([
                    socket.invoke('login',{hash: data.hash, chanelName: chanelName, instance_id: data.instance_id}),
                    socket.listener('authenticate').once()
                ]);
                connectSiteVisitor();
            })
        }

        async function connectSiteVisitor() {
            var firstRun = sampleChannel == null, ignoreNext = false, timeoutClear = null;
            sampleChannel = socket.subscribe('uo_' + vid);
            if (firstRun == true) {
                try {
                    // We want to receive signal is widget open in any of the windows
                    attributes.mode != 'embed' && !attributes.widgetStatus.value && socket.transmitPublish('uo_' + vid, {op: 'ws_isopen', 'clientId' : sampleChannel.client.clientId});

                    // We want to publish request to receive all the vars other instances has
                    if (attributes.lhc_var !== null) {
                        socket.transmitPublish('uo_' + vid, { 'clientId' : sampleChannel.client.clientId, op: 'check_vars', 'init':false, 'lhc_var': attributes.lhc_var});
                        attributes.eventEmitter.addListener('jsVarsUpdated', function () {
                            ignoreVars === false && attributes.ignoreVars === false && socket.transmitPublish('uo_' + vid, {'clientId' : sampleChannel.client.clientId, op:'current_vars', 'init':false, 'lhc_var': attributes.lhc_var});
                            ignoreVars = false;
                        });
                    }

                    // Subscribe to widget status, just ignore initial status
                    attributes.mode != 'embed' && attributes.widgetStatus.subscribe((data) => {
                        if (ignoreNext == true) {
                            clearTimeout(timeoutClear);
                            timeoutClear = setTimeout(() => {
                                ignoreNext = false;
                            }, 1000);
                            return;
                        }
                        socket.transmitPublish('uo_' + vid, {op: 'wstatus', status: data, 'clientId' : sampleChannel.client.clientId,});
                    }, true);

                    // Listen for chat started event and dispatch to other windows
                    attributes.eventEmitter.addListener('chatStarted', function (data, mode) {
                        if (mode !== 'popup' || attributes.kcw === true) {
                            socket.transmitPublish('uo_' + vid, {op: 'chat_started', data: data, 'clientId' : sampleChannel.client.clientId,});
                        }
                    });
                } catch (e) {
                    console.log(e);
                }
                try {
                    for await (let op of sampleChannel) {
                        if (op.op == 'check_message') {
                            attributes.eventEmitter.emitEvent('checkMessageOperator');
                        } else if (op.op == 'is_online') {
                            socket.transmitPublish('ous_'+instance_id,{op:'vi_online', status: true, vid: vid});
                        } else if (op.op == 'chat_started') {
                            try {
                                if (attributes.userSession.id === null && op.data.id && sampleChannel.client.clientId != op.clientId) {
                                    chatEvents.sendChildEvent('reopenNotification', [{
                                        'id': op.data.id,
                                        'hash': op.data.hash
                                    }]);
                                }
                            } catch (e) {
                                console.log(e);
                            }
                        } else if (op.op == 'ws_isopen') {
                            try {
                                if (attributes.mode != 'embed' && attributes.widgetStatus.value && sampleChannel.client.clientId != op.clientId) {
                                    socket.transmitPublish('uo_'+vid,{op:'wstatus', status: true});
                                }
                            } catch (e) {
                                console.log(e);
                            }

                        } else if (op.op == 'wstatus') {
                            try {
                                if (attributes.mode != 'embed' && op.status != attributes.widgetStatus.value && sampleChannel.client.clientId != op.clientId) {
                                    ignoreNext = true;
                                    attributes.widgetStatus.next(op.status);
                                }
                            } catch (e) {
                                console.log(e);
                            }
                        } else if (op.op == 'current_vars' || op.op == 'check_vars') {
                            try {
                                if (sampleChannel.client.clientId != op.clientId){
                                    if (op.lhc_var && attributes.lhc_var !== null) {
                                        ignoreVars = true;
                                        attributes.ignoreVars = true;
                                        for (var index in op.lhc_var) {
                                            if ((typeof attributes.lhc_var[index] === 'undefined' || attributes.lhc_var[index] === '' || op.init === false) && op.lhc_var[index] !== '' && attributes.lhc_var[index] !== op.lhc_var[index]) {
                                                attributes.lhc_var[index] = op.lhc_var[index];
                                            }
                                        }
                                        attributes.ignoreVars = false;
                                    }
                                    if (op.op == 'check_vars') {
                                        attributes.lhc_var !== null && socket.transmitPublish('uo_'+vid,{'clientId' : sampleChannel.client.clientId, op:'current_vars', 'init':true, 'lhc_var': attributes.lhc_var});
                                    }
                                }
                            } catch (e) {
                                console.log(e);
                            }
                        }
                    }
                } catch (e) {
                    // shut up old browsers
                }
            }
        }

    }
}

const nodeJSChat = new _nodeJSChat();
export {nodeJSChat};

