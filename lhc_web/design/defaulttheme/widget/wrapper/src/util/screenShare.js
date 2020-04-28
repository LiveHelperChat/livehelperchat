import {helperFunctions} from '../lib/helperFunctions';

class _screenShare {
    constructor() {
        this.params = {};
        this.attributes = null;
        this.chatEvents = null;

        this.isSharing = false;
        this.sharemode = 'chat';
        this.sharehash = null;
        this.cobrowser = null;
        this.intervalRequest = null;
    }

    startCoBrowse(params) {

        if (typeof formsEnabled == "undefined") var formsEnabled = false;

        this.isSharing = true;

        this.cobrowser = new LHCCoBrowser({'formsenabled':formsEnabled,
                'chat_hash': this.sharehash,
                'nodejssettings': params['nodejssettings'],
                'nodejsenabled': params['nodejsenabled'],
                'trans': params['trans'],
            'url': params['url']+'/(hash)/'+this.sharehash+'/?url='+encodeURIComponent(location.href.match(/^(.*\/)[^\/]*$/)[1])});
        this.cobrowser.startMirroring();

        let listener = (data) => {
            if (this.cobrowser) {
                this.cobrowser.handleMessage(data.split(':'));
            }
        };

        this.attributes.eventEmitter.addListener('screenshareCommand', listener);
        
        this.attributes.eventEmitter.addOnceListener('finishScreenSharing', (data) => {

            helperFunctions.removeById('lhc_status_mirror');

            this.attributes.storageHandler.removeSessionStorage('LHC_screenshare');

            this.isSharing = false;
  
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            var locationCurrent = encodeURIComponent(window.location.href.substring(window.location.protocol.length));
            s.setAttribute('id','lhc_finish_shr');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src',LHC_API.args.lhc_base_url+'/cobrowse/finishsession/(sharemode)/chat/(hash)/'+this.sharehash);
            th.appendChild(s);

            this.cobrowser = null;

            this.attributes.eventEmitter.removeListener('screenshareCommand',listener);
        });

        this.attributes.storageHandler.setSessionStorage('LHC_screenshare',1);
    }

    setParams(params, attributes, chatEvents) {
        this.params = params;
        this.attributes = attributes;
        this.chatEvents = chatEvents;

        const chatParams = this.attributes['userSession'].getSessionAttributes();

        this.sharehash = chatParams['id'] + '_' + chatParams['hash'];

        if (this.isSharing == false) {
            helperFunctions.makeRequest(LHC_API.args.lhc_base_url + '/widgetrestapi/screensharesettings', {}, (data) => {
                if ((this.params['auto_start']) || data['auto_share'] == 1 || (this.attributes.focused == true && confirm('Allow operator to see your page content?'))) {
                    this.initCoBrowsing(data);
                } else if (this.attributes.focused == false) {
                    clearInterval(this.intervalRequest);
                    this.intervalRequest = setInterval(() => {
                        if (this.attributes.focused == true) {
                            clearInterval(this.intervalRequest);
                            if (confirm('Allow operator to see your page content?')){
                                this.initCoBrowsing(data);
                            }
                        }
                    },500);
                }
            });
        }
    }

    initCoBrowsing(data) {
        if (typeof TreeMirror == "undefined") {
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('type', 'text/javascript');
            s.setAttribute('src', data['cobrowser']);
            th.appendChild(s);
            s.onreadystatechange = s.onload = () => {
                this.startCoBrowse(data);
            };
        } else {
            this.startCoBrowse(data);
        }
    }
}

const screenShare = new _screenShare();
export {screenShare};

