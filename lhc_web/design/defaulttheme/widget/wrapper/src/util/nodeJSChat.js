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
            path: params.path
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
                }
            });
        }

        var chanelName = 'chat_'+'uo_' + vid;

        socket.on('connect', function (status) {
            if (status.isAuthenticated) {
                connectSiteVisitor();

                // Disable check messages in case we connect to nodejs
                LHC_API.args.check_messages = false;

            } else {
                socket.emit('login', {hash: params.hash, chanelName: chanelName}, function (err) {
                    if (err) {
                        console.log(err);
                    } else {

                        connectSiteVisitor();

                        // Disable check messages in case we connect to nodejs
                        LHC_API.args.check_messages = false;
                    }
                });
            }
        });
    }
}

const nodeJSChat = new _nodeJSChat();
export {nodeJSChat};

