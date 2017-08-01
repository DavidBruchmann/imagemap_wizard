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

use \TYPO3\CMS\Backend\Utility\IconUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

$extensionClassesPath = ExtensionManagementUtility::extPath('imagemap_wizard') . 'Classes/';
# require_once($extensionClassesPath . 'Domain/Model/Mapper.php');
use \Barlian\Domain\Model\Mapper;
# require_once($extensionClassesPath . 'Domain/Model/Typo3Env.php');
use \Barlian\Domain\Model\Typo3Env;


/**
 * Class/Function which renders the Wizard-Form with the Data provided by the given Data-Object.
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class AbstractView extends \TYPO3\CMS\Backend\Form\Element\UserElement {

	protected $form; // tceform

	protected $formName; // tceform name

	protected $extensionKey = 'imagemap_wizard';

	protected $extensionPath;

	protected $id;

	protected $data;

	/**
	 * related functions:
	 * - addJsExtensionFile($file) Adds a single js-file to existing array
	 * - getJsExtensionIncludes()  returns all existing js-files of the extension wrapped with script-tags, all as one string
	 * @var array
	 */
	protected $jsExtensionFiles = array();

	/**
	 * related functions:
	 * - addCssExtensionFile($file) Adds a single css-file to existing array
	 * - getCssExtensionIncludes()  returns all existing css-files of the extension wrapped with style-tags, all as one string
	 *
	 * @var array
	 */
	protected $cssExtensionFiles = array();

	/**
	 * related functions:
	 * - addInlineJS($js)      adds another string to the existing string
	 * - getInlineJSIncludes() returns existing string wrapped with script-tag
	 * @var string
	 */
	protected $inlineJs = '';

	/**
	 * related functions:
	 * - addInlineCss($css)     adds another string to the existing string
	 * - getInlineCssIncludes() returns existing string wrapped with style-tag
	 * @var string
	 */
	protected $inlineCss = '';

	/**
	 * Array of js-files that can reside outside of the extension
	 * related functions:
	 * - addJsFile($cssFile)
	 * - getJsIncludes()
	 *
	 * @var array
	 */
	protected $jsFiles = array();

	/**
	 * Array of css-files that can reside outside of the extension
	 * related functions:
	 * - addCssFile($cssFile)
	 * - getCssIncludes()
	 *
	 * @var array
	 */
	protected $cssFiles = array();
	
	// TODO: explanation:
	protected $additionalJavaScriptPost = array();
	protected $additionalJavaScriptSubmit = array();
	protected $additionalHiddenFields = array();
	protected $additionalInlineLanguageLabelFiles = array();
	protected $stylesheetFiles = array();
	protected $requireJsModules = array();
	protected $extJSCODE = '';
	protected $inlineData = array();

	protected static $icon2Sprite = array(
		"gfx/button_up.gif" => 'actions-move-up',
		"gfx/button_down.gif" => 'actions-move-down',
		"gfx/undo.gif" => 'actions-edit-undo',
		"gfx/redo.gif" => 'extensions-imagemap_wizard-redo',
		"gfx/garbage.gif" => 'actions-edit-delete',
		"gfx/add.gif" => 'actions-edit-add',
		"gfx/refresh_n.gif" => 'actions-system-refresh',
		"gfx/pil2down.gif" => 'actions-view-table-expand',
		"gfx/pil2up.gif" => 'actions-view-table-collapse',
		"gfx/link_popup.gif" => 'extensions-imagemap_wizard-link',
		"gfx/zoom_in.gif" => 'extensions-imagemap_wizard-zoomin',
		"gfx/zoom_out.gif" => 'extensions-imagemap_wizard-zoomout',
		"gfx/arrowup.png" => 'actions-view-go-up',
		"gfx/arrowdown.png" => 'actions-view-go-down',
		"gfx/close_gray.gif" => 'actions-document-close',
	);
	
#	protected $languageService;
	
