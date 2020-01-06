
class _helperFunctions {
    constructor() {
    }

    initElement(a, c, b, k, s) {
        var e;
        a = a.createElement(c);
        b = b || {};
        for (e in b) a[e] = b[e];
        s && "iframe" !== c && (a.innerHTML = s);
        k && (a.style.cssText = k);
        return a
    };
    
    getDocument(a) {
        return a.contentWindow ? a.contentWindow.document : a.contentDocument ? a.contentDocument : a.document ? a.document : null
    };

    // Returns time zone offset
    getTzOffset(){
        Date.prototype.stdTimezoneOffset = function() {
            var jan = new Date(this.getFullYear(), 0, 1);
            var jul = new Date(this.getFullYear(), 6, 1);
            return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
        };

        Date.prototype.dst = function() {
            return this.getTimezoneOffset() < this.stdTimezoneOffset();
        };

        var today = new Date();
        var timeZoneOffset = 0;

        if (today.dst()) {
            timeZoneOffset = today.getTimezoneOffset();
        } else {
            timeZoneOffset = today.getTimezoneOffset()-60;
        };

        return (timeZoneOffset/60)*-1;
    };

    getAbstractStyle(params) {
        return [
            "outline:                    " + (params.outline ? params.outline : "none") + " !important; ",
            "visibility:                 " + (params.visibility ? params.visibility : "visible") + " !important; ",
            "resize:                     " + (params.resize ? params.resize : "none") + " !important; ",
            "box-shadow:                 " + (params.boxshadow ? params.boxshadow : "none") + " !important; ",
            "overflow:                   " + (params.overflow ? params.overflow : "visible") + " !important; ",
            "background                  : " + (params.background ? params.background : "none") + " !important; ",
            "opacity                     : " + (params.opacity ? params.opacity : "1") + " !important; ",
            "filter                      : alpha(opacity=" + (params.opacity ? 100 * params.opacity : "100") + ") !important; ",
            "-ms-filter                  : progid:DXImageTransform.Microsoft.Alpha(Opacity" + (params.opacity ? params.opacity : "1") + ") !important; ",
            "-moz-opacity                : " + (params.opacity ? params.opacity : "1") + " !important; ",
            "-khtml-opacity              : " + (params.opacity ? params.opacity : "1") + " !important; ",
            "top                         : " + (params.top ? params.top : "auto") + " !important; ",
            "right                       : " + (params.right ? params.right : "auto") + " !important; ",
            "bottom                      : " + (params.bottom ? params.bottom : "auto") + " !important; ",
            "left                        : " + (params.left ? params.left : "auto") + " !important; ",
            "position                    : " + (params.position ? params.position : "absolute") + " !important; ",
            "border                      : " + (params.border ? params.border : "0") + " !important; ",
            "min-height                  : " + (params.minheight ? params.minheight : "auto") + " !important; ",
            "min-width                   : " + (params.minwidth ? params.minwidth : "auto") + " !important; ",
            "max-height                  : " + (params.maxheight ? params.maxheight : "none") + " !important; ",
            "max-width                   : " + (params.maxwidth ? params.maxwidth : "none") + " !important; ",
            "padding                     : " + (params.padding ? params.padding : "0") + " !important; ",
            "margin                      : " + (params.margin ? params.margin : "0") + " !important; ",
            "-moz-transition-property    : " + (params.transition ? params.transition : "none") + " !important; ",
            "-webkit-transition-property : " + (params.transition ? params.transition : "none") + " !important; ",
            "-o-transition-property      : " + (params.transition ? params.transition : "none") + " !important; ",
            "transition-property         : " + (params.transition ? params.transition : "none") + " !important; ",
            "transform                   : " + (params.transform ? params.transform : "none") + " !important; ",
            "-webkit-transform           : " + (params.transform ? params.transform : "none") + " !important; ",
            "-ms-transform               : " + (params.transform ? params.transform : "none") + " !important; ",
            "width                       : " + (params.width ? params.width : "auto") + " !important; ",
            "height                      : " + (params.height ? params.height : "auto") + " !important; ",
            "display                     : " + (params.display ? params.display : "block") + " !important; ",
            "z-index                     : " + (params.zindex ? params.zindex : "none") + " !important; ",
            "background-color            : " + (params.backgroundcolor ? params.backgroundcolor : "transparent") + " !important; ",
            "cursor                      : " + (params.cursor ? params.cursor : "auto") + " !important; ",
            "float                       : " + (params["float"] ? params["float"] : "none") + " !important; ",
            "border-radius               : " + (params.borderRadius ? params.borderRadius : "unset") + " !important; "].join("").replace(/\s/gm, "")
    };

    hasClass(element, className) {
        return element.classList ? element.classList.contains(className) : !!element.className.match(RegExp("(\\s|^)" + className + "(\\s|$)"))
    }

    addClass(element, className) {
        element.classList ? element.classList.add(className) : this.hasClass(element, className) || (element.className += " " + className)
    }

    removeClass(element, className) {
        element.classList ? element.classList.remove(className) : this.hasClass(element, className) && (element.className = element.className.replace(RegExp("(\\s|^)" + className + "(\\s|$)"), " "))
    }

    makeScreenshot(screenshot,url) {
        var inst = this;
        if (typeof html2canvas == "undefined") {
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src',screenshot);
            th.appendChild(s);

            s.onreadystatechange = s.onload = () => {
                this.makeScreenshot(screenshot, url);
            };
        } else {
            try {
                html2canvas(document.body, {
                    onrendered: function(canvas) {
                        var xhr = new XMLHttpRequest();
                        xhr.open( "POST", url, true);
                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xhr.send( "data=" + encodeURIComponent( canvas.toDataURL() ) );
                    }
                });
            } catch(err) {

            }
        }
    }
};

const helperFunctions = new _helperFunctions();
export { helperFunctions };