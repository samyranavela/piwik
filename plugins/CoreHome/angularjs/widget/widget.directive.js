/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/**
 * Example:
 * <div piwik-widget widget="widget"></div>
 */
(function () {
    angular.module('piwikApp').directive('piwikWidget', piwikWidget);

    piwikWidget.$inject = ['piwik'];

    function piwikWidget(piwik){
        return {
            restrict: 'A',
            scope: {
                widget: '='
            },
            templateUrl: 'plugins/CoreHome/angularjs/widget/widget.directive.html?cb=' + piwik.cacheBuster
        };
    }
})();