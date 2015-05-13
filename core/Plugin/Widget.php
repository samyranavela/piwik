<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugin;

use Piwik\Development;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\WidgetsList;

/**
 * Base class of all plugin widget providers. Plugins that define their own widgets can extend this class to easily
 * add new widgets or to remove widgets defined by other plugins.
 *
 * For an example, see the {@link https://github.com/piwik/piwik/blob/master/plugins/ExamplePlugin/Widgets.php} plugin.
 *
 * @api
 */
class Widget
{
    protected $category = '';
    protected $module = '';
    protected $action = '';

    /**
     * @ignore
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function getModule()
    {
        if (empty($this->module)) {
            $parts = $this->getClassNameParts();

            $this->module = $parts[2];
        }

        return $this->module;
    }

    public function getAction()
    {
        if (empty($this->action)) {
            $parts = $this->getClassNameParts();

            if (count($parts) >= 4) {
                $this->action = lcfirst(end($parts));
            }
        }

        return $this->action;
    }

    private function getClassNameParts()
    {
        $classname = get_class($this);
        return explode('\\', $classname);
    }

    /**
     * @return \Piwik\Plugin\Widget[]
     * @ignore
     */
    public static function getAllWidgets()
    {
        $widgetClasses = PluginManager::getInstance()->findMultipleComponents('Widgets', 'Piwik\\Plugin\\Widget');

        $widgets = array();
        foreach ($widgetClasses as $widgetClass) {
            $widgets[] = new $widgetClass();
        }

        return $widgets;
    }

    /**
     * @ignore
     * @return Widgets|null
     */
    public static function factory($module, $action)
    {
        if (empty($module) || empty($action)) {
            return;
        }

        $pluginManager = PluginManager::getInstance();

        try {
            if (!$pluginManager->isPluginActivated($module)) {
                return;
            }

            $plugin = $pluginManager->getLoadedPlugin($module);
        } catch (\Exception $e) {
            // we are not allowed to use possible widgets, plugin is not active
            return;
        }

        // the widget class implements such an action, but we have to check whether it is actually exposed and whether
        // it was maybe disabled by another plugin, this is only possible by checking the widgetslist, unfortunately
        if (!WidgetsList::isDefined($module, $action)) {
            return;
        }

        /** @var Widget[] $widgetContainer */
        $widgets = $plugin->findMultipleComponents('Widgets', 'Piwik\\Plugin\\Widget');

        foreach ($widgets as $widget) {
            if ($widget->getAction() == $action) {
                return $widget;
            }
        }
    }

}
