import {helperFunctions} from '../lib/helperFunctions';

class _nodeJSChat {

    constructor() {
        this.params = {};
        this.attributes = null;
        this.chatEvents = null;
    }

    setParams(params, attributes, chatEvents) {
        this.params = params;
        this.attributes = attributes;
        this.chatEvents = chatEvents;

        const vid = this.attributes.userSession.getVID();

        var socketOptions = {
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

        var socket = socketCluster.connect(socketOptions);

        socket.on('error', function (err) {
            console.error(err);
        });

        function connectSiteVisitor(){
            var sampleChannel = socket.subscribe('uo_' + vid);

            sampleChannel.on('subscribeFail', function (err) {
                console.error('Failed to subscribe to the sample channel due to error: ' + err);
            });

            sampleChannel.watch(function (op) {
                if (op.op == 'check_message') {
                    attributes.eventEmitter.emitEvent('checkMessageOperator');
                } else if (op.op == 'is_online') {
                    socket.publish('ous_'+instance_id,{op:'vi_online', status: true, vid: vid});
                }
            });
        }

        var chanelName = 'uo_' + vid;
        var instance_id = this.attributes.instance_id;

        socket.on('deauthenticate', function() {
            helperFunctions.makeRequest(attributes.LHC_API.args.lhc_base_url + attributes['lang'] + "nodejshelper/tokenvisitor", { params: {ts: (new Date()).getTime()}}, (data) => {
                instance_id = data.instance_id;
                socket.emit('login', {hash: data.hash, chanelName: chanelName, instance_id: data.instance_id}, function (err) {
                    if (err) {
                        console.log(err);
                    }
                });
            })
        });

        socket.on('connect', function (status) {
            if (status.isAuthenticated) {
                connectSiteVisitor();
                // Disable check messages in case we connect to nodejs
                attributes.LHC_API.args.check_messages = false;
            } else {
                helperFunctions.makeRequest(attributes.LHC_API.args.lhc_base_url + attributes['lang'] + "nodejshelper/tokenvisitor", { params: {ts: (new Date()).getTime()}}, (data) => {
                    instance_id = data.instance_id;
                    socket.emit('login', {hash: data.hash, chanelName: chanelName, instance_id: data.instance_id}, function (err) {
                        if (err) {
                            console.log(err);
                        } else {
                            connectSiteVisitor();
                            // Disable check messages in case we connect to nodejs
                            attributes.LHC_API.args.check_messages = false;
                        }
                    });
                })
            }
        });
    }
}

const nodeJSChat = new _nodeJSChat();
export {nodeJSChat};

