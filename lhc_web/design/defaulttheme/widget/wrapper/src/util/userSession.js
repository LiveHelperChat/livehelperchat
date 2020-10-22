
export class userSession {

    constructor() {
        this.vid = null;
        this.hnh = null;

        this.attributes = {};
        this.ref = null;
        this.id = null;
        this.hash = null;
        this.jsVars = [];
        this.updateVarsTimeout = null;
        this.JSON = {
            parse: window.JSON && (window.JSON.parse || window.JSON.decode) || String.prototype.evalJSON && function(str){return String(str).evalJSON();} || $.parseJSON || $.evalJSON,
            stringify:  Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON
        };
    }

    setAttributes(attributes) {
        this.attributes = attributes;
    }

    setSessionReferrer(ref) {
        this.ref = ref;
    }

    getSessionReferrer() {
        return this.ref;
    }

    getVID() {
        return this.vid;
    }

    setVID(vid) {
        this.vid = vid;
    }

    setSessionInformation(params) {
        this.vid = params['vid'] || null;
        this.hnh = params['hnh'] || null;
        this.hash = params['hash'] || null;
        this.id = params['id'] || null;
    }

    getSessionAttributes() {
        var attr = {};

        if (this.vid !== null) {
            attr['vid'] = this.vid;
        }

        if (this.hnh !== null) {
            attr['hnh'] = this.hnh;
        }

        if (this.id !== null) {
            attr['id'] = this.id;
        }

        if (this.hash !== null) {
            attr['hash'] = this.hash;
        }

        return attr
    }

    setupVarsMonitoring(jsVars, cb) {
        this.jsVars = jsVars;

        // Try to monitor variable if it's lhc_var
        try {
            if (this.attributes.lhc_var !== null)
            {

                var validator = {
                    set: (obj, prop, value) => {
                        // The default behavior to store the value
                        obj[prop] = value;

                        clearTimeout(this.updateVarsTimeout);
                        this.updateVarsTimeout = setTimeout( () =>{ this.updateJSVars(obj, cb);  },1000);

                        // Indicate success
                        return true;
                    }
                };

                this.attributes.lhc_var = new Proxy(this.attributes.lhc_var,validator);

                // Convert argument or global lhc_var to a reference
                if (this.attributes.LHC_API.args.lhc_var) {
                    this.attributes.LHC_API.args.lhc_var = this.attributes.lhc_var;
                } else if (lhc_var) {
                    lhc_var = this.attributes.lhc_var;
                }

                // Update vars initially
                this.updateJSVars(this.attributes.lhc_var);
            }
       } catch(err) {
            console.log(err);
       };

    }

    getVars() {
        if (this.jsVars.length > 0) {
            var js_args = {};
            var currentVar = null;

            for (var index in this.jsVars) {
                try {

                    if (this.jsVars[index].var.indexOf('lhc_var.') !== -1) {
                        currentVar = this.attributes.lhc_var[this.jsVars[index].var.replace('lhc_var.','')] || null;
                    } else {
                        currentVar = eval(this.jsVars[index].var);
                    }

                    if (typeof currentVar !== 'undefined' && currentVar !== null && currentVar !== '') {
                        js_args[this.jsVars[index].id] = currentVar;
                    }
                } catch(err) {

                }
            }

            return js_args;
        }
        return null;
    }

    getAppendVariables(){
        var append = '';

        if (this.vid !== null) {
            append = append + '/(vid)/' + this.vid ;
        }

        if (this.hash !== null && this.id !== null) {
            append = append + '/(hash)/' + this.id  + '_' + this.hash;
        }

        return append;
    }

    updateJSVars(vars, cb) {

        let varsJSON = this.getVars(vars);

        var xhr = new XMLHttpRequest();
        xhr.open( "POST", this.attributes.LHC_API.args.lhc_base_url + '/chat/updatejsvars' + this.getAppendVariables(), true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send( "data=" + encodeURIComponent( this.JSON.stringify(varsJSON) ) );

        if (typeof cb !== 'undefined' && this.hash === null && this.id === null) {
            cb(varsJSON);
        }
    }

    setChatInformation(data) {
        this.id = data.id;
        this.hash = data.hash;
        this.hnh = Math.round(Date.now() / 1000);
    }

}