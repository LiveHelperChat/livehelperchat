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
                    for await (let op of sampleChannel) {
                        if (op.op == 'check_message') {
                            attributes.eventEmitter.emitEvent('checkMessageOperator');
                        } else if (op.op == 'is_online') {
                            socket.transmitPublish('ous_'+instance_id,{op:'vi_online', status: true, vid: vid});
                        }
                    }
                } catch (e){
                    // shut up old browsers
                }
            }
        }

    }
}

const nodeJSChat = new _nodeJSChat();
export {nodeJSChat};

