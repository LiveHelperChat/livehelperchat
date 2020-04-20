
export class storageHandler {
    constructor(global, domain) {
        this.window = global;
        this.isCookieEnabled = this.checkCookieEnabled();
        this.hasSessionStorage = this.isCookieEnabled && !!this.window.sessionStorage;
        this.domain = domain;
        this.secureCookie = false;
        this.cookiePerPage = false;
    }

    setCookieDomain(domain) {
        this.domain = domain;
    }

    setCookiePerPage(cookiePerPage) {
        this.cookiePerPage = cookiePerPage;
    }

    getCookieDomain() {
        if (this.domain) {
            return '.' + this.domain;
        } else {
            return null;
        }
    }

    setSecureCookie(status) {
        this.secureCookie = status;
    }

    setHTTPCookie(coookieName, cookieValue, expireTime, domain) {
        var string = "";
        expireTime || (expireTime = new Date, expireTime.setTime(expireTime.getTime() + 15552E6), string = "; expires=" + expireTime.toGMTString());
        document.cookie = coookieName + "=" + cookieValue + string + (this.cookiePerPage === false ? "; path=/" : (this.cookiePerPage === true ? "" : '; path=' + this.cookiePerPage)) + (this.secureCookie ? ";secure" : "") + (domain ? ";domain=" + domain : "");
    };

    getHTTPCookie(cookieName) {
        var b, d, k = [], baseCookie = cookieName + "=", e = document.cookie.split(";");
        cookieName = 0;
        for (b = e.length; cookieName < b; cookieName++) {
            for (d = e[cookieName]; " " === d.charAt(0);) d = d.substring(1, d.length);
            0 === d.indexOf(baseCookie) && k.push(d.substring(baseCookie.length, d.length))
        }

        // Reset duplicate cookies
        if (k.length == 2) {
            let host = (window.location.hostname || document.location.host),
            reset = baseCookie + "0;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
            if (host) {
                document.cookie = reset;
                document.cookie = reset + "; domain=." + host;
            }
        }

        return k;
    };

    setSessionStorage(key, value) {
        if (this.hasSessionStorage && sessionStorage.setItem) try {
            sessionStorage.setItem(key, value)
        } catch (d) {
        }
    }

    removeSessionStorage(key) {
        if (this.hasSessionStorage && sessionStorage.removeItem) try {
            sessionStorage.removeItem(key)
        } catch (d) {
        }
    }

    getSessionStorage(a) {
        return this.hasSessionStorage && sessionStorage.getItem ?
            sessionStorage.getItem(a) : null
    }

    setSessionReferer(ref) {
        if (!this.getSessionStorage('lhc_ref')) {
            this.setSessionStorage('lhc_ref',ref);
        }
    }

    getSessionReferrer() {
        return this.getSessionStorage('lhc_ref');
    }

    getSessionInformation() {
        var a;
        this.sessionInformation && (a = this.sessionInformation);
        a || (a = this.getHTTPCookie("lhc_per")[0]);
        return this.parseSessionInformation(a)
    };

    storeSessionInformation(sessionInformation) {
             this.setHTTPCookie("lhc_per", JSON.stringify(sessionInformation), false, this.getCookieDomain());
            this.sessionInformation = sessionInformation;
    };

    parseSessionInformation(content) {
        if (content){
            return JSON.parse(decodeURIComponent(content))
        } else {
            return {};
        }
    };

    checkCookieEnabled() {
        var a = this.window.cookieEnabled ? !0 : !1;
        "undefined" != typeof this.window.cookieEnabled || a || (document.cookie = "testcookie", a = -1 != document.cookie.indexOf("testcookie") ? !0 : !1);
        return a
    };
}

