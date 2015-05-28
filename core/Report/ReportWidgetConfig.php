<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Report;
use Piwik\Widget\WidgetConfig;

/**
 * Defines a new widget. You can create a new widget using the console command `./console generate:widget`.
 * The generated widget will guide you through the creation of a widget.
 *
 * For an example, see {@link https://github.com/piwik/piwik/blob/master/plugins/ExamplePlugin/Widgets/MyExampleWidget.php}
 *
 * @api since Piwik 2.15
 */
class ReportWidgetConfig extends WidgetConfig
{
    protected $viewDataTable = null;
    protected $forceViewDataTable = false;

    public function setDefaultView($viewDataTableId)
    {
        $this->viewDataTable = $viewDataTableId;
        return $this;
    }

    public function forceViewDataTable($viewDataTableId)
    {
        $this->forceViewDataTable = true;
        $this->setDefaultView($viewDataTableId);

        return $this;
    }

    public function getDefaultView()
    {
        return $this->viewDataTable;
    }

    public function getParameters()
    {
        $defaultParams = array(
            'module' => $this->getModule(),
            'action' => $this->getAction(),
            'forceView' => (int) $this->forceViewDataTable
        );

        if ($this->viewDataTable) {
            $defaultParams['viewDataTable'] = $this->viewDataTable;
        }

        return $defaultParams + $this->parameters;
    }

}