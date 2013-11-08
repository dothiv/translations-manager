angular.module('transconv').controller('StepTwoController', ['$scope', '$http', 'fileReader', '$resource', function($scope, $http, fileReader, $resource) {
        var REGEX_KEY = /^([a-zA-Z][a-zA-Z0-9]*\.)*[a-zA-Z][a-zA-Z0-9]*$/g;

        function setKey(base, path, val) {
            key = path.split('.');
            // check if path contains no dot anymore
            if (key[0] == path || !angular.isObject(base)) {
                // check if there's already a value
                if (!angular.isObject(base) || key[0] in base) {
                    return false;
                }
                // set the value, then we are done
                base[key[0]] = val;
                return true;
            }
            // make sure base contains key[0]
            if (!(key[0] in base))
                base[key[0]] = {};
            // recursive call with new base and shortened path
            if (setKey(base[key[0]], path.split('.').splice(1).join('.'), val)) {
                // success
                return base;
            } else {
                // error
                return false;
            }
        }

        function getJsonFromCsv(data, keyCol, valCol) {
            var csv = $.csv.toArrays(data);
            var keys = [];
            var vals = {};
            var errors = [];
            var tree = {};

            angular.forEach(csv, function(row) {
                var key = row[keyCol];
                var val = row[valCol];
                keys.push();
                if (!key.match(REGEX_KEY))
                    errors.push('The key "' + key + '" is invalid. Please only use A-z, 0-9, dots for seperation and no numbers in the beginning or after dots.');
                if (key in vals)
                    errors.push('The key "' + key + '" is used twice.');
                vals[key] = val;
            });

            keys.sort();

            angular.forEach(vals, function(val, key) {
                if (!key.match(REGEX_KEY)) return;
                if (!setKey(tree, key, val))
                    errors.push('A prefix of the key "' + key + '" is already in use.');
            });

            return {tree: tree, errors: errors};
        };

        function processCsvFile(csv) {
            $scope.changeset.translations = {};
            $scope.csverrors = [];
            angular.forEach({'de':2, 'en':3}, function(valCol, locale) {
                var result = getJsonFromCsv(csv, 1, valCol);
                $scope.changeset.translations[locale] = result.tree;
                $scope.csverrors = $scope.csverrors.concat(result.errors);
            });
        }

        function diff() {
            $http.post('diff', $scope.changeset)
                .success(function(data, status, headers, config) {
                    $scope.diff = JSON.parse(data);
                })
                .error(function(data, status, headers, config) {
                    // TODO
                })
        };

        $scope.commit = function() {
            $http.post('commit', $scope.changeset)
                .success(function(data, status, headers, config) {
                    $scope.log = JSON.parse(data);
                    $scope.commited = true;
                })
                .error(function(data, status, headers, config) {
                    // TODO
                })
        }

        $scope.$watch('changeset.translations', function() {
            if (!('files' in $scope.changeset))
                $scope.changeset.files = {};
            angular.forEach($scope.changeset.translations, function(translations, locale) {
                $scope.changeset.files[locale] = JSON.stringify(translations, undefined, 2)
            });
        })

        $scope.$watch('csverrors', function() {
            if (angular.isArray($scope.csverrors) && $scope.csverrors.length == 0 && $scope.file) {
                $scope.diff = diff();
            }
        });

        $scope.$watch('charityfile', function() {
            $scope.target = 'Charity';
            $scope.file = $scope.charityfile;
        });

        $scope.$watch('companyfile', function() {
            $scope.target = 'Company';
            $scope.file = $scope.companyfile;
        });

        $scope.$watch('file', function() {
            if ($scope.file)
                $scope.file.then(function (result) {
                    processCsvFile(result);
                });
            else
                $scope.target = false;
        });

        $scope.ignoreCsvError = function(id) {
            $scope.csverrors.splice(id,1);
        };

        $scope.ignoreCsvErrors = function() {
            $scope.csverrors = [];
        };


        $scope.presets = [
            { name: "somebody else" },
            { name: "Nils Wisiol",    email: "nils.wisiol@dothiv.org" },
            { name: "Benedikt Budig", email: "bb@dothiv.org" },
            { name: "Andre Löffler",  email: "info@andre-loeffler.net" },
            { name: "Greta Jeske",    email: "gj@dothiv.org"},
        ];

        $scope.$watch('preset', function() {
            if ('email' in $scope.preset) {
                $scope.changeset.name = $scope.preset.name;
                $scope.changeset.email = $scope.preset.email;
                $scope.isPreset = true;
            } else {
                $scope.changeset.name = $scope.changeset.email = '';
                $scope.isPreset = false;
            }
        });

        $scope.preset = $scope.presets[0];
        $scope.limit = 5;
        $scope.isPreset = false;
        $scope.changeset = {};
        $scope.commited = false;

}]);