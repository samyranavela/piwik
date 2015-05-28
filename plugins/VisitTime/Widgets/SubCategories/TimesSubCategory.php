<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\VisitTime\Widgets\SubCategories;

use Piwik\Widget\SubCategory;

class TimesSubCategory extends SubCategory
{
    protected $category = 'General_Visitors';
    protected $name = 'VisitTime_SubmenuTimes';
    protected $order = 35;

}
