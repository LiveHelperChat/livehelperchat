services.factory('CannedReplaceCtrlFactory', ['$http','$q',function ($http, $q) {
    this.getPlayerClasses = function(id) {
        var deferred = $q.defer();
        $http.get(WWW_DIR_JAVASCRIPT + 'sollutiumcrm/getpclasses/'+id).then(function(data) {
            deferred.resolve(data.data);
        },function() {
            deferred.reject('error');
        });
        return deferred.promise;
    };

    this.saveBonus = function(id,data) {
        var deferred = $q.defer();
        $http.post(WWW_DIR_JAVASCRIPT + 'sollutiumcrm/savebonus/' + id,data).then(function(data) {
            if (typeof data.data.error_url !== 'undefined') {
                document.location = data.data.error_url;
            } else {
                deferred.resolve(data.data);
            }
        },function(){
            deferred.reject('error');
        });
        return deferred.promise;
    };

    return this;
}]);


lhcAppControllers.controller('CannedReplaceCtrl',['$scope','$http','$location','$rootScope', '$log','$window','CannedReplaceCtrlFactory', function($scope, $http, $location, $rootScope, $log, $window, CannedReplaceCtrlFactory) {

    this.combinations = [];
    this.player_classes = [];
    this.department_id = null;
    this.bonus_id = null;
    this.name = null;
    this.disable_in_chat = null;

    // Are we in read only mode
    this.readOnly = false;

    // Logical attributes
    this.changes_saved = false;
    this.error_list = [];

    var that = this;

    // What type of transactions combination to add
    this.transaction_add = null;
    this.condition_add = null;

    this.orSumStarted = null;
    this.orSumData = null;

    this.makeid = function(length) {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    this.setConditions = function() {
        this.combinations = $window['replaceConditions'];
    }

    this.addTransactionGroup = function(bonus) {
        bonus.transactions.push({'_id': this.makeid(10), name: 'Group ' +  parseInt(bonus.transactions.length+1), type: this.transaction_add, transactions : []});
    }

    this.addConditionGroup = function (bonus) {
        bonus.conditions.push({'_id': this.makeid(10), name: 'Group ' +  parseInt(bonus.conditions.length+1),type: this.condition_add, conditions : []});
    }

    this.addTransactionItem = function (transactionGroup, type) {
        transactionGroup.transactions.push({type:type,logic:'and',number:1});
    }

    this.addConditionItem = function (conditionGroup, type) {
        conditionGroup.conditions.push({type:type,'logic':'and'});
    }

    this.deleteTransactionGroupItem = function (transactionItem, transactionGroup) {
        if (confirm('Are you sure?')){
            transactionGroup.transactions.splice( transactionGroup.transactions.indexOf(transactionItem), 1);
        }
    }

    this.deleteConditionGroupItem = function (transactionItem, transactionGroup) {
        if (confirm('Are you sure?')){
            transactionGroup.conditions.splice( transactionGroup.conditions.indexOf(transactionItem), 1);
        }
    }

    this.deleteElement = function (element,list) {
        if (confirm('Are you sure?')){
            list.splice(list.indexOf(element), 1);
        }
    }

    this.move = function(element, list, offset) {
        index = list.indexOf(element);
        newIndex = index + offset;
        if (newIndex > -1 && newIndex < list.length){
            removedElement = list.splice(index, 1)[0];
            list.splice(newIndex, 0, removedElement)
        }
    };

    this.addCombination = function() {
        that.combinations.push({
            'conditions' : [],
            'value' : '',
            'priority' : 0,
        });
    };

    /*this.addCombination = function (bonus) {

        if (typeof bonus.combinations === 'undefined') {
            bonus.combinations = [];
        }

        bonus.awardoptions.push({
            'value' : '',
            'conditions' : [],
        });
    }*/

    this.addAwardOr = function (bonus) {

        if (typeof bonus.awardoptions === 'undefined') {
            bonus.awardoptions = [];
        }

        bonus.awardoptions.push({
            'bonus_type' : 'OR'
        });
    }

    this.addCondition = function(award) {
        award.conditions.push({type:award.condition_add});
    }

    this.getNamesPclass = function (classIds) {
        var selected = [];
        angular.forEach(this.player_classes, function(pclass){
            if (classIds.indexOf(pclass.id) !== -1) {
                selected.push(pclass.name);
            }
        });

        if (selected.length > 0){
            return '['+selected.join(', ')+']';
        } else {
            return null;
        }
    }

    this.deleteField = function(field) {
        that.bonusoptions.splice(that.bonusoptions.indexOf(field),1);
    };

    this.moveUp = function(field,list) {
        that.move(field,list,-1);
    }

    this.moveDown = function(field,list) {
        that.move(field,list,1);
    }

    $scope.$watch('bonusctrl.department_id', function(newVal,oldVal) {
        if (newVal != oldVal) {
            that.loadPlayerClasses();
        };
    });

    this.saveBonus = function () {
        if (this.readOnly === false){
            SolutiumBonusFactory.saveBonus(this.bonus_id,{dep_id: this.department_id,data_bonus:this.bonusoptions, name:this.name, disable_in_chat: this.disable_in_chat}).then(function(data){

                if (data.error == false) {
                    if (that.bonus_id === null) {
                        document.location = WWW_DIR_JAVASCRIPT + 'sollutiumcrm/newbonus/' + data.data.id;
                    } else {
                        that.bonus_id = data.data.id;
                    }
                    that.changes_saved = true;
                    that.error_list = [];
                } else {
                    that.changes_saved = false;
                    that.error_list = data.data;
                }
            })
        } else {
            alert("You can't save this bonus changes!");
        }
    }

    this.loadPlayerClasses = function () {
        SolutiumBonusFactory.getPlayerClasses(this.department_id).then(function(data){
            that.player_classes = data;
        },function(error) {
            alert('We could not change your status!');
        });
    }

    this.init = function () {
        if (this.department_id > 0) {
            this.loadPlayerClasses();
        }
    };
}]);