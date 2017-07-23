<?php

namespace Barlian\ImagemapWizard\View;

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

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
#use \Barlian\ImagemapWizard\Domain\Model\Typo3Env;
#use \TYPO3\CMS\Backend\Utility\IconUtility;
#use \TYPO3\CMS\Core\Utility\GeneralUtility;
#use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


/**
 * Class/Function which renders the TCE-Form with the Data provided by the given Data-Object.
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class TceformView extends \Barlian\ImagemapWizard\View\AbstractView {

	protected $wizardConf;

	public function setWizardConf($wConf) {
		$this->wizardConf = $wConf;
	}

	/**
	 * Renders Content and prints it to the screen (or any active output buffer)
	 *
	 * @return string	 the rendered form content
	 */
	public function renderContent() {
		#DebuggerUtility::var_dump(array(__METHOD__,$this->data,'$this->data->hasValidImageFile()'=>$this->data->hasValidImageFile()));
		if (!$this->data->hasValidImageFile()) {
			# DebuggerUtility::var_dump($this);
			if($this->form){
				$content = $this->form->sL('LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:form.no_image');
			}
		} else {
			# DebuggerUtility::var_dump(array(__METHOD__,$this->form));
			
			$content = $this->renderTemplate('tceform.php');

			$this->form->additionalCode_pre[] = $this->getCssIncludes();
			$this->form->additionalCode_pre[] = $this->getCssExtensionIncludes();
			$this->form->additionalCode_pre[] = $this->getInlineCssIncludes();

			$this->form->additionalCode_pre[] = $this->getJSIncludes();
			$this->form->additionalCode_pre[] = $this->getJsExtensionIncludes();
			$this->form->additionalCode_pre[] = $this->getInlineJSIncludes();
			
			#DebuggerUtility::var_dump(array(__METHOD__,$this));
		}
		return $content;
	}
}


#if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/view/class.tx_imagemapwizard_view_tceform.php']) {
#	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/view/class.tx_imagemapwizard_view_tceform.php']);
#}


?>