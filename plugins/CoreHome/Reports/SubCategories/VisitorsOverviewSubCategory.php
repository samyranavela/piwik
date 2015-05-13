<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CoreHome\Reports\SubCategories;

use Piwik\Plugin\Report;
use Piwik\Plugin\Report\SubCategory;

class VisitorsOverviewSubCategory extends SubCategory
{
    protected $category = 'Visitors';
    protected $name = 'Overview';
    protected $reports = array();
    protected $sparkline = array('module' => 'VisitsSummary', 'action' => 'sparklines');

    public function __construct()
    {
        $this->evolution = Report::factory('VisitsSummary', 'get');
    }

}
