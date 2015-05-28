<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\API;


use Piwik\Piwik;
use Piwik\Report\ReportWidgetConfig;
use Piwik\Widget\Category;
use Piwik\Widget\SubCategory;
use Piwik\Widget\WidgetContainerConfig;
use Piwik\Widget\WidgetConfig;
use Piwik\Widget\WidgetsList;

class WidgetMetadata
{

    public function getPagesMetadata($idSite)
    {
        $widgetsList = WidgetsList::get($idSite);
        $categories  = $this->moveWidgetsIntoCategories($widgetsList->getWidgets());
        $categories  = $this->buildPagesMetadata($categories);

        return $categories;
    }

    public function getWidgetMetadata($idSite)
    {
        $list = WidgetsList::get($idSite);
        $flat = array();

        $categories = $this->moveWidgetsIntoCategories($list->getWidgets());

        foreach ($list->getWidgets() as $widgetConfig) {

            /** @var WidgetConfig[] $widgets */
            $widgets = array($widgetConfig);
            if ($widgetConfig instanceof WidgetContainerConfig) {
                $widgets = array_merge($widgets, $widgetConfig->getWidgetConfigs());
            }

            foreach ($widgets as $widget) {
                if (!$widget->isWidgetizeable() || !$widget->getName()) {
                    continue;
                }

                $category    = null;
                $subcategory = null;

                if (isset($categories[$widget->getCategory()])) {
                    $category    = $categories[$widget->getCategory()];
                    $subcategory = $category->getSubCategory($widget->getSubCategory());
                }

                $flat[] = $this->buildWidgetMetadata($widget, $category, $subcategory, $addNestedWidgets = false);
            }
        }

        usort($flat, array($this, 'sortWidgets'));

        return $flat;
    }

    private function buildWidgetMetadata(WidgetConfig $widget, $category, $subcategory, $addNestedWidgets = true)
    {
        $category = $category ? $this->buildCategoryMetadata($category) : null;
        $subcategory = $subcategory ? $this->buildSubCategoryMetadata($subcategory) : null;

        $item = array(
            'name'        => Piwik::translate($widget->getName()),
            'category'    => $category,
            'subcategory' => $subcategory,
            'module'      => $widget->getModule(),
            'action'      => $widget->getAction(),
            'order'       => $widget->getOrder(),
            'parameters'  => $this->buildWidgetParameters($widget),
            'widget_url'  => '?' . http_build_query($this->buildWidgetParameters($widget)),
            'uniqueId'    => $widget->getUniqueId(),
        );

        if ($widget instanceof ReportWidgetConfig) {
            // todo this is rather bad, there should be a method that is implemented by widgetConfig to add "configs".
            $item['viewDataTable'] = $widget->getDefaultView();
            $item['isReport'] = true;
        }

        if ($widget instanceof WidgetContainerConfig) {
            $item['layout'] = $widget->getLayout();
            $item['isContainer'] = true;

            if ($addNestedWidgets) {
                // todo we would extract that code into a method and reuse it with above
                $children = array();
                foreach ($widget->getWidgetConfigs() as $widgetConfig) {
                    $cat = $this->createCategoryForName($widgetConfig->getCategory());
                    $subcat = $this->createSubCategoryForName($widgetConfig->getCategory(), $widgetConfig->getSubCategory());

                    $child = $this->buildWidgetMetadata($widgetConfig, $cat, $subcat);
                    $children[] = $child;
                }
                $item['widgets'] = $children;
            }
        }

        return $item;
    }

    private function sortWidgets($widgetA, $widgetB) {
        $orderA = $widgetA['category']['order'];
        $orderB = $widgetB['category']['order'];

        if ($orderA === $orderB) {
            if (!empty($widgetA['subcategory']['order']) && !empty($widgetB['subcategory']['order'])) {

                $subOrderA = $widgetA['subcategory']['order'];
                $subOrderB = $widgetB['subcategory']['order'];

                if ($subOrderA === $subOrderB) {
                    return 0;
                }

                return $subOrderA > $subOrderB ? 1 : -1;

            } elseif (!empty($orderA)) {

                return 1;
            }

            return -1;
        }

        return $orderA > $orderB ? 1 : -1;
    }

    private function buildWidgetParameters(WidgetConfig $widget)
    {
        // todo this should be actually done in WidgetConfig
        return array('module' => $widget->getModule(),
            'action' => $widget->getAction(),
        ) + $widget->getParameters();
    }

    private function buildCategoryMetadata(Category $category)
    {
        return array(
            'name'  => Piwik::translate($category->getName()),
            'order' => $category->getOrder(),
            'id'    => $category->getId()
        );
    }

    private function buildSubCategoryMetadata(SubCategory $subcategory)
    {
        return array(
            'name'  => Piwik::translate($subcategory->getName()),
            'order' => $subcategory->getOrder(),
            'id'    => $subcategory->getId()
        );
    }

    /**
     * @param WidgetConfig[] $widgetConfigs
     * @return Category[]
     */
    private function moveWidgetsIntoCategories($widgetConfigs)
    {
        /** @var Category[] $all */
        $all = Category::getAllCategoriesWithSubCategories();

        // move reports into categories/subcategories and create missing ones if needed
        foreach ($widgetConfigs as $widgetConfig) {
            $category    = $widgetConfig->getCategory();
            $subcategory = $widgetConfig->getSubCategory();

            if (!$category) {
                continue;
            }

            if ($widgetConfig instanceof WidgetContainerConfig && !$widgetConfig->getWidgetConfigs()) {
                // if a container does not contain any widgets, ignore it
                continue;
            }

            if (!isset($all[$category])) {
                $all[$category] = $this->createCategoryForName($category);
            }

            if (!$subcategory) {
                continue;
            }

            if (!$all[$category]->hasSubCategory($subcategory)) {
                $all[$category]->addSubCategory($this->createSubCategoryForName($category, $subcategory));
            }

            $all[$category]->getSubCategory($subcategory)->addWidgetConfig($widgetConfig);
        }

        return $all;
    }

    private function createCategoryForName($categoryName)
    {
        $category = new Category();
        $category->setName($categoryName);
        return $category;
    }

    private function createSubCategoryForName($categoryName, $subCategoryName)
    {
        $subcategory = new SubCategory();
        $subcategory->setCategory($categoryName);
        $subcategory->setName($subCategoryName);
        return $subcategory;
    }

    /**
     * @param Category[] $categories
     * @return array
     */
    private function buildPagesMetadata($categories)
    {
        // todo should be sorted by order
        $pages = array();

        foreach ($categories as $category) {
            foreach ($category->getSubCategories() as $subcategory) {
                $page = $this->buildPageMetadata($category, $subcategory);

                if (!empty($page['widgets'])) {
                    $pages[] = $page;
                }
            }
        }

        return $pages;
    }

    private function buildPageMetadata(Category $category, SubCategory $subcategory)
    {
        $ca = array(
            'uniqueId' => $category->getName() . '.' . $subcategory->getName(),
            'category' => $this->buildCategoryMetadata($category),
            'subcategory' => $this->buildSubCategoryMetadata($subcategory),
            'widgets' => array()
        );

        foreach ($subcategory->getWidgetConfigs() as $config) {
            $widget = $this->buildWidgetMetadata($config, $cat = null, $subcat = null);
            unset($widget['category']);
            unset($widget['subcategory']);

            $ca['widgets'][] = $widget;
        }

        return $ca;
    }

}