<?php

namespace Barlian\ImagemapWizard\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Tolleiv Nietsch (info@tolleiv.de)
 *  (c) 2017 David Bruchmann, Webdevelopment Barlian (david.bruchmann@gmail.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('imagemap_wizard') . 'Classes/';
# require_once($extensionClassesPath . 'Domain/Model/DataObject.php');
# require_once($extensionClassesPath . 'Utilities/WizardUtility.php');

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \Barlian\ImagemapWizard\Utilities\WizardUtility;

/**
 * Class/Function provides basic action for the Wizard-Form in Backend
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class TceFormUserElementController extends \Barlian\ImagemapWizard\Controller\AbstractBaseController
{
	
	protected $params = array();
	
	public $debug = FALSE;

	/**
	 * Generate the Form since this is directly called from TCA we have to repeat some initial steps
	 *
	 * @param	Array		$PA   Environment, Parameters and Data
	 * @param	\TYPO3\CMS\Backend\Form\Element\UserElement		The Form Object
	 * @return	String		HTMLCode with form-field
	 */
	public function renderForm($PA, $fobj) { // t3lib_TCEforms $fobj
		// TODO: is full namespace required here?
		$this->WizardUtility = GeneralUtility::makeInstance('Barlian\ImagemapWizard\Utilities\WizardUtility');

		$GLOBALS['BE_USER']->setAndSaveSessionData('imagemap_wizard.value', NULL);
		$params = $this->initParams($PA);
		$this->initContext('tceform');
		$this->PA = $params;
		$this->fobj = $fobj;
		if($this->debug){
			DebuggerUtility::var_dump(array(__METHOD__, '$this->params'=>$this->params, 't3lib_TCEForm' => $t3lib_TCEForm, 'debug_backtrace'=>debug_backtrace(), ));
		}
		# return $this->triggerAction();
		if($this->context=='ajax'){
			// is "return" needed here?
			return $this->TceformAjaxAction();
		} else {
			return $this->TceformAction();
		}
	}

	/**
	 * Using the incoming params PA for assigning $this->params
	 *
	 * @param array $PA
	 *
	 * @return array $params
	 */
	protected function initParams($PA){
		$params = $this->WizardUtility->getServerRequestParams($PA);
		if($this->debug){
			DebuggerUtility::var_dump(array(__METHOD__, '$PA'=>$PA, '$fobj'=>$fobj, '$params'=>$params, ));
		}
		$this->params['table'] = $params['table'];
		if ($GLOBALS['TCA'][ $params['table'] ]['columns'][ $params['field'] ]['config']['type'] == 'flex') {
			$parts = array_slice(explode('][', $params['itemFormElName']), 3);
			$field = substr(implode('/', $parts), 0, -1);
			$this->params['field'] = sprintf('%s:%d:%s:%s', $params['table'], $params['row']['uid'], $params['field'], $field);
		} else {
			$this->params['field'] = $params['field'];
		}
		$this->params['uid'] = isset($params['row']['uid']) ? $params['row']['uid'] : $params['uid'];
		$this->params['pid'] = isset($params['row']['pid']) ? $params['row']['pid'] : $params['pid'];
		// TODO: get pObj:
		$this->params['pObj'] = isset($params['pObj']) ? $params['pObj'] : NULL;
		$this->params['fieldConf'] = $params['fieldConf'];
		// TODO: transfer itemFormElName somehow?
		$this->params['itemFormElName'] = isset($params['itemFormElName']) ? $params['itemFormElName'] : NULL;
		return $params;
	}

	protected function initView() {
		$nodeFactory = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Form\NodeFactory');
		$this->view = GeneralUtility::makeInstance('Barlian\ImagemapWizard\View\TceformView', $nodeFactory, array());
		$this->view->init($this->context);
	}

	/**
	 * Action to populate TCE-Form with Wizard-Data
	 *
	 * @return void
	 */
	protected function TceformAjaxAction() {
		$this->params['table']          = GeneralUtility::_GP('table');
		$this->params['field']          = GeneralUtility::_GP('field');
		$this->params['uid']            = GeneralUtility::_GP('uid');
		$this->params['fieldConf']      = unserialize(stripslashes((GeneralUtility::_GP('config'))));
		$this->params['pObj']           = GeneralUtility::makeInstance('t3lib_TCEforms');
		$this->params['itemFormElName'] = GeneralUtility::_GP('formField');
		$this->forceValue               = GeneralUtility::_GP('value');
		$this->params['pObj']->initDefaultBEMode();
		$GLOBALS['BE_USER']->setAndSaveSessionData('imagemap_wizard.value', $this->forceValue);
		echo $this->TceformAction();
	}

	/**
	 * Form action just renders the TCEForm which opens the wizard
	 * comes with a cool preview and Ajax functionality which updates the preview...
	 *
	 * @return string $content
	 */
	protected function TceformAction() {
		try {
			$dataObj = $this->makeDataObj(
				$this->params['table'],
				$this->params['field'],
				$this->params['uid'],
				$this->forceValue
			);
		} catch (Exception $e) {
			// @todo make something smart if params are empty and object creation failed
			DebuggerUtility::var_dump(array('ERROR in '.__METHOD__,$e));
		}
		$dataObj->setFieldConf( $this->params['fieldConf'] );
		$this->view->setData( $dataObj );

		// is this somehow right: ???
		// what was the reason for this???
		if(isset($this->PA)){
			$this->view->setTCEForm( $this->fobj );
		} elseif(isset($this->params['pObj'])){
			$this->view->setTCEForm( $this->params['pObj'] );
		} else {
			// ERROR // TODO
		}
		$this->view->setFormName( $this->params['itemFormElName'] );
		$this->view->setWizardConf( $this->params['fieldConf']['config']['wizards'] );
		
		#DebuggerUtility::var_dump($this);
		
		# $content = $this->view->renderContent();
		$content = $this->view->render();
		/*
		if($this->debug){
			DebuggerUtility::var_dump(array(
				__METHOD__,
				'WizardUtility::getRequest()'=>WizardUtility::getRequest(),
				'$this->params'=>$this->params,
				'$this'=>$this,
				'$dataObj'=>$dataObj,
				'$this->view'=>$this->view,
				'$content' => $content
			));
		}
		*/
		return $content;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/Controller/TceFormUserElementController.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/Controller/TceFormUserElementController.php']);
}
