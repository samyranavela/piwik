<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Ecommerce;

use Piwik\DataTable;
use Piwik\FrontController;
use Piwik\Piwik;
use Piwik\Translation\Translator;
use Piwik\View;
use Piwik\Plugins\Goals\TranslationHelper;

class Controller extends \Piwik\Plugins\Goals\Controller
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator, TranslationHelper $translationHelper)
    {
        $this->translator = $translator;

        parent::__construct($translator, $translationHelper);
    }

    public function getSparklines()
    {
        $idGoal = Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER;

        $view = new View('@Ecommerce/getSparklines');
        $view->onlyConversionOverview = false;
        $view->conversionsOverViewEnabled = true;

        if ($idGoal == Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER) {
            $goalDefinition['name'] = $this->translator->translate('Goals_Ecommerce');
            $goalDefinition['allow_multiple'] = true;
        } else {
            if (!isset($this->goals[$idGoal])) {
                Piwik::redirectToModule('Goals', 'index', array('idGoal' => null));
            }
            $goalDefinition = $this->goals[$idGoal];
        }

        $this->setGeneralVariablesView($view);

        $goal = $this->getMetricsForGoal($idGoal);
        foreach ($goal as $name => $value) {
            $view->$name = $value;
        }

        if ($idGoal == Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_ORDER) {
            $goal = $this->getMetricsForGoal(Piwik::LABEL_ID_GOAL_IS_ECOMMERCE_CART);
            foreach ($goal as $name => $value) {
                $name = 'cart_' . $name;
                $view->$name = $value;
            }
        }

        $view->idGoal = $idGoal;
        $view->goalAllowMultipleConversionsPerVisit = $goalDefinition['allow_multiple'];

        return $view->render();
    }

    public function getEcommerceLog($fetch = false)
    {
        $saveGET = $_GET;
        $_GET['segment'] = urlencode('visitEcommerceStatus!=none');
        $_GET['widget'] = 1;
        $output = FrontController::getInstance()->dispatch('Live', 'getVisitorLog', array($fetch));
        $_GET   = $saveGET;

        return $output;
    }

}
