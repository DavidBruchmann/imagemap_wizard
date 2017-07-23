<?php

namespace Barlian\ImagemapWizard\Utilities;

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

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class WizardUtility {

	# Call to undefined function Barlian\ImagemapWizard\Utilities\WizardUtility\getServerRequestParams() 

	public function getServerRequestParams($PA){
		# DebuggerUtility::var_dump(array(__METHOD__,$PA,'debug_backtrace[0]'=>debug_backtrace()[0],'debug_backtrace[1]'=>debug_backtrace()[1])); //$params,$PA));
		if($PA instanceof \TYPO3\CMS\Core\Http\ServerRequest){
			$queryParams = $PA->getQueryParams();
			$params = $queryParams['P'];
		} else {
			$params = $PA;
		}
		return $params;
	}

	/**
	 *
	 * @return bool
	 */
	public static function checkRecordEditRequest(){
		$request = self::getRequest();
		$doIt = FALSE;
		if($request['route']=='/record/edit'){
			foreach($request as $k => $v){
				if(preg_match('/edit\[tt_content\]\[[0-9]*?\]/',$k) && $v=='edit'){
					$doIt = TRUE;
					continue;
				}
			}
		}
		return $doIt;
	}

	/**
	 *
	 * @return array
	 */
	public static function getRequest(){
		$request = $_REQUEST;
		$rawRequest = explode('&',str_replace('&amp;','&',urldecode($_SERVER['QUERY_STRING'])));
		foreach($rawRequest as $k => $v){
			$sub = explode('=',$v);
			if(strpos($sub[0],'[')){
				$request[$sub[0]] = isset($sub[1]) ? $sub[1] : '';
			}
		}
		return $request;
	}

}
