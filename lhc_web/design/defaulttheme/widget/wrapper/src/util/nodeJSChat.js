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
            var firstRun = sampleChannel == null;
            sampleChannel = socket.subscribe('uo_' + vid);
            if (firstRun == true) {
                try {
                    // We want to receive signal is widget open in any of the windows
                    attributes.mode != 'embed' && !attributes.widgetStatus.value && socket.transmitPublish('uo_' + vid, {op: 'ws_isopen'});

                    // We want to publish request to receive all the vars other instances has
                    attributes.lhc_var !== null && socket.transmitPublish('uo_' + vid, {op: 'check_vars'});

                    // Subscribe to widget status, just ignore initial status
                    attributes.mode != 'embed' && attributes.widgetStatus.subscribe((data) => {
                        socket.transmitPublish('uo_' + vid, {op: 'wstatus', status: data});
                    }, true);

                    // Listen for chat started event and dispatch to other windows
                    attributes.eventEmitter.addListener('chatStarted', function (data, mode) {
                        if (mode !== 'popup' || attributes.kcw === true) {
                            socket.transmitPublish('uo_' + vid, {op: 'chat_started', data: data});
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
                                if (attributes.userSession.id === null && op.data.id) {
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
                                if (attributes.mode != 'embed' && attributes.widgetStatus.value) {
                                    socket.transmitPublish('uo_'+vid,{op:'wstatus', status: true});
                                }
                            } catch (e) {
                                console.log(e);
                            }

                        } else if (op.op == 'wstatus') {
                            try {
                                if (attributes.mode != 'embed' && op.status != attributes.widgetStatus.value) {
                                    attributes.widgetStatus.next(op.status);
                                }
                            } catch (e) {
                                console.log(e);
                            }
                        } else if (op.op == 'current_vars') {
                            try {
                                if (op.lhc_var && attributes.lhc_var !== null) {
                                    for (var index in op.lhc_var) {
                                        if (typeof attributes.lhc_var[index] == 'undefined') {
                                            attributes.lhc_var[index] = op.lhc_var[index];
                                        }
                                    }
                                }
                            } catch (e) {
                                console.log(e);
                            }
                        } else if (op.op == 'check_vars') {
                            attributes.lhc_var && socket.transmitPublish('uo_'+vid,{op:'current_vars', 'lhc_var': attributes.lhc_var});
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

