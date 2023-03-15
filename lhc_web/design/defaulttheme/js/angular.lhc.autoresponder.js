lhcAppControllers.controller('AutoResponderCtrl',['$scope','$http','$location','$rootScope', '$log', '$window', function($scope, $http, $location, $rootScope, $log, $window) {

    this.languages = [];
    this.dialects = [];
    this.departments = [];
    this.ignoreLanguages = {
        'languages' : []
    };

    var that = this;

    this.move = function(element, offset) {
        index = that.languages.indexOf(element);
        newIndex = index + offset;
        if (newIndex > -1 && newIndex < that.languages.length){
            removedElement = that.languages.splice(index, 1)[0];
            that.languages.splice(newIndex, 0, removedElement)
        }
    };

    this.setDialects = function() {
        this.dialects = $window['languageDialects'];
        this.departments = $window['replaceDepartments'];
    }

    this.setIgnoreLanguages = function() {
        this.ignoreLanguages.languages = $window['autoResponderLanguagesIgnore'];
    }

    this.setLanguages = function() {
        this.languages = $window['autoResponderLanguages'];
    }

    this.initController = function() {
        that.dialects = $window['languageDialects'];
    }

    this.addLanguage = function() {
        that.languages.push({
            'message' : '',
            'fallback_message' : '',
            'dep_id': "0",
            'dep_ids': [],
            'languages' : []});

        setTimeout(function () {
            $('#autoresponder-tabs li:eq(' + (that.languages.length+3) + ') a').tab('show');
        },250);

        setTimeout(function(){
            $('.btn-block-department').makeDropdown();
        },1000);
    };

    this.toggleSelection =  function toggleSelection(lang, language) {
        var idx = lang.languages.indexOf(language);
        // Is currently selected
        if (idx > -1) {
            lang.languages.splice(idx, 1);
        } else {
            lang.languages.push(language);
        }
    };

    this.deleteLanguage = function(field) {
        that.languages.splice(that.languages.indexOf(field),1);
        $('#autoresponder-tabs a:first').tab('show');
    };

    this.moveLeftField = function(field) {
        that.move(field,-1);
    }

    this.moveRightField = function(field) {
        that.move(field,1);
    }

    this.getLanguagesChecked = function (lang) {

        var shortCode = [];

        lang.languages.forEach(function(item) {
            if (item.length == 2) {
                shortCode.push(item);
            }
        });

        var _that = this;

        lang.dep_ids && lang.dep_ids.forEach(function(item) {
            shortCode.push(_that.departments[item]);
        });

        return shortCode.length > 0 ? shortCode.join(', ') : lang.languages.join(', ');
    }

    this.isSelectedDialect = function(lang, dialect) {

        if (typeof lang.dialect === 'undefined') {
            lang.dialect = [];
        }

        var allChecked = true;

        dialect.items.forEach(function(item){
            if (lang.languages.indexOf(item.lang_code) === -1 || (item.short_code != '' && lang.languages.indexOf(item.short_code) === -1)){
                allChecked = false;
            }
        });

        lang.dialect[dialect.lang.id] = allChecked;
    }

    this.changeSelection = function (lang, dialect) {

        if (lang.dialect[dialect.lang.id] === false) {
            dialect.items.forEach(function(item){
                if (item.short_code != '' && lang.languages.indexOf(item.short_code) === -1) {
                    lang.languages.push(item.short_code);
                }

                if (lang.languages.indexOf(item.lang_code) === -1){
                    lang.languages.push(item.lang_code);
                }
            });
            // Unchecked
        } else {
            dialect.items.forEach(function(item){
                var idx = null;

                if (item.short_code != '') {
                    idx = lang.languages.indexOf(item.short_code);
                    if (idx > -1) {
                        lang.languages.splice(idx, 1);
                    }
                }

                idx = lang.languages.indexOf(item.lang_code);
                if (idx > -1) {
                    lang.languages.splice(idx, 1);
                }
            });
        }
    }


    this.addOption = function(element) {
        this.addDepartment(element)
    }

    this.addDepartment = function(combination){

        if (!combination.dep_ids) {
            combination.dep_ids = [];
        }

        if (combination.dep_ids.indexOf(combination.dep_id) == -1) {
            combination.dep_ids.push(combination.dep_id);
        }
    }

    this.deleteElement = function (element,list) {
        if (confirm('Are you sure?')){
            list.splice(list.indexOf(element), 1);
        }
    }
    

}]);