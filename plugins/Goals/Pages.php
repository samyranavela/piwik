<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Goals;


use Piwik\API\Request;
use Piwik\Common;
use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Evolution;
use Piwik\Plugins\CoreVisualizations\Visualizations\Sparklines;
use Piwik\Widget\WidgetContainerConfig;
use Piwik\Widget\WidgetConfig;
use Piwik\Report\ReportWidgetFactory;

class Pages
{
    private $orderId = 0;
    private $allReports = array();
    private $factory = array();

    public function __construct(ReportWidgetFactory $reportFactory, $reportsWithGoalMetrics)
    {
        $this->factory = $reportFactory;
        $this->allReports = $reportsWithGoalMetrics;
    }

    /**
     * @param array $goals
     * @return WidgetConfig[]
     */
    public function createGoalsOverviewPage($goals)
    {
        $widgets = array();

        $config = $this->factory->createWidget();
        $config->forceViewDataTable(Evolution::ID);
        $config->setSubCategory('General_Overview');
        $config->setName('General_EvolutionOverPeriod');
        $config->setAction('getEvolutionGraph');
        $config->setOrder(++$this->orderId);
        $config->setParameters(array('columns' => 'nb_conversions'));
        $config->setIsNotWidgetizable();
        $widgets[] = $config;

        $config = $this->factory->createWidget();
        $config->forceViewDataTable(Sparklines::ID);
        $config->setSubCategory('General_Overview');
        $config->setName('');
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $widgets[] = $config;

        $config = $this->factory->createContainerWidget('Goals');
        $config->setSubCategory('General_Overview');
        $config->setName('Goals_ConversionsOverviewBy');
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $this->buildGoalByDimensionView('', $config);
        $widgets[] = $config;

        foreach ($goals as $goal) {
            $name = Common::sanitizeInputValue($goal['name']);
            $goalTranslated = Piwik::translate('Goals_GoalX', array($name));

            $config = $this->factory->createWidget();
            $config->setName($goalTranslated);
            $config->setSubCategory('General_Overview');
            $config->forceViewDataTable(Sparklines::ID);
            $config->setParameters(array('idGoal' => $goal['idgoal']));
            $config->setOrder(++$this->orderId);
            $config->setIsNotWidgetizable();
            $config->addParameters(array('allow_multiple' => (int) $goal['allow_multiple']));
            $widgets[] = $config;
        }

        return $widgets;
    }

    /**
     * @return WidgetConfig[]
     */
    public function createEcommerceOverviewPage()
    {
        $widgets = array();
        $config  = $this->factory->createWidget();
        $config->forceViewDataTable(Evolution::ID);
        $config->setCategory('Goals_Ecommerce');
        $config->setSubCategory('General_Overview');
        $config->setName('General_EvolutionOverPeriod');
        $config->setAction('getEvolutionGraph');
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $config->setParameters(array('columns' => 'nb_conversions', 'idGoal' => Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER));
        $widgets[] = $config;

        $config = $this->factory->createWidget();
        $config->setCategory('Goals_Ecommerce');
        $config->forceViewDataTable(Sparklines::ID);
        $config->setSubCategory('General_Overview');
        $config->setName('');
        $config->setModule('Ecommerce');
        $config->setAction('getSparklines');
        $config->setParameters(array('idGoal' => Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER));
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $widgets[] = $config;

        return $widgets;
    }

    /**
     * @return WidgetConfig[]
     */
    public function createEcommerceSalesPage()
    {
        $config = $this->factory->createContainerWidget('GoalsOrder');
        $config->setCategory('Goals_Ecommerce');
        $config->setSubCategory('Ecommerce_Sales');
        $config->setName('');
        $config->setParameters(array('idGoal' => Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER));
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $this->buildGoalByDimensionView(Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER, $config);

        return array($config);
    }

