<?php

namespace Barlian\ImagemapWizard\Hook;

/***************************************************************
 *  Copyright notice
 *
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

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('imagemap_wizard').'/Classes/Utilities/WizardUtility.php');
use \Barlian\ImagemapWizard\Utilities\WizardUtility;


/**
 * Hook-Function to fix an issue with backpath in TYPO3 7.6
 *
 * @author David Bruchmann, Webdevelopment Barlian (david.bruchmann@gmail.com)
 */
class PageRendererHooks {
	
	# protected $request = NULL;

	public function FixBackPath($content, $pObj){
		if(WizardUtility::checkRecordEditRequest()){
			// storing current backPath in $pObj->inlineComments
			if($pObj->backPath){
				$pObj->addInlineComment('$pObj->backPath='.$pObj->backPath);
			}
			// setting backPath to ''
			$pObj->setBackPath('');
		}
	}

	public function RestoreBackPath($content, $pObj){
		if(WizardUtility::checkRecordEditRequest()){
			// removing current backPath from $content['inlineComments']
			$inlineCommentsNew = array();
			$storedBackPath = '';
			foreach($content['inlineComments'] as $k => $v){
				if(preg_match('/$pObj->backPath=(.*)$/',$v,$match)){
					#\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array('$match'=>$match));
					if(isset($match[1])){
						// setting backPath to stored value again
						$storedBackPath = $match[1];
						$pObj->setBackPath($storedBackPath);
					}
				} else {
					// collecting all $inlineComments if not own storage
					$inlineCommentsNew[] = $v;
				}
			}
			$content['inlineComments'] = $inlineCommentsNew;
			# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array('$request'=>$request));
			# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array('$content'=>$content,'$pObj'=>$pObj,'$this'=>$this));
			/*
			#echo '<pre>'.'$content[jsLibs] = '.$content['jsLibs'].'</pre>';
			# imagemap_wizard
			$toVerify = array(
				'jsLibs',              // STRING: js with script-tag in php-string
				'jsFiles',             // STRING:
				'jsFooterFiles',       // STRING:
				'cssLibs',             // STRING: several link-tags, explode it by '> <'
				'cssFiles',            // STRING:
				#'headerData',          // ARRAY :
				#'footerData',          // ARRAY :
				'jsInline',            // STRING:
				'cssInline',           // STRING:
				#'xmlPrologAndDocType', // STRING:
				#'htmlTag',             // STRING:
				#'headTag',             // STRING:
				#'charSet',             // STRING:
				#'metaCharsetTag',      // STRING:
				#'shortcutTag',         // STRING:
				#'inlineComments',      // ARRAY :
				#'baseUrl',             // STRING:
				#'baseUrlTag',          // STRING:
				#'favIcon',             // STRING:
				#'iconMimeType',        // STRING:
				#'titleTag',            // STRING:
				#'title',               // STRING:
				#'metaTags',            // ARRAY :
				'jsFooterInline',      // STRING:
				'jsFooterLibs',        // STRING:
				#'bodyContent',         // ? / NULL:
			);
			foreach($toVerify as $item){
				// some logic
			}
			*/
		}
	}

}


#if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/class.tx_imagemapwizard_softrefproc.php']) {
#	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/classes/class.tx_imagemapwizard_softrefproc.php']);
#}

?>