#	// Declaration of Barlian\ImagemapWizard\View\AbstractView::__construct() must be compatible with
#	// TYPO3\CMS\Backend\Form\NodeInterface::__construct(TYPO3\CMS\Backend\Form\NodeFactory $nodeFactory, array $data)
#	public function __construct(){
#		$this->languageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Lang\LanguageService');
#		$this->languageService->init($GLOBALS['BE_USER']->uc['lang']);
#	}


	/**
	 * Just initialize the View, fill internal variables etc...
	 */
	public function init() {
		$GLOBALS['LANG']->includeLLFile('EXT:lang/locallang_wizards.xml');
		$GLOBALS['LANG']->includeLLFile('EXT:imagemap_wizard/Resources/Private/Language/locallang.xml');
		$this->id = "imagemap" . GeneralUtility::shortMD5(rand(1, 100000));
		$this->extensionPath = ExtensionManagementUtility::extPath($this->extensionKey);
	}

	public function getId() {
		return $this->id;
	}

	/**
	 * Sets the relates Data-Model-Object
	 *
	 * @param \Barlian\ImagemapWizard\Domain\Model\DataObject Data-Object
	 */
	public function setData(\Barlian\ImagemapWizard\Domain\Model\DataObject $data) {
		$this->data = $data;
	}

	public function setTCEForm($form) {
		# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array('method'=>__METHOD__,'this'=>$this,'form'=>$form));
		$this->form = $form;
	}

	public function setFormName($name) {
		$this->formName = $name;
	}

	/**
	 * Collect required CSS-Resoucres
	 *
	 * @param String Filename
	 */
	protected function addCssExtensionFile($cssExtensionFile) {
		if (!in_array($cssExtensionFile, $this->cssExtensionFiles)) {
			$this->cssExtensionFiles[] = $cssExtensionFile;
		}
	}

	/**
	 * returns all existing css-files of the extension wrapped with style-tags, all as one string
	 * Paths are prepended with the backpath and extension-path
	 *
	 * @return string
	 */
	protected function getCssExtensionIncludes() {
		$backPath = Typo3Env::getBackPath();
		$extPath = str_replace(PATH_site, '', $this->extensionPath);
		$ret = '';
		if (is_array($this->cssExtensionFiles)) {
			foreach ($this->cssExtensionFiles as $cssExtensionFile) {
				#$ret .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $backPath . $extPath . $cssExtensionFile . "\" />";
				$ret .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $backPath . $extPath . $cssExtensionFile . "\" class=\"imagemapCssExtensionIncludes\" />";
			}
		}
		return $ret;
	}

	/** 
	 * Creating markup for External-Javascript-Resoucres $this->jsExtensionFiles
	 * returns all existing css-files of the extension wrapped with script-tags, all as one string
	 * Paths are prepended with the backpath and extension-path
	 *
	 * @return String Markup
	 */
	protected function getJsExtensionIncludes() {
		$backPath = Typo3Env::getBackPath();
		$extPath = str_replace(PATH_site, '', $this->extensionPath);
		$ret = '';
		if (is_array($this->jsExtensionFiles)) {
			foreach ($this->jsExtensionFiles as $jsExtensionFile) {
				#$ret .= "\n<script type=\"text/javascript\" src=\"" . $backPath . $extPath . $jsExtensionFile . "\"></script>";
				$ret .= "\n<script type=\"text/javascript\" src=\"" . $backPath . $extPath . $jsExtensionFile . "\" class=\"imagemapJsExtensionIncludes\"></script>";
			}
		}
		return $ret;
	}

	/**
	 * Collect required Javascript-Resoucres
	 *
	 * @param String Filename
	 */
	protected function addJsExtensionFile($jsExtensionFile) {
		if (!in_array($jsExtensionFile, $this->jsExtensionFiles)) {
			$this->jsExtensionFiles[] = $jsExtensionFile;
		}
	}

	/**
	 * Collect required Inline-Stylesheets.
	 *
	 * @param String Stylesheet-Block
	 */
	protected function addInlineCss($css) {
		$this->inlineCss .= "\n\n" . $css;
	}

	/**
	 * Creating markup for Inline-Stylesheet-Code
	 *
	 * @return String Markup
	 */
	protected function getInlineCssIncludes() {
		#return trim($this->inlineCss) ? ('<style type="text/css">' . trim($this->inlineCss) . '</style>') : '';
		return trim($this->inlineCss) ? ('<style type="text/css" id="imagemapInlineCssIncludes">' . trim($this->inlineCss) . '</style>') : '';
	}

	/**
	 * Collect required Inline-Javascript.
	 *
	 * @param String Javascript-Block
	 */
	protected function addInlineJS($js) {
		$this->inlineJs .= "\n\n" . $js;
	}

	/**
	 * Creating markup for Inline-Javascript-Code
	 *
	 * @return String Markup
	 */
	protected function getInlineJSIncludes() {
		#return trim($this->inlineJs) ? ('<script type="text/javascript">' . trim($this->inlineJs) . '</script>') : '';
		return trim($this->inlineJs) ? ('<script type="text/javascript" id="imagemapInlineJsIncludes">' . trim($this->inlineJs) . '</script>') : '';
	}

	/**
	 *
	 * @param String Filename
	 */
	protected function addCssFile($cssFile) {
		if (!in_array($cssFile, $this->cssFiles)) {
			$this->cssFiles[] = $cssFile;
		}
	}

	/**
	 *
	 * @param String Filename
	 */
	protected function getCssIncludes() {
		$ret = '';
		if (is_array($this->cssFiles)) {
			foreach ($this->cssFiles as $cssFile) {
				#$ret .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $cssFile . "\" />";
				$ret .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $cssFile . "\"  id=\"imagemapCssIncludes\" />";
			}
		}
		return $ret;
	}

	/**
	 * 
	 * @param String Filename
	 */
	protected function addJsFile($jsFile) {
		if (!in_array($jsFile, $this->jsFiles)) {
			$this->jsFiles[] = $jsFile;
		}
	}

	/**
	 *
	 * @param String Filename
	 */
	protected function getJsIncludes() {
		$ret = '';
		if (is_array($this->jsFiles)) {
			foreach ($this->jsFiles as $jsFile) {
				#$ret .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $jsFile . "\" />";
				$ret .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $jsFile . "\" id=\"imagemapJsIncludes\"  />";
			}
		}
		return $ret;
	}

	protected function setAdditionalJavaScriptPost($scripts){
		$this->additionalJavaScriptPost = array();
		if(is_array($scripts)){
			$this->additionalJavaScriptPost = $scripts;
		} else {
			$this->additionalJavaScriptPost[] = $scripts;
		}
	}

	protected function addAdditionalJavaScriptPost($name, $script){
		# DebuggerUtility::var_dump(array(__METHOD__,$name=>$script));
		$this->additionalJavaScriptPost[$name] = $script;
	}

	protected function getAdditionalJavaScriptPost(){
		return $this->additionalJavaScriptPost;
	}

	protected function removeAdditionalJavaScriptPostByVal($script){
		foreach($this->additionalJavaScriptPost as $key => $value){
			if($script === $value){
				unset($this->additionalJavaScriptPost[$key]);
			}
		}
	}

	protected function removeAdditionalJavaScriptPostByKey($name){
		foreach($this->additionalJavaScriptPost as $key => $value){
			if($key === $name){
				unset($this->additionalJavaScriptPost[$key]);
			}
		}
	}

	protected function setAdditionalJavaScriptSubmit($scripts){
		$this->additionalJavaScriptSubmit = array();
		if(is_array($scripts)){
			$this->additionalJavaScriptSubmit = $scripts;
		} else {
			$this->additionalJavaScriptSubmit[] = $scripts;
		}
	}

	protected function addAdditionalJavaScriptSubmit($script){
		$this->additionalJavaScriptSubmit[] = $script;
	}

	protected function getAdditionalJavaScriptSubmit(){
		return $this->additionalJavaScriptSubmit;
	}

	protected function removeAdditionalJavaScriptSubmitByVal($script){
		foreach($this->additionalJavaScriptSubmit as $key => $value){
			if($script === $value){
				unset($this->additionalJavaScriptSubmit[$key]);
			}
		}
	}

	protected function removeAdditionalJavaScriptSubmitByKey($name){
		foreach($this->additionalJavaScriptSubmit as $key => $value){
			if($key === $name){
				unset($this->additionalJavaScriptSubmit[$key]);
			}
		}
	}

	protected function setAdditionalHiddenFields($scripts){
		$this->additionalHiddenFields = array();
		if(is_array($scripts)){
			$this->additionalHiddenFields = $scripts;
		} else {
			$this->additionalHiddenFields[] = $scripts;
		}
	}

	protected function addAdditionalHiddenFields($script){
		$this->additionalHiddenFields[] = $script;
	}

	protected function getAdditionalHiddenFields(){
		return $this->additionalHiddenFields;
	}

	protected function removeAdditionalHiddenFieldsByVal($script){
		foreach($this->additionalHiddenFields as $key => $value){
			if($script === $value){
				unset($this->additionalHiddenFields[$key]);
			}
		}
	}

	protected function removeAdditionalHiddenFieldsByKey($name){
		foreach($this->additionalHiddenFields as $key => $value){
			if($key === $name){
				unset($this->additionalHiddenFields[$key]);
			}
		}
	}

	protected function setAdditionalInlineLanguageLabelFiles($scripts){
		$this->additionalInlineLanguageLabelFiles = array();
		if(is_array($scripts)){
			$this->additionalInlineLanguageLabelFiles = $scripts;
		} else {
			$this->additionalInlineLanguageLabelFiles[] = $scripts;
		}
	}

	protected function addAdditionalInlineLanguageLabelFiles($script){
		$this->additionalInlineLanguageLabelFiles[] = $script;
	}

	protected function getAdditionalInlineLanguageLabelFiles(){
		return $this->additionalInlineLanguageLabelFiles;
	}

	protected function removeAdditionalInlineLanguageLabelFilesByVal($script){
		foreach($this->additionalInlineLanguageLabelFiles as $key => $value){
			if($script === $value){
				unset($this->additionalInlineLanguageLabelFiles[$key]);
			}
		}
	}

	protected function removeAdditionalInlineLanguageLabelFilesByKey($name){
		foreach($this->additionalInlineLanguageLabelFiles as $key => $value){
			if($key === $name){
				unset($this->additionalInlineLanguageLabelFiles[$key]);
			}
		}
	}

	protected function setStylesheetFiles($scripts){
		$this->stylesheetFiles = array();
		if(is_array($scripts)){
			$this->stylesheetFiles = $scripts;
		} else {
			$this->stylesheetFiles[] = $scripts;
		}
	}

	protected function addStylesheetFiles($script){
		$this->stylesheetFiles[] = $script;
	}

	protected function getStylesheetFiles(){
		return $this->stylesheetFiles;
	}

	protected function removeStylesheetFilesByVal($script){
		foreach($this->stylesheetFiles as $key => $value){
			if($script === $value){
				unset($this->stylesheetFiles[$key]);
			}
		}
	}

	protected function removeStylesheetFilesByKey($name){
		foreach($this->stylesheetFiles as $key => $value){
			if($key === $name){
				unset($this->stylesheetFiles[$key]);
			}
		}
	}

	protected function setRequireJsModules($scripts){
		$this->requireJsModules = array();
		if(is_array($scripts)){
			$this->requireJsModules = $scripts;
		} else {
			$this->requireJsModules[] = $scripts;
		}
	}

	protected function addRequireJsModules($script){
		$this->requireJsModules[] = $script;
	}

	protected function getRequireJsModules(){
		return $this->requireJsModules;
	}

	protected function removeRequireJsModulesByVal($script){
		foreach($this->requireJsModules as $key => $value){
			if($script === $value){
				unset($this->requireJsModules[$key]);
			}
		}
	}

	protected function removeRequireJsModulesByKey($name){
		foreach($this->requireJsModules as $key => $value){
			if($key === $name){
				unset($this->requireJsModules[$key]);
			}
		}
	}

	protected function setExtJSCODE($script){
		$this->extJSCODE = $script;
	}

	protected function getExtJSCODE(){
		return $this->extJSCODE;
	}

	protected function setInlineData($datas){
		$this->inlineData = array();
		if(is_array($datas)){
			$this->inlineData = $datas;
		} else {
			$this->inlineData[] = $datas;
		}
	}

	protected function addInlineData($data){
		$this->inlineData[] = $data;
	}

	protected function getInlineData(){
		return $this->inlineData;
	}

	protected function removeInlineDataByVal($data){
		foreach($this->inlineData as $key => $value){
			if($data === $value){
				unset($this->inlineData[$key]);
			}
		}
	}

	protected function removeInlineDataByKey($name){
		foreach($this->inlineData as $key => $value){
			if($key === $name){
				unset($this->inlineData[$key]);
			}
		}
	}

	protected function renderTemplate($file) {
		ob_start();
		require_once(ExtensionManagementUtility::extPath($this->extensionKey) . 'Resources/Private/Templates/' . $file);
		$ret = ob_get_contents();
		ob_end_clean();
		# DebuggerUtility::var_dump($this);
		# echo '<pre>'.htmlentities($ret).'</pre>';
		return $ret;
	}

	protected function getAjaxURL($script) {
		$ajaxURL = Typo3Env::getExtBackPath('imagemap_wizard') . $script;
		return $ajaxURL;
	}

	protected function getLL($label, $printIt = false) {
		$value = $GLOBALS['LANG']->getLL($label);
		if ($printIt) {
			echo $value;
		}
		return $value;
	}

	/**
	 * Create a img-tag with a TYPO3-Skinicon
	 *
	 * @param String skinPath the Path to the TYPO3-icon
	 * @param String attr additional required attributes
	 * @return String HTML-img-tag
	 */
	protected function getIcon($skinPath, $attr = '') {
		return '<span ' . $attr . '>' . IconUtility::getSpriteIcon(self::$icon2Sprite[$skinPath]) . '</span>';
	}

	/**
	 *   Determine path to the view-templates
	 *   Just a shortcut to reduce the code within the view's
	 *
	 *   @return string	  relative path to the template folder
	 */
	protected function getTplSubpath() {
		return Typo3Env::getExtBackPath('imagemap_wizard') . 'templates/';
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/View/AbstractView.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/View/AbstractView.php']);
}
