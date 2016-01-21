(function() {
    'use strict';

    var initialized = false;

    angular.module('treeControlWidget', ['ui.tree',function() {
        }])
        .controller('treeControlWidgetController', ['$scope', '$http', function($scope, $http) {
            $scope.selectedItem = {};

            $scope.options = {
                dragStart: function(e) {
                    //debugger;
                },
                dragStop:  function(e) {
                    //debugger;
                    console.log(arguments);
                    var destItem = e.dest.nodesScope.$nodeScope ? e.dest.nodesScope.$nodeScope.item : null;
                    var element = e.source.nodeScope.item;
                    var originalParent = e.source.nodesScope.$nodeScope.item;

                    destItem.collapsed = false;
                    destItem.has_child_objects = false;

                    if('object' !== typeof destItem.items) {
                        destItem.items = [];
                    }

                    var parentItem = originalParent;
                    if ('object' === typeof parentItem.items) {
                        for (var i=0; i< parentItem.items.length; i++) {
                            if (parentItem.items[i].id === element.id) {
                                parentItem.items.splice(i, 1);
                                break;
                            }
                        }
                    }

                    element.parent = destItem.id;
                    if (e.dest.index >= 0) {
                        destItem.items.splice(e.dest.index - 1, 0, element);
                    } else {
                        destItem.items.push(element);
                    }

                    $scope.refreshValueByScopeList();
                }
            };

            $scope.removeItem = function(scope) {
                scope.item.deleted = 1;
                $scope.refreshValueByScopeList();
            };

            $scope.toggle = function(scope) {
                scope.toggle();
            };

            $scope.newSubItem = function(scope) {
                var title = prompt('Új menüpont neve');
                if (title) {
                    var newItem = {
                        id: '',
                        title: title,
                        items: [],
                        enabled: 1
                    };

                    if (scope && scope.$modelValue) {
                        scope.$modelValue.items.push(newItem);
                    } else {
                        $scope.list.push(newItem)
                    }

                    $scope.refreshValueByScopeList();
                }
            };

            $scope.promptRename = function(item) {
                var title = prompt('Menüpont elnevezése', item.title);
                if (title) {
                    item.title = title;
                    $scope.refreshValueByScopeList();
                }
            };

            $scope.toggleEnabled = function(item, value) {
                item.enabled = value;
                $scope.refreshValueByScopeList();
            };

            $scope.itemFilter = function(item) {
                return item.deleted ? false : true;
            };

            $scope.$watch('value', function(value){
                if (!initialized && typeof value === 'string') {
                    var parsedJSON = JSON.parse(value);
                    if (typeof parsedJSON === 'object') {
                        $scope.list = parsedJSON;
                        setTimeout(function() {
                            initialized = true;
                        }, 100);
                    }
                }
            });

            $scope.updatePositions = function(list) {
                for (var i=0; i < list.length; i++) {
                    list[i].position = i+1;
                    if (list[i].items && list[i].items.length){
                        $scope.updatePositions(list[i].items);
                    }
                }
            };

            $scope.buildEditUrl = function(item){
                return item.id ? unescape($scope.edit_url).replace('{id}', item.id) : false;
            };

            $scope.refreshValueByScopeList = function() {
                $scope.updatePositions($scope.list);
                $scope.value = JSON.stringify($scope.list);
            };

            $scope.init = function(){
                $scope.list = $scope.value;
            };

            $scope.loadChildren = function(item, url) {
                url = url.replace(/_ID_/g, item.id);
                item.loading = true;
                $http.get(url).success(function(res, headerGetter){
                    item.items = res;
                    item.loading = false;
                    if (0 === res.length) {
                        item.has_child_objects = false;
                    }
                });
            }



        }]).controller('treeControlWidgetItemController', ['$scope', function($scope){
            $scope.init = function() {
                var collapsed = $scope.$parent.item.collapsed;
                if (collapsed) {
                    $scope.collapse();
                } else {
                    $scope.expand();
                }

                $scope.$parent.$watch('collapsed', function(newValue, oldValue, $currentScope) {
                    $currentScope.item.collapsed = newValue;
                })
            }
        }]);
})();