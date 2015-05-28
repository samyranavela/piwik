<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CoreHome\Widgets\SubCategories;

use Piwik\Widget\SubCategory;

class DevicesSubCategory extends SubCategory
{
    protected $category = 'General_Visitors';
    protected $name = 'DevicesDetection_Devices';
    protected $order = 15;

}
