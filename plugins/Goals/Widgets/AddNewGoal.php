<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Goals\Widgets;

use Piwik\Common;
use Piwik\Plugins\Goals\API;
use Piwik\Widget\WidgetConfig;

class AddNewGoal extends \Piwik\Widget\Widget
{
    public static function configure(WidgetConfig $config)
    {
        $idSite = Common::getRequestVar('idSite', null, 'int');
        $goals  = API::getInstance()->getGoals($idSite);

        $config->setCategory('Goals_Goals');
        $config->setSubCategory('Goals_AddNewGoal');
        $config->setParameters(array('idGoal' => ''));
        $config->setIsNotWidgetizable();

        if (count($goals) !== 0) {
            $config->disable();
        }
    }
}
