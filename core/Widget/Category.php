<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Widget;

use Piwik\Container\StaticContainer;
use Piwik\Plugin\Manager as PluginManager;

/**
 * Base type for metric metadata classes that describe aggregated metrics. These metrics are
 * computed in the backend data store and are aggregated in PHP when Piwik archives period reports.
 *
 * Note: This class is a placeholder. It will be filled out at a later date. Right now, only
 * processed metrics can be defined this way.
 */
class Category
{
    protected $name = '';

    /**
     * @var SubCategory[]
     */
    protected $subCategories = array();

    protected $order = 99;

    public function getOrder()
    {
        return $this->order;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->name;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function addSubCategory(SubCategory $subCategory)
    {
        $this->subCategories[$subCategory->getId()] = $subCategory;
    }

    public function hasSubCategory($subCategoryId)
    {
        return isset($this->subCategories[$subCategoryId]);
    }

    public function getSubCategory($subCategoryId)
    {
        if ($this->hasSubCategory($subCategoryId)) {
            return $this->subCategories[$subCategoryId];
        }
    }

    public function getSubCategories()
    {
        return $this->subCategories;
    }

    public function hasSubCategories()
    {
        return !empty($this->subCategories);
    }

    /** @return \Piwik\Widget\Category[] */
    private static function getAllCategories()
    {
        $manager = PluginManager::getInstance();
        // todo move to Piwik\Widget\Category
        $categories = $manager->findMultipleComponents('Widgets/Categories', '\\Piwik\\Widget\\Category');

        $instances = array();
        foreach ($categories as $category) {
            $cat = StaticContainer::get($category);
            $instances[$cat->getName()] = $cat;
        }

        return $instances;
    }

    /**
     * @return \Piwik\Widget\Category[]
     */
    public static function getAllCategoriesWithSubCategories()
    {
        $categories    = Category::getAllCategories();
        $subcategories = SubCategory::getAllSubCategories();

        // move subcategories into categories
        foreach ($subcategories as $subcategory) {
            $category = $subcategory->getCategory();

            if (!$category) {
                continue;
            }

            if (!isset($categories[$category])) {
                $categories[$category] = new Category();
                $categories[$category]->setName($category);
            }

            $categories[$category]->addSubCategory($subcategory);
        }

        return $categories;
    }
}