<?php
/*
 *    @package    JVideo
 *    @subpackage Components
 *    @link http://jvideo.warphd.com
 *    @copyright (C) 2007 - 2010 Warp
 *    @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 ***
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JVideoControllerInstall extends JControllerLegacy
{
    private $view;

	public function __construct()
	{
        parent::__construct();

        $this->view = $this->getView('install', 'html');
	}

	public function display($cachable = false, $urlparams = array())
	{
		$task = $this->getTask();
		
		switch($task)
		{
			case "step2":
                $this->displayStep2View();
				break;
			case "step3":
                $this->displayStep3View();
				break;
            case "step4":
                $this->displayStep4View();
				break;
            case "step5":
                $this->displayStep5View();
                break;
			case "step1":
            default:
                $this->displayStep1View();
				break;
		}
	}

    private function displayStep1View()
    {
        $this->setupStep1View();
		$this->view->display('step1');
    }

    private function displayStep2View()
    {
		$this->setupStep2View();
		$this->view->display('step2');
    }

    private function displayStep3View()
    {
		$this->setupStep3View();
		$this->view->display('step3');
    }

    private function displayStep4View()
    {
		$this->setupStep4View();
		$this->view->display('step4');
    }

    private function displayStep5View()
    {
        $this->setupStep5View();
        $this->view->display('step5');
    }

	public function setupStep1View()
	{
        $this->doStepTasks('step1');
	}
	
	public function setupStep2View()
	{
        $this->doStepTasks('step2');
	}

    public function setupStep3View()
	{
        $this->doStepTasks('step3');
	}

    public function setupStep4View()
    {
        $this->doStepTasks('step4');
    }

    public function setupStep5View()
    {
        $this->doStepTasks('step5');
    }

    public function doStepTasks($step)
    {
        $model = $this->getModel("install");

        $stepTasks = $model->doStepTasks($step);

        JRequest::setVar('stepTasks', $stepTasks);
    }
}