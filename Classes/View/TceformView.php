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
use \TYPO3\CMS\Core\Utility\GeneralUtility;
#use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Backend\Form\FormResultCompiler;
use TYPO3\CMS\Core\Page\PageRenderer;


/**
 * Class/Function which renders the TCE-Form with the Data provided by the given Data-Object.
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class TceformView extends \Barlian\ImagemapWizard\View\AbstractView {

	protected $wizardConf;

	public function __construct(\TYPO3\CMS\Backend\Form\NodeFactory $nodeFactory, array $data=array()) {
		parent::init();
		$this->init();
	}

	/**
	 * Just initialize the View, fill internal variables etc...
	 */
	public function init() {

	}

	public function setWizardConf($wConf) {
		$this->wizardConf = $wConf;
	}

    /**
     * Wrapper for access to the current page renderer object
     *
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected function getPageRenderer()
    {
        if ($this->pageRenderer === null) {
            $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        }
        return $this->pageRenderer;
    }

	public function initPageRenderer($conf){
        $pageRenderer = $this->getPageRenderer();
		# DebuggerUtility::var_dump(array('__METHOD__'=>__METHOD__,'$conf'=>$conf));
		if(count($conf['additionalJavaScriptPost'])){
			foreach($conf['additionalJavaScriptPost'] as $key => $additionalJavaScriptPost){
				$pageRenderer->addJsFooterInlineCode($key, $additionalJavaScriptPost, $compress = false, $forceOnTop = false);
			}
		}
        #$pageRenderer->addJsFile($lib);
	}


	public function render() {
		$resultArray = $this->initializeResultArray();

		$fieldWizardResult = $resultArray;
		$fieldWizardResult['html'] = $this->renderFieldWizard();
		$fieldWizardResult['additionalJavaScriptPost'] = $this->getAdditionalJavaScriptPost();
		$fieldWizardResult['additionalJavaScriptSubmit'] = $this->getAdditionalJavaScriptSubmit();
		$fieldWizardResult['additionalHiddenFields'] = $this->getAdditionalHiddenFields();
		$fieldWizardResult['additionalInlineLanguageLabelFiles'] = $this->getAdditionalInlineLanguageLabelFiles();
		$fieldWizardResult['stylesheetFiles'] = $this->getStylesheetFiles();
		$fieldWizardResult['requireJsModules'] = $this->getRequireJsModules();
		$fieldWizardResult['extJSCODE'] = $this->getExtJSCODE();
		$fieldWizardResult['inlineData'] = $this->getInlineData();

		# DebuggerUtility::var_dump(array($resultArray,$fieldWizardResult));
		$resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

		$this->initPageRenderer($resultArray);
		# DebuggerUtility::var_dump(array('$this->pageRenderer'=>$this->pageRenderer));
		# DebuggerUtility::var_dump(array($resultArray,$fieldWizardResult));

		#$formResultCompiler = GeneralUtility::makeInstance(FormResultCompiler::class);
		#$formResultCompiler->mergeResult($resultArray);
		#$jsBottom = $formResultCompiler->printNeededJSFunctions();
		#DebuggerUtility::var_dump($jsBottom);

		/*
		$mainFieldHtml = [];
		$mainFieldHtml[] = '<div class="form-control-wrap">';
		$mainFieldHtml[] =  '<div class="form-wizards-wrap">';
		$mainFieldHtml[] =      '<div class="form-wizards-element">';
		// Main HTML of element done here ...
		$mainFieldHtml[] =      '</div>';
		$mainFieldHtml[] =      '<div class="form-wizards-items-bottom">';
		$mainFieldHtml[] =          $fieldWizardHtml;
		$mainFieldHtml[] =      '</div>';
		$mainFieldHtml[] =  '</div>';
		$mainFieldHtml[] = '</div>';

		$resultArray['html'] = implode(LF, $mainFieldHtml);
		*/
		#$resultArray['html'] = $fieldWizardResult;
		return $resultArray['html'];
	}
	/**/

	/**
	 * Renders the template with thumbnail and button for the TCE-form
	 * Any variables for JS-files and CSS-files are / can be filled by the template
	 * by according functions of this View-class
	 *
	 * @return string	 the rendered form content
	 */
	public function renderFieldWizard() {
		$content = '';
		# DebuggerUtility::var_dump(array(__METHOD__,$this->data,'$this->data->hasValidImageFile()'=>$this->data->hasValidImageFile()));
		if (!$this->data->hasValidImageFile()) {
			# DebuggerUtility::var_dump($this);
			if($this->form){
				$content = $GLOBALS['LANG']->sL('LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xlf:form.no_image');
			}
		} else {
			# DebuggerUtility::var_dump(array(__METHOD__,$this->form));

			$content = $this->renderTemplate('tceform.php');

			/*
			# \TYPO3\CMS\Backend\Form\AbstractNode
				return [
					'additionalJavaScriptPost' => [],
					'additionalJavaScriptSubmit' => [],
					'additionalHiddenFields' => [],
					'additionalInlineLanguageLabelFiles' => [],
					'stylesheetFiles' => [],
					// can hold strings or arrays, string = requireJS module, array = requireJS module + callback e.g. array('TYPO3/Foo/Bar', 'function() {}')
					'requireJsModules' => [],
					'extJSCODE' => '',
					'inlineData' => [],
					'html' => '',
				];
				// https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/FormEngine/Rendering/Index.html#nodefactory
				public function render(){
					$resultArray = $this->initializeResultArray();

					$fieldWizardResult = $this->renderFieldWizard();
					$fieldWizardHtml = $fieldWizardResult['html'];
					$resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

					$mainFieldHtml = [];
					$mainFieldHtml[] = '<div class="form-control-wrap">';
					$mainFieldHtml[] =  '<div class="form-wizards-wrap">';
					$mainFieldHtml[] =      '<div class="form-wizards-element">';
					// Main HTML of element done here ...
					$mainFieldHtml[] =      '</div>';
					$mainFieldHtml[] =      '<div class="form-wizards-items-bottom">';
					$mainFieldHtml[] =          $fieldWizardHtml;
					$mainFieldHtml[] =      '</div>';
					$mainFieldHtml[] =  '</div>';
					$mainFieldHtml[] = '</div>';

					$resultArray['html'] = implode(LF, $mainFieldHtml);
					return $resultArray;
				}
			*/

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


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/View/TceformView.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/View/TceformView.php']);
}
