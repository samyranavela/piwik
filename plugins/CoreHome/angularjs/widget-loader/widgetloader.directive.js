/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/**
 * Example:
 * <div piwik-widget-loader="widget url"></div>
 */
(function () {
    angular.module('piwikApp').directive('piwikWidgetLoader', piwikWidgetLoader);

    piwikWidgetLoader.$inject = ['piwik', '$location', '$http', '$compile', '$q'];

    function piwikWidgetLoader(piwik, $location, $http, $compile, $q){
        return {
            restrict: 'A',
            transclude: true,
            scope: {
                piwikWidgetLoader: '='
            },
            templateUrl: 'plugins/CoreHome/angularjs/widget-loader/widgetloader.directive.html?cb=' + piwik.cacheBuster,
            compile: function (element, attrs) {

                return function (scope, element, attrs, ngModel) {
                    var changeCounter = 0,
                        currentScope,
                        currentElement,
                        httpCanceler,
                        contentNode = element.find('.theWidgetContent');

                    var cleanupLastWidgetContent = function() {
                        if (currentElement) {
                            currentElement.remove();
                            currentElement = null;
                        }
                        if (currentScope) {
                            currentScope.$destroy();
                            currentScope = null;
                        }
                    };

                    var abortHttpRequestIfNeeded = function () {
                        if (httpCanceler) {
                            httpCanceler.resolve();
                            httpCanceler = null;
                        }
                    }

                    function getFullWidgetUrl(url) {

                        // available in global scope
                        var search = $location.search();
                        url += '&idSite=' + search.idSite + '&period=' + search.period;
                        url += '&date=' + search.date + '&random=' + parseInt(Math.random() * 10000);

                        return url;
                    }

                    function loadWidgetUrl(url, thisChangeId)
                    {
                        scope.loading = true;

                        url = getFullWidgetUrl(url);

                        abortHttpRequestIfNeeded();
                        cleanupLastWidgetContent();

                        httpCanceler = $q.defer();

                        $http.get(url, {timeout: httpCanceler.promise}).success(function(response) {
                            if (thisChangeId !== changeCounter || !response) {
                                // another widget was requested meanwhile, ignore this response
                                return;
                            }

                            httpCanceler = null;

                            var newScope = scope.$new();
                            currentScope = newScope;

                            scope.loading = false;
                            scope.loadingFailed = false;

                            currentElement = contentNode.html(response).children();
                            $compile(currentElement)(newScope);

                        }).error(function () {
                            if (thisChangeId !== changeCounter) {
                                // another widget was requested meanwhile, ignore this response
                                return;
                            }

                            httpCanceler = null;

                            cleanupLastWidgetContent();

                            scope.loading = false;
                            scope.loadingFailed = true;
                        });
                    }

                    scope.$watch('piwikWidgetLoader', function (newUrl, oldUrl) {
                        if (newUrl) {
                            loadWidgetUrl(newUrl, ++changeCounter);
                        }
                    });

                    element.on('$destroy', function() {
                        abortHttpRequestIfNeeded();
                    });
                };
            }
        };
    }
})();