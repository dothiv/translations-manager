var transconv = angular.module('transconv', ['ngResource'], function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

transconv.config(function($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: '/bundles/translationsmanager/partials/stepzero.html',
        controller: 'StepZeroController'
    })
    .when('/step1', {
        templateUrl: '/bundles/translationsmanager/partials/stepone.html',
        controller: 'StepOneController'
    })
    .when('/step2', {
        templateUrl: '/bundles/translationsmanager/partials/steptwo.html',
        controller: 'StepTwoController'
    });
});

transconv.directive("ngFileSelect",function(){
    return {
        link: function($scope,el) {
            el.bind("change", function(e) {
                $scope.file = (e.srcElement || e.target).files[0];
                $scope.getFile();
            });
        }
    };
});

transconv.directive("dhTextFileDrop",function(fileReader){
    return {
      restrict: 'A',
      link: function($scope, elem, attr) {
        var name = attr['dhTextFileDrop'];
        elem.bind('dragover', function (e) {
          e.stopPropagation();
          e.preventDefault();
          e.dataTransfer.dropEffect = 'copy';
        });
        elem.bind('dragenter', function(e) {
          e.stopPropagation();
          e.preventDefault();
          $scope.$apply(function() {
            $scope.divClass = 'on-drag-enter';
          });
        });
        elem.bind('dragleave', function(e) {
          e.stopPropagation();
          e.preventDefault();
          $scope.$apply(function() {
            $scope.divClass = '';
          });
        });
        elem.bind('drop', function(e) {
          var droppedFiles = e.dataTransfer.files;
          e.stopPropagation();
          e.preventDefault();
          $scope.$apply(function() {
              $scope[name] = fileReader.readAsText(droppedFiles[0], $scope);
          });
        });
      }
    };
});
