
export class storageHandler {
    constructor(global, domain, prefix, cookieEnabled) {
        this.window = global;
        this.cookieEnabled = false;

        // Have we checked cookies support
        this.cookiesSupportChecked = false;

        // We should try to enable cookie
        if (cookieEnabled == true) {
            this.checkCookiesSupport();
        }

        this.domain = domain;
        this.secureCookie = false;
        this.cookiePerPage = false;
        this.prefix = prefix || 'lhc';
    }

    checkCookiesSupport() {

        if (this.cookiesSupportChecked === true) {
            return this.cookieEnabled;
        }

        this.cookiesSupportChecked = true;

        try {
            this.hasSessionStorage = !!this.window.sessionStorage;
        } catch (e){
            this.hasSessionStorage = false;
        }

        try {
            this.hasLocalStorage = !!this.window.localStorage;
        } catch (e){
            this.hasLocalStorage = false;
        }

        try {
            // Create cookie
            document.cookie = 'cookietest=1';
            this.cookieEnabled = document.cookie.indexOf('cookietest=') !== -1;
            // Delete cookie
            document.cookie = 'cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT';
        } catch (e) {

        }

        return this.cookieEnabled;
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
        if (this.cookieEnabled === false) return ;
        var string = "";
        expireTime || (expireTime = new Date, expireTime.setTime(expireTime.getTime() + 15552E6), string = "; expires=" + expireTime.toGMTString());
        document.cookie = coookieName + "=" + cookieValue + string + (this.cookiePerPage === false ? "; path=/" : (this.cookiePerPage === true ? "" : '; path=' + this.cookiePerPage)) + (this.secureCookie ? ";secure" : "") + (domain ? ";domain=" + domain : "") + ";SameSite=Lax";
    };

    getHTTPCookie(cookieName) {
        if (this.cookieEnabled === false) return [];
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

    setLocalStorage(key, value) {
        if (this.hasLocalStorage && localStorage.setItem) try {
            localStorage.setItem(key, value)
        } catch (d) {
        }
    }

    getLocalStorage(a) {
        return this.hasLocalStorage && localStorage.getItem ?
            localStorage.getItem(a) : null
    }

    removeLocalStorage(key) {
        if (this.hasLocalStorage && localStorage.removeItem) try {
            localStorage.removeItem(key)
        } catch (d) {
        }
    }

    setSessionReferer(ref) {
        if (!this.getSessionStorage(this.prefix+'_ref')) {
            this.setSessionStorage(this.prefix+'_ref',ref);
        }
    }

    getSessionReferrer() {
        return this.getSessionStorage(this.prefix+'_ref');
    }

    getSessionInformation() {
        var a;
        this.sessionInformation && (a = this.sessionInformation);
        a || (a = this.getHTTPCookie(this.prefix + "_per")[0]);
        return this.parseSessionInformation(a)
    };

    getStoreValue(sessionInformation)
    {
        let pairs = [];
        Object.keys(sessionInformation).forEach(key => {
            let value = sessionInformation[key];
            pairs.push(key + '|' + value);
        });
        return pairs.join('|');
    }

    storeSessionInformation(sessionInformation) {
        this.setHTTPCookie(this.prefix+"_per", this.getStoreValue(sessionInformation), false, this.getCookieDomain());
        this.sessionInformation = sessionInformation;
    };

    parseSessionInformation(content) {
        // Check was it stored as our format or JSON.
        if (content && content.indexOf('|') !== -1) {
            let contentReturn = {};
            let parts = content.split('|');

            for (var i = 0; i < parts.length / 2; i++) {
                contentReturn[parts[i * 2]] = parts[(i * 2) + 1];
            }

            return contentReturn;
        } else {
            if (content) {
                return JSON.parse(unescape(content))
            } else {
                return {};
            }
        }

    };
}

