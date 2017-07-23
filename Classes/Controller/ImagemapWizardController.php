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
require_once($extensionClassesPath . 'Domain/Model/Typo3Env.php');
require_once($extensionClassesPath . 'Utilities/WizardUtility.php');

/**
 * Class/Function provides basic action for the Wizard-Form in Backend
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class ImagemapWizardController extends \Barlian\ImagemapWizard\Controller\AbstractBaseController {
	protected $view;
	protected $context = 'wizard';
	protected $ajax = FALSE;
	protected $params;
	protected $forceValue;
	
	protected $PA;
	protected $fobj;
	
	// Sub of $PA:
	protected $PaParams;
	
	protected $WizardUtility;
	
	protected $request;
	
	protected $debug = FALSE;

	/**
	 * Default action just renders the Wizard with the default view.
	 */
	public function WizardAction($PA, $fobj) {
		$this->request = $this->WizardUtility->getRequest();
		#if($this->request['route'] !== '/record/edit'){
		#	return;
		#}
		#if(!(array_key_exists('edit', $this->request) && array_key_exists('tt_content', $this->request['edit']))){
		#	return;
		#} else {
			$this->fobj = $fobj;
			$this->PA = $PA;
			$this->PaParams = $this->PA->getQueryParams()['P'];
/*
DebuggerUtility::var_dump(array(
			__METHOD__,
	'$PA'=>$PA,
	'$fobj'=>$fobj,
	'$this->PaParams' => $this->PaParams,
));
*/
			#$this->params = array(
			#	'table' => 'tt_content',
			#	'uid' => $this->request['edit']['tt_content'],
			#	'field' => $this->PA['field'],
			#);
			$currentValue = $GLOBALS['BE_USER']->getSessionData('imagemap_wizard.value');
			$this->view->setData($this->makeDataObj(
				$this->PaParams['table'],
				$this->PaParams['field'],
				$this->PaParams['uid'],
				$currentValue
			));
			$this->view->renderContent();
		#}
		#echo ('TEST');
		#return;
		#$currentValue = $GLOBALS['BE_USER']->getSessionData('imagemap_wizard.value');
		
/*
DebuggerUtility::var_dump(array(
			__METHOD__,
	'$PA'=>$PA,
	'$fobj'=>$fobj,
	'$params'=>$this->params,
#	'this->view' => $this->view,
	'$currentValue' => $currentValue,
	'request' => $this->WizardUtility->getRequest(),
));
*/
	}
		/*
			old:
			http://localhost/_typo3/_PROJECTS/dgpt.de/v4/typo3conf/ext/imagemap_wizard/wizard.php?&
				P[params]=&
				P[exampleImg]=&
				P[table]=tt_content&
				P[uid]=1081&
				P[pid]=5&
				P[field]=tx_imagemapwizard_links&
				P[flexFormPath]=&
				P[md5ID]=IDc9f4037713&
				P[returnUrl]=/_typo3/_PROJECTS/dgpt.de/v4/typo3/alt_doc.php%3F%26returnUrl%3Dmod.php%253F%2526M%253Dweb_list%2526id%253D5%26edit%5Btt_content%5D%5B1081%5D%3Dedit&P[formName]=editform&P[itemName]=data%5Btt_content%5D%5B1081%5D%5Btx_imagemapwizard_links%5D&P[hmac]=edb2960f4ed422183d9b1ef5cbd7e4a8624a594e&P[fieldChangeFunc][0]=imagemapwizard_valueChanged%28field%29%3B&P[fieldChangeFuncHash]=2133ee975b6f32ef788022b18ec036af61477b06&P[currentValue]=%3Cmap%3E%0A%3Carea%20shape%3D%22rect%22%20coords%3D%22556%2C27%2C596%2C50%22%20alt%3D%22Berufsverband%20der%20Approbierten%20Gruppenpsychotherapeuten%20(BAG%20e.V.)%22%20color%3D%22%23993366%22%20title%3D%22%22%3E%3C/area%3E%0A%3Carea%20shape%3D%22rect%22%20coords%3D%22448%2C52%2C616%2C72%22%20alt%3D&P[currentSelectedValues]=
			
			new:
			http://localhost/_typo3/_PROJECTS/dgpt.de/2016/v7/typo3/index.php?
				M=imagemap&
				moduleToken=88ced77e19b35947a47d59f2ad25c67473f6c26b&
				mode=wizard&
				act=file&
				P[params]=&
				P[exampleImg]=&
				P[table]=tt_content&
				P[uid]=1258&
				P[pid]=5&
				P[field]=tx_imagemapwizard_links&
				P[flexFormPath]=&
				P[md5ID]=ID17cc4c6844&
				P[returnUrl]=%2F_typo3%2F_PROJECTS%2Fdgpt.de%2F2016%2Fv7%2Ftypo3%2Findex.php%3Froute%3D%252Frecord%252Fedit%26token%3D1f0874eaa50b702719775d01533c5db669ce758e%26edit%5Btt_content%5D%5B1258%5D%3Dedit%26returnUrl%3D%252F_typo3%252F_PROJECTS%252Fdgpt.de%252F2016%252Fv7%252Ftypo3%252Findex.php%253FM%253Dweb_list%2526moduleToken%253D9a4b55226c219f4c06d4ae81a97b93ae6a5fa95e%2526id%253D5&P[formName]=editform&P[itemName]=data%5Btt_content%5D%5B1258%5D%5Btx_imagemapwizard_links%5D&P[hmac]=7726b5417ddcffce8c94b7c8a62c287932e4c996&P[fieldChangeFunc][0]=imagemapwizard_valueChanged%28field%29%3B&P[fieldChangeFuncHash]=2133ee975b6f32ef788022b18ec036af61477b06&P[currentValue]=&P[currentSelectedValues]=
		 
		 
			index.php?
				route=/wizard/record/browse&
				token=65e4f1ac5e8db15e1451b93ada38e62d315d5c54&
				mode=file&
				bparams=|||gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg|data-5-tt_content-1258-image-sys_file_reference|inline.checkUniqueElement||inline.importElement
			
			/index.php?
				route=/wizard/link/browse&
				token=806cf81c9eedfe8112efff8c137625de9c60889f&
				P[params]=&
				P[exampleImg]=&
				P[table]=tt_content&
				P[uid]=1258&
				P[pid]=5&
				P[field]=header_link&
				P[flexFormPath]=&
				P[md5ID]=ID160f06d571&
				P[returnUrl]=%2F_typo3%2F_PROJECTS%2Fdgpt.de%2F2016%2Fv7%2Ftypo3%2Findex.php%3Froute%3D%252Frecord%252Fedit%26token%3D1f0874eaa50b702719775d01533c5db669ce758e%26edit%5Btt_content%5D%5B1258%5D%3Dedit%26returnUrl%3D%252F_typo3%252F_PROJECTS%252Fdgpt.de%252F2016%252Fv7%252Ftypo3%252Findex.php%253FM%253Dweb_list%2526moduleToken%253D9a4b55226c219f4c06d4ae81a97b93ae6a5fa95e%2526id%253D5&P[formName]=editform&P[itemName]=data%5Btt_content%5D%5B1258%5D%5Bheader_link%5D&P[hmac]=c7e76b6d2fa54e5ce4f202e52ffc287457fad5b4&P[fieldChangeFunc][TBE_EDITOR_fieldChanged]=TBE_EDITOR.fieldChanged%28%27tt_content%27%2C%271258%27%2C%27header_link%27%2C%27data%5Btt_content%5D%5B1258%5D%5Bheader_link%5D%27%29%3B&P[fieldChangeFuncHash]=4ec3c5fae595bf3419202825356a0e9d92164e40&P[currentValue]=&P[currentSelectedValues]=
		 */


}


#if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/controller/class.tx_imagemapwizard_controller_wizard.php']) {
#	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/controller/class.tx_imagemapwizard_controller_wizard.php']);
#}


?>