    /**
     * @param array $goal
     * @return WidgetConfig[]
     */
    public function createGoalDetailPage($goal)
    {
        $widgets = array();

        $idGoal = $goal['idgoal'];
        $name   = Common::sanitizeInputValue($goal['name']);
        $params = array('idGoal' => $idGoal);

        $goalTranslated = Piwik::translate('Goals_GoalX', array($name));

        $config = $this->factory->createWidget();
        $config->setName($goalTranslated);
        $config->setSubCategory($idGoal);
        $config->forceViewDataTable(Evolution::ID);
        $config->setAction('getEvolutionGraph');
        $config->setParameters($params);
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $widgets[] = $config;

        $config = $this->factory->createWidget();
        $config->setSubCategory($idGoal);
        $config->setName('');
        $config->forceViewDataTable(Sparklines::ID);
        $config->setParameters($params);
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $widgets[] = $config;

        $conversions = $this->getConversionForGoal($idGoal);

        if ($conversions > 0) {
            $config = $this->factory->createWidget();
            $config->setAction('goalConversionsOverview');
            $config->setSubCategory($idGoal);
            $config->setName('Goals_ConversionsOverview');
            $config->setParameters($params);
            $config->setOrder(++$this->orderId);
            $config->setIsNotWidgetizable();
            $widgets[] = $config;
        }

        $config = $this->factory->createContainerWidget('Goals' . $idGoal);
        $config->setName(Piwik::translate('Goals_GoalConversionsBy', array($name)));
        $config->setSubCategory($idGoal);
        $config->setParameters(array());
        $config->setOrder(++$this->orderId);
        $config->setIsNotWidgetizable();
        $this->buildGoalByDimensionView($idGoal, $config);
        $widgets[] = $config;

        return $widgets;
    }

    private function buildGoalByDimensionView($idGoal, WidgetContainerConfig $container)
    {
        $container->setLayout('ByDimension');
        $ecommerce = $idGoal == Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER;

        $conversions = $this->getConversionForGoal();
        if ($ecommerce) {
            $cartNbConversions = $this->getConversionForGoal($idGoal);
        } else {
            $cartNbConversions = false;
        }

        $preloadAbandonedCart = $cartNbConversions !== false && $conversions == 0;

        // add ecommerce reports
        $ecommerceCustomParams = array();
        if ($ecommerce) {
            if ($preloadAbandonedCart) {
                $ecommerceCustomParams['abandonedCarts'] = '1';
            } else {
                $ecommerceCustomParams['abandonedCarts'] = '0';
            }
        }

        if (Common::getRequestVar('idGoal', '') === '') // if no idGoal, use 0 for overview
        {
            $customParams['idGoal'] = '0'; // NOTE: Must be string! Otherwise Piwik_View_HtmlTable_Goals fails.
        }

        if ($conversions > 0 || $ecommerce) {
            // for non-Goals reports, we show the goals table
            $customParams = $ecommerceCustomParams + array('documentationForGoalsPage' => '1');

            if (Common::getRequestVar('idGoal', '') === '') // if no idGoal, use 0 for overview
            {
                $customParams['idGoal'] = '0'; // NOTE: Must be string! Otherwise Piwik_View_HtmlTable_Goals fails.
            }

            $translationHelper = new TranslationHelper();

            foreach ($this->allReports as $category => $reports) {
                if ($ecommerce) {
                    $categoryText = $translationHelper->translateEcommerceMetricCategory($category);
                } else {
                    $categoryText = $translationHelper->translateGoalMetricCategory($category);
                }

                foreach ($reports as $report) {
                    if (empty($report['viewDataTable'])
                        && empty($report['abandonedCarts'])
                    ) {
                        $report['viewDataTable'] = 'tableGoals';
                    }

                    $widget = $this->createWidgetForReport($report['module'], $report['action']);
                    $widget->setParameters($customParams);
                    $widget->setCategory($categoryText);
                    $widget->setSubCategory($categoryText);
                    $widget->setIsNotWidgetizable();

                    if (!empty($report['viewDataTable'])) {
                        $widget->setDefaultView($report['viewDataTable']);
                    }

                    $container->addWidget($widget);
                }
            }
        }
    }

    private function createWidgetForReport($module, $action)
    {
        $factory = new ReportWidgetFactory(Report::factory($module, $action));
        return $factory->createWidget();
    }

    private function getConversionForGoal($idGoal = '')
    {
        $period = Common::getRequestVar('period', '', 'string');
        $date   = Common::getRequestVar('date', '', 'string');
        $idSite = Common::getRequestVar('idSite', 0, 'int');

        if (!$period || !$date || !$idSite) {
            return false;
        }

        $datatable = Request::processRequest('Goals.get', array(
            'idGoal' => $idGoal,
            'period' => $period,
            'date' => $date,
            'idSite' => $idSite,
            'serialize' => 0,
            'segment' => false
        ));

        // we ignore the segment even if there is one set. We still want to show conversion overview if there are conversions
        // in general but not for this segment

        $dataRow = $datatable->getFirstRow();

        if (!$dataRow) {
            return false;
        }

        return $dataRow->getColumn('nb_conversions');
    }

}
