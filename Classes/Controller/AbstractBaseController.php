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

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('imagemap_wizard') . 'Classes/';
require_once($extensionClassesPath . 'Domain/Model/DataObject.php');
require_once($extensionClassesPath . 'Utilities/WizardUtility.php');

/**
 * Class/Function provides basic action for the Wizard-Form in Backend
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
abstract class AbstractBaseController {

	/**
	 * Initialize Context and required View
	 */
	public function __construct() {
		$this->WizardUtility = GeneralUtility::makeInstance('Barlian\ImagemapWizard\Utilities\WizardUtility');
		$this->initContext();
		$this->initView();
	}

	/**
	 * Wrapper to instaciate the dataObject
	 *
	 * @param $table
	 * @param $field
	 * @param $uid
	 * @param $value
	 * @return tx_imagemapwizard_model_dataObject
	 */
	protected function makeDataObj($table, $field, $uid, $value) {
		$dataObj = GeneralUtility::makeInstance(
			'Barlian\\ImagemapWizard\\Domain\\Model\\DataObject',
			$table,
			$field,
			$uid,
			$value
		);
		return $dataObj;
	}

	/**
	 * Execute required action which is determined by the given context
	 * Calls one of the following actions:
	 * - TceformAction
	 * - TceformAjaxAction
	 * - WizardAction
	 * Missing Action: WizardAjaxAction
	 */
	public function triggerAction() {
		$action = ucfirst($this->context) . ($this->ajax ? 'Ajax' : '') . 'Action';
		if ((TYPO3_MODE == 'BE')) {
			if($this->debug) {
				DebuggerUtility::var_dump(array(
					'$action'=>$action,
					__METHOD__.': You should not prefix assets with \'typo3/\' in the Backend'
				));
			}
			#throw new \Exception("You should not prefix assets with 'typo3/' in the Backend");
		}
		# DebuggerUtility::var_dump(array(__METHOD__,$this, $action));
		return call_user_func_array(array($this, $action), array());
	}

	/**
	 * Determine context
	 */
	protected function initContext($forceContext = NULL) {
		#$this->initRequest(NULL,NULL);		
		$this->ajax = (GeneralUtility::_GP('ajax') == '1');
		$reqContext = $forceContext ? $forceContext : GeneralUtility::_GP('context');
		$this->context = ($reqContext == 'tceform') ? 'tceform' : 'wizard';
	}

	protected function initView() {
		$nodeFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Form\\NodeFactory');

		//	$this->context: [tceform | wizard]
		$viewClass = 'Barlian\\ImagemapWizard\\View\\' .
			($this->context=='tceform' ? 'TceformView' : 'WizardView');

		$this->view = GeneralUtility::makeInstance($viewClass, $nodeFactory, array());
		$this->view->init($this->context);
	}
	
	protected function initRequest($PA, $fObj){
		$this->WizardUtility = GeneralUtility::makeInstance('Barlian\ImagemapWizard\Utilities\WizardUtility');
		
	
		$GLOBALS['BE_USER']->setAndSaveSessionData('imagemap_wizard.value', NULL);
		#if($PA instanceof \TYPO3\CMS\Core\Http\ServerRequest){
		#	$queryParams = $PA->getQueryParams();
		#	$params = $queryParams['P'];
		#} else {
		#	$params = $PA;
		#}
		
		$params = $this->WizardUtility->getServerRequestParams($PA);
		#DebuggerUtility::var_dump(array(__METHOD__,$params));
		$this->params['table'] = $params['table'];
		if ($GLOBALS['TCA'][$params['table']]['columns'][$params['field']]['config']['type'] == 'flex') {
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

		$this->initContext('tceform');
		$this->initView();
		$this->PA = $params;
		$this->fobj = $fobj;
	}

}

?>