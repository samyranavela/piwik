<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugin\Report;
use Piwik\Piwik;

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
        return Piwik::translate($this->name);
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function addSubCategory(SubCategory $subCategory)
    {
        $this->subCategories[$subCategory->getName()] = $subCategory;
    }

    public function hasSubCategory($subCategoryName)
    {
        return isset($this->subCategories[$subCategoryName]);
    }

    public function getSubCategory($subCategoryName)
    {
        return $this->subCategories[$subCategoryName];
    }

    public function getSubCategories()
    {
        return $this->subCategories;
    }

    /** @return \Piwik\Plugin\Report\Category[] */
    public static function getAllCategories()
    {
        $categories = \Piwik\Plugin\Manager::getInstance()->findMultipleComponents('Reports/Categories', '\\Piwik\\Plugin\\Report\\Category');

        $instances = array();
        foreach ($categories as $category) {
            $instances[] = new $category;
        }

        return $instances;
    }
}