lhcAppControllers.controller('TrItemCtrl',['$scope','$http','$location','$rootScope', '$log', function($scope, $http, $location, $rootScope, $log) {

    this.languages = [];
    this.dialects = [];

    var that = this;

    this.move = function(element, offset) {
        index = that.languages.indexOf(element);
        newIndex = index + offset;
        if (newIndex > -1 && newIndex < that.languages.length){
            removedElement = that.languages.splice(index, 1)[0];
            that.languages.splice(newIndex, 0, removedElement)
        }
    };

    this.addLanguage = function() {
        that.languages.push({
            'message' : '',
            'languages' : []});
        setTimeout(function () {
            $('#tritems-tabs a:first').tab('show');
        },250);
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
        $('#tritems-tabs a:first').tab('show');
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

}]);