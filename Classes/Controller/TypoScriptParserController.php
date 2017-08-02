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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;


$extensionClassesPath = ExtensionManagementUtility::extPath('imagemap_wizard') . 'Classes/';
require_once($extensionClassesPath . 'Domain/Model/Mapper.php');
use \Barlian\Domain\Model\Mapper;

/**
 * Class/Function ...
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class TypoScriptParserController {

	public function applyImageMap($content, $conf) {
		# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($content);
		$xhtml = preg_match('/^xhtml/', $GLOBALS['TSFE']->config['config']['doctype']);
		$attrlist = explode(',', 'shape,coords,href,target,nohref,alt,title,accesskey,tabindex,onfocus,onblur,id,class,style,lang,dir,onclick,ondblclick,onmousedown,onmouseup,onmouseover,onmousemove,onmouseout,onkeypressonkeydown,onkeyup');

		// remove target attribute to have xhtml-strict output
		if (strcmp($GLOBALS['TSFE']->config['config']['doctype'], 'xhtml_strict')===0) {
			$attrlist = array_diff($attrlist, array('target'));
		}
		$mapname = $this->cObj->stdWrap(
			preg_replace('/\s/', '-', $this->cObj->getData($conf['map.']['name'], $this->cObj->data)),
			$conf['map.']['name.']
		);

		// checking which image this is - using registers I guess 
		// these won't change in later versions (global vars might)
		$num = $this->cObj->getData('register:IMAGE_NUM_CURRENT', $this->cObj->data);
		# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($num);

		if ($num == 0) {
			$converter = GeneralUtility::makeInstance('Barlian\Domain\Model\Mapper');
			$mapname = $converter->createValidNameAttribute($mapname);
			$map = $converter->generateMap(
				$this->cObj,
				$mapname,
				$this->cObj->getData($conf['map.']['data'],$this->cObj->data),
				$attrlist,
				$xhtml,
				$conf,
				$num
			);
			# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($content);
			if (!$converter->isEmptyMap($map) || $this->cObj->getData('register:keepUsemapMarker', $this->cObj->data)) {
				return str_replace('***IMAGEMAP_USEMAP***', $mapname, $content) . $map;
			}
		}
		# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($content);
		return str_replace(' usemap="#***IMAGEMAP_USEMAP***"', '', $content);
	}
}


#if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/class.tx_imagemapwizard_parser.php']) {
#	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/class.tx_imagemapwizard_parser.php']);
#}


?>