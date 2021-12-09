import {helperFunctions} from '../lib/helperFunctions';

class _zoomImage {
    constructor() {
        this.params = {};
        this.attributes = null;
        this.chatEvents = null;
    }

    cleanup(){
        this.removeById('lhc-co-browsing-modal');
        this.removeById('lhc-zoom-style');
    }

    setParams(params, attributes, chatEvents) {

        this.params = params;
        this.attributes = attributes;
        this.chatEvents = chatEvents;

        this.addCss('body{overflow:hidden;}.lhc-modal *{box-sizing: border-box;}.lhc-modal { display: none; position: fixed; z-index: 2147483641 !important;padding-top: ' + (attributes.isMobile == true ? 0 : 20)  +'px;left: 0;top: 0;  width: 100%;height: 100%; overflow: auto; background-color: rgb(0,0,0);  background-color: rgba(0,0,0,0.4); }'+
            '.lhc-modal-content {background-color: #fefefe; margin: auto; border: 1px solid #888; width: ' + (attributes.isMobile == true ? 100 : 80)  +'%;border-radius:5px; }'+
            '#lhc-close { color: #aaaaaa;    font-size: 34px;    font-weight: bold;  }'+
            '#lhc-close:hover,#lhc-close:focus {color: #000; text-decoration: none; cursor: pointer;}');

        this.appendHTML('<div id="lhc-co-browsing-modal" style="display: block" class="lhc-modal">'+
            '<div class="lhc-modal-content">'+
            '<div style="padding:5px 20px;clear: both; text-align: right"><span id="lhc-close">&times;</span></div>'+
            '<div style="text-align: center;background-color:#cecece"><img id="lhc-zoom-image" src="'+params.src+'" style="max-width:100%;max-height: '+(window.innerHeight - 140) +'px;" title="" /></div><div><div style="height: 49px; padding:10px;" ><span style="font-family: Arial; font-size: 18px;font-weight: bold;">'+params.title+'&nbsp;</span> <a target="_blank" href="'+params.src+'" style="font-family: Arial; text-decoration: none;background-color: #dddddd; display: inline-block; padding:5px; border:1px solid #CECECE; color: #6d6d6d; float: right">'+params.txt_download+'</a></div></div></div></div>');

        var btn = document.getElementById("lhc-close");
        var modal = document.getElementById("lhc-co-browsing-modal");

        var that = this;

        btn.onclick = () => {
            this.cleanup();
            window.removeEventListener('click',clickListener);
            window.removeEventListener('resize',resizeListener);
        }

        function clickListener(event) {
            if (event.target == modal) {
                that.cleanup();
                window.removeEventListener('click',clickListener);
                window.removeEventListener('resize',resizeListener);
            }
        }

        function resizeListener(event) {
                document.getElementById('lhc-zoom-image').style.maxHeight = (window.innerHeight - 140) + "px";
        }

        window.addEventListener('click',clickListener);
        window.addEventListener('resize', resizeListener);
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
        style.id = "lhc-zoom-style";
        if (style.styleSheet) {
            style.styleSheet.cssText = css_content;
        } else {
            var rules = document.createTextNode(css_content);
            style.appendChild(rules);
        };

        head.appendChild(style);
    }
}

const zoomImage = new _zoomImage();
export {zoomImage};

