<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Widget;

use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use \Piwik\Plugin\Manager as PluginManager;

/**
 * Base type for metric metadata classes that describe aggregated metrics. These metrics are
 * computed in the backend data store and are aggregated in PHP when Piwik archives period reports.
 *
 * Note: This class is a placeholder. It will be filled out at a later date. Right now, only
 * processed metrics can be defined this way.
 */
class SubCategory
{
    protected $category = '';
    protected $name = '';
    protected $id = '';

    /**
     * @var WidgetConfig[]
     */
    protected $widgets = array();

    protected $order = 99;

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function getWidgetConfigs()
    {
        return $this->widgets;
    }

    public function addWidgetConfig(WidgetConfig $widget)
    {
        $this->widgets[] = $widget;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        if (empty($this->id)) {
            return $this->name;
        }

        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /** @return \Piwik\Widget\SubCategory[] */
    public static function getAllSubCategories()
    {
        $subcategories = array();

        Piwik::postEvent('SubCategory.addSubCategories', array(&$subcategories));

        $manager = PluginManager::getInstance();
        $classes = $manager->findMultipleComponents('Widgets/SubCategories', '\\Piwik\\Widget\\SubCategory');

        foreach ($classes as $subcategory) {
            $subcategories[] = StaticContainer::get($subcategory);
        }

        return $subcategories;
    }
}