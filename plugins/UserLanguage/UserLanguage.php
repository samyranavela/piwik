<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\UserLanguage;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\FrontController;

/**
 *
 */
class UserLanguage extends \Piwik\Plugin
{
    public function postLoad()
    {
        Piwik::addAction('Template.footerUserCountry', array('Piwik\Plugins\UserLanguage\UserLanguage', 'footerUserCountry'));
    }

    public static function footerUserCountry(&$out)
    {
        /** @var FrontController $frontController */
        $frontController = StaticContainer::get('Piwik\FrontController');

        $out .= '<div><h2>' . Piwik::translate('UserLanguage_BrowserLanguage') . '</h2>';
        $frontController->fetchDispatch('UserLanguage', 'getLanguage');
        $out .= '</div>';
    }
}