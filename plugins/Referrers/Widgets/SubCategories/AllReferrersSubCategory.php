<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Referrers\Widgets\SubCategories;

use Piwik\Widget\SubCategory;

class AllReferrersSubCategory extends SubCategory
{
    protected $category = 'Referrers_Referrers';
    protected $name = 'Referrers_WidgetGetAll';
    protected $order = 5;

}
