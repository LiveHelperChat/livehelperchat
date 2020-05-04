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
                if (this.params['auto_start'] || data['auto_share'] == 1) {
                    this.initCoBrowsing(data);
                } else {

                    this.addCss('.lhc-modal {display: none; position: fixed; z-index: 1000001 !important;padding-top: 100px;left: 0;top: 0;  width: 100%;height: 100%; overflow: auto; background-color: rgb(0,0,0);  background-color: rgba(0,0,0,0.4); }'+
                                  '.lhc-modal-content {background-color: #fefefe; margin: auto; padding: 20px; border: 1px solid #888; width: 60%;border-radius:5px; }'+
                                  '#lhc-close { color: #aaaaaa;    float: right;  font-size: 28px;    font-weight: bold;  }'+
                                  '#lhc-close:hover,#lhc-close:focus {color: #000; text-decoration: none; cursor: pointer;}');

                    this.appendHTML('<div id="lhc-co-browsing-modal" style="display: block" class="lhc-modal">'+
                        '<div class="lhc-modal-content">'+
                            '<span id="lhc-close">&times;</span>'+
                            '<p style="text-align: center"><button id="lhc-start-share-session" style="background-color: #4CAF50;' +
                        '  border: none;' +
                        '  color: white;' +
                        '  padding: 7px 16px;' +
                        '  text-align: center;border-radius:5px;' +
                        '  text-decoration: none;' +
                        '  display: inline-block;' +
                        '  font-size: 16px;' +
                        '  margin: 4px 2px;' +
                        '  cursor: pointer;">' + data.trans.start_share + '</button><button id="lhc-deny-share-session" style="background-color: #d2404a;' +
                        '  border: none;' +
                        '  color: white;' +
                        '  padding: 7px 16px;' +
                        '  text-align: center;border-radius:5px;' +
                        '  text-decoration: none;' +
                        '  display: inline-block;' +
                        '  font-size: 16px;' +
                        '  margin: 4px 2px;' +
                        '  cursor: pointer;">' + data.trans.deny + '</button></p></div></div>');

                    var btn = document.getElementById("lhc-close");
                    var btnDeny = document.getElementById("lhc-deny-share-session");
                    var modal = document.getElementById("lhc-co-browsing-modal");

                    btnDeny.onclick = btn.onclick = () => {
                        this.removeById('lhc-co-browsing-modal');
                    }

                    window.addEventListener('click',(event) => {
                        if (event.target == modal) {
                            this.removeById('lhc-co-browsing-modal');
                        }
                    });

                    document.getElementById("lhc-start-share-session").onclick = () => {
                        this.removeById('lhc-co-browsing-modal');
                        this.initCoBrowsing(data);
                    };
                }
            });
        }
    }

    removeById(EId)
    {
        var EObj = null;
        return(EObj = document.getElementById(EId))?EObj.parentNode.removeChild(EObj):false;
    }

    appendHTML(htmlStr) {
        var frag = document.createDocumentFragment(),
            temp = document.createElement('div');
        temp.innerHTML = htmlStr;
        while (temp.firstChild) {
            frag.appendChild(temp.firstChild);
        };
        document.body.insertBefore(frag, document.body.childNodes[0]);
    }

    addCss(css_content) {
        var head = document.getElementsByTagName('head')[0];
        var style = document.createElement('style');
        style.type = 'text/css';

        if (style.styleSheet) {
            style.styleSheet.cssText = css_content;
        } else {
            var rules = document.createTextNode(css_content);
            style.appendChild(rules);
        };

        head.appendChild(style);
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

