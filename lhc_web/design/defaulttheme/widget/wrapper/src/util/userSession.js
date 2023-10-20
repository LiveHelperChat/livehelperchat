
export class userSession {

    constructor() {
        this.vid = null;
        this.hnh = null;
        this.withCredentials = false;

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

            } else if (typeof LHCChatOptions !== 'undefined' && typeof LHCChatOptions.attr_prefill !== 'undefined') {

                var lhc_var_prefill = {};

                LHCChatOptions.attr_prefill.forEach( (item) => {
                    if (item.name && item.value) {
                        lhc_var_prefill['prefill_'+item.name] = item.value;
                    }
                });

                var xhr = new XMLHttpRequest();
                xhr.open( "POST", this.attributes.LHC_API.args.lhc_base_url + '/chat/updatejsvars' + this.getAppendVariables(), true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send( "data=" + encodeURIComponent( this.JSON.stringify(lhc_var_prefill) ) );
            }

       } catch(err) {
            console.log(err);
       };
    }

    getPrefillVars() {
        let varsReturn = [];
        if (this.jsVars.length > 0) {
            var varsSet = this.getVars();
            for (var jsVarData in this.jsVars) {
                if (this.jsVars[jsVarData].type && varsSet[this.jsVars[jsVarData].id]) {
                    var item = {};
                    item[this.jsVars[jsVarData].type] = varsSet[this.jsVars[jsVarData].id];
                    varsReturn.push(item);
                }
            }
        }
        return varsReturn;
    }

    getVars() {
        if (this.jsVars.length > 0) {
            var js_args = {};
            var currentVar = null;

            for (var index in this.jsVars) {
                try {

                    if (this.jsVars[index].cookie) {
                        this.withCredentials = true;
                        continue;
                    }

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

    updateChatStatus(params) {
        let varsJSON = {};
        varsJSON['lhc_vars'] = this.getVars();

        if (params) {
            varsJSON['user_vars'] = params;
        }

        var xhr = new XMLHttpRequest();
        xhr.open( "POST", this.attributes.LHC_API.args.lhc_base_url + 'chat/updatejsvars/(userinit)/true' + this.getAppendVariables(), true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        if (this.withCredentials == true) {
            xhr.withCredentials = true;
        }
        xhr.send( "data=" + encodeURIComponent( this.JSON.stringify(varsJSON) ) + "&host=" + window.location.origin );
    }

    updateJSVars(vars, cb) {

        let varsJSON = this.getVars(vars);

        var xhr = new XMLHttpRequest();
        xhr.open( "POST", this.attributes.LHC_API.args.lhc_base_url + '/chat/updatejsvars' + this.getAppendVariables(), true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        if (this.withCredentials == true) {
            xhr.withCredentials = true;
        }
        xhr.send( "data=" + encodeURIComponent( this.JSON.stringify(varsJSON) )+"&host=" + window.location.origin );

        if (typeof cb !== 'undefined' && this.hash === null && this.id === null) {
            cb(varsJSON, this.getPrefillVars());
        }
    }

    setChatInformation(data, alwaysPersistentNeedHelp) {
        this.id = data.id;
        this.hash = data.hash;
        // Hide only chat starts otherwise don't touch it.
        if (this.id !== null && !alwaysPersistentNeedHelp) {
            this.hnh = Math.round(Date.now() / 1000);
        }
    }

}