<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\UserCountry\Widgets\SubCategories;

use Piwik\Widget\SubCategory;

class LocationsSubCategory extends SubCategory
{
    protected $category = 'General_Visitors';
    protected $name = 'UserCountry_SubmenuLocations';
    protected $order = 25;

}
