<?php

namespace Barlian\ImagemapWizard\Domain\Model;

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
use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

$extensionClassesPath = ExtensionManagementUtility::extPath('imagemap_wizard') . 'Classes/';
require_once($extensionClassesPath . 'Domain/Model/Mapper.php');
use \Barlian\Domain\Model\Mapper;
require_once($extensionClassesPath . 'Domain/Model/Typo3Env.php');
use \Barlian\Domain\Model\Typo3Env;

/**
 * Class/Function used to access the given Map-Data within Backend-Forms
 *
 * @author    Tolleiv Nietsch <info@tolleiv.de>
 */
class DataObject {
    protected $row;
    protected $liveRow;
    protected $table;
    protected $mapField;
    protected $backPath;
    protected $modifiedFlag = FALSE;
    protected $fieldConf;
	
	protected $extensionKey = 'imagemap_wizard';
	protected $extensionPath;
	protected $templaVoilaIsLoaded = FALSE;
	protected $templaVoilaPath = NULL;
	protected $compatVersion_6_0 = FALSE;
	protected $compatVersion_7_6 = FALSE;
	
	protected $mapperObj = NULL;
	protected $t3envObj = NULL;

    /**
     *
     * @param $table
     * @param $field
     * @param $uid
     * @param $currentValue
     * @return unknown_type
     */
    public function __construct($table, $field, $uid, $currentValue = NULL) {
		$this->extensionPath          = ExtensionManagementUtility::extPath($this->extensionKey);
		$this->templaVoilaPath        = NULL;
		if($this->templaVoilaIsLoaded = ExtensionManagementUtility::isLoaded('templavoila')){
			$this->templaVoilaPath    = ExtensionManagementUtility::extPath('templavoila');
		}
		// TODO: is it required or useful? :
		$this->compatVersion_6_0      = GeneralUtility::compat_version('6.0');
		$this->compatVersion_7_6      = GeneralUtility::compat_version('7.6');

        if (!in_array($table, array_keys($GLOBALS['TCA']))) {
			# var_dump(func_get_args());
            throw new \Exception('table (' . $table . ') is not defined in TCA.');
        }
        $this->table = $table;
		// @TODO: loadTCA($this->table) not required ?
        if (!in_array($field, array_keys($GLOBALS['TCA'][$table]['columns']))) {
            throw new \Exception('field (' . $field . ') is unknow for table in TCA. '."\n".'Consider running the Databas-Analyzer in the Install-Tool.');
        }
        $this->mapField = $field;
		$this->row = BackendUtility::getRecordWSOL($table, intval($uid));
		
        if ($currentValue) {
            $this->useCurrentData($currentValue);
        }
        $this->liveRow = $this->row;
		// This makes the difference between row and liveRow:
		BackendUtility::fixVersioningPid($table, $this->liveRow);
		
		$this->mapperObj = GeneralUtility::makeInstance('Barlian\Domain\Model\Mapper');
		$this->t3envObj  = GeneralUtility::makeInstance('Barlian\Domain\Model\Typo3Env');
		
		$this->map = $this->mapperObj->map2array($this->getFieldValue($this->mapField));

        $this->backPath = Typo3Env::getBackPath();
    }

    /**
     *
     * @param $field
     * @param $listNum
     * @return unknown_type
     */
    public function getFieldValue($field, $listNum = -1) {

        if (!is_array($this->row)) {
            return NULL;
        }
        $isFlex = $this->isFlexField($field);
        $parts = array();
        if ($isFlex) {
            $parts = explode(':', $field);
            $dbField = $parts[2];
        } else {
            $dbField = $field;
        }

        if (!array_key_exists($dbField, $this->row)) {
            return NULL;
        }

        $data = $this->row[$dbField];
        if ($isFlex) {
			$xml = GeneralUtility::xml2array($data);
			$tools = GeneralUtility::makeInstance('TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools'); //t3lib_flexformtools');
            $data = $tools->getArrayValueByPath($parts[3], $xml);
        }

        if ($listNum == -1) {
            return $data;
        } else {
            $tmp = preg_split('/,/', $data);
            return $tmp[$listNum];
        }
        return NULL;

    }

    /**
     * Fetches the first file reference from FAL
     *
     * @param string $field
     * @return string|NULL
     */
    public function getFalFieldValue($field) {
        $image = NULL;
        if (!is_array($this->row)) {
            return NULL;
        }
        $db = $GLOBALS['TYPO3_DB'];
        $row = $db->exec_SELECTgetSingleRow(
            'sys_file.identifier',
            'sys_file, sys_file_reference',
            'sys_file.uid = sys_file_reference.uid_local AND ' .
            'sys_file_reference.uid_foreign = ' . intval($this->row['uid']),
            '',
            'sorting_foreign ASC'
        );
        if (!$row) return NULL;
        $identifier = $row['identifier'];
        $someFileIdentifier = $identifier;
        $storageRepository = GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
        $storage = $storageRepository->findByUid(1);
        $file = $storage->getFile($someFileIdentifier);
        return $file->getPublicUrl();
    }

    /**
     *    Retrives current imagelocation - if multiple files are stored in the field only the first is recognized
     *
     * @param $abs
     * @return string
     */
    public function getImageLocation($abs = FALSE) {
        $location = '';
        $imageField = $this->determineImageFieldName();
		// TODO: make it smarter
        if ($this->compatVersion_6_0 || $this->compatVersion_7_0 || $this->compatVersion_8_0) {
            $location = $this->getFalFieldValue($imageField);
        } else {
            if ($this->isFlexField($imageField)) {
                $path = $this->getFieldConf('config/userImage/uploadfolder');
            } else {
                $path = $GLOBALS['TCA'][$this->table]['columns'][$imageField]['config']['uploadfolder'];
            }
            $location = $path . '/' . $this->getFieldValue($imageField, 0);
        }
        return ($abs ? PATH_site : $this->backPath) . $location;
    }

    /**
     *
     * @return boolean
     */
    public function hasValidImageFile() {
        return $this->getFieldValue('uid') &&
            is_file($this->getImageLocation(TRUE)) &&
            is_readable($this->getImageLocation(TRUE));
    }

    /**
     *    Renders the image within a frontend-like context
     *
     * @return string
     */
    public function renderImage() {
		if (!$this->t3envObj->initTSFE($this->getLivePid(), $GLOBALS['BE_USER']->workspace, $GLOBALS['BE_USER']->user['uid'])) {
            return 'Can\'t render image since TYPO3 Environment is not ready.<br/>Error was:' . $this->t3envObj->get_lastError();
        }
        $conf = array('table' => $this->table, 'select.' => array('uidInList' => $this->getLiveUid(), 'pidInList' => $this->getLivePid()));

        if ($this->templaVoilaIsLoaded) {
			// @TODO
            require_once($this->templaVoilaPath . 'pi1/class.tx_templavoila_pi1.php');
        }
        //render like in FE with WS-preview etc...
        $this->t3envObj->pushEnv();
		if($GLOBALS['TYPO3_MODE'] == 'BE'){
			$this->t3envObj->setEnv('');
		} else {
			// is this ever called from FE?
			$this->t3envObj->setEnv(PATH_site);
		}
        $this->t3envObj->resetEnableColumns('pages'); // enable rendering on access-restricted pages
        $this->t3envObj->resetEnableColumns('pages_language_overlay');
        $this->t3envObj->resetEnableColumns($this->table); // no fe_group, start/end, hidden restrictions needed :P
        $GLOBALS['TSFE']->cObj->LOAD_REGISTER(array('keepUsemapMarker' => '1'), 'LOAD_REGISTER');
		// HOW THE IMAGE IS PRODUCED HERE???
        $result = $GLOBALS['TSFE']->cObj->CONTENT($conf);
        $this->t3envObj->popEnv();

        // extract the image
        $matches = array();
		$regex_strict = '/(<img[^>]+usemap="[^"]+"[^>]*\/?>)/';
		$regex_permissive = '/(<img[^>]+(usemap="[^"]+")?[^>]*\/?>)/';
        if (!preg_match($regex_permissive, $result, $matches)) {
            // @TODO: consider to use the normal image as fallback here instead of showing an error-message
            return 'No Image rendered from TSFE. :(<br/>'.
				'Has the page some kind of special doktype or has it access-restrictions?<br/>'.
				'There are lot\'s of things which can go wrong since normally nobody creates frontend-output in the backend ;)<br/>'.
				'Error was:' . $this->t3envObj->get_lastError();
        }
		// @TODO: anything to fix in the path???
        $result = str_replace('src="', 'src="' . ($this->backPath), $matches[1]);
/*
DebuggerUtility::var_dump(array(
	'__METHOD__'=>__METHOD__,
	'$conf'=>$conf,
	'$this->backPath'=>$this->backPath,
	'$result'=>$result,
	 '$GLOBALS[TSFE]->cObj'=> $GLOBALS['TSFE']->cObj,
	 '$this->t3envObj' => $this->t3envObj,
	 'backtrace'=>debug_backtrace()
));
*/
        return $result;
    }

    /**
     *  Renders a thumbnail with preconfiguraed dimensions
	 *
	 * @TODO: render thumbnail smaller
     *
     * @param $confKey
     * @param $defaultMaxWH
     * @return unknown_type
     */
    public function renderThumbnail($confKey, $defaultMaxWH) {
		$maxSize = $this->t3envObj->getExtConfValue($confKey, $defaultMaxWH);
		$img = $this->renderImage();
        $matches = array();
        if (preg_match('/width="(\d+)" height="(\d+)"/', $img, $matches)) {
            $width = intval($matches[1]);
            $height = intval($matches[2]);
            if (($width > $maxSize) && ($width >= $height)) {
                $height = ($maxSize / $width) * $height;
                $width = $maxSize;
            } else if ($height > $maxSize) {
                $width = ($maxSize / $height) * $width;
                $height = $maxSize;
            }
            return preg_replace('/width="(\d+)" height="(\d+)"/', 'width="' . $width . '" height="' . $height . '"', $img);
        } else {
            return '';
        }
    }

    /**
     * Calculates the scale-factor which is required to scale down the imagemap to the thumbnail
     *
     * @param $confKey
     * @param $defaultMaxWH
     * @return float
     */
    public function getThumbnailScale($confKey, $defaultMaxWH) {
		$maxSize = $this->t3envObj->getExtConfValue($confKey, $defaultMaxWH);
		$ret = 1;
        $img = $this->renderImage();
        $matches = array();
        if (preg_match('/width="(\d+)" height="(\d+)"/', $img, $matches)) {
            $width = intval($matches[1]);
            $height = intval($matches[2]);
            if (($width > $maxSize) && ($width >= $height)) {
                $ret = ($maxSize / $width);
            } else if ($height > $maxSize) {
                $ret = ($maxSize / $height);
            }
        }
        return $ret;
    }

    /**
     *
     * @param $template
     * @return string
     */
    public function listAreas($template = "") {
        if (!is_array($this->map["#"])) {
            return '';
        }
        $result = '';
        foreach ($this->map["#"] as $area) {
            $markers = array(
				"##coords##"     => $area["@"]["coords"],
				"##shape##"      => ucfirst($area["@"]["shape"]),
				"##color##"      => $this->attributize($area["@"]["color"]),
				"##link##"       => $this->attributize($area["value"]),
				"##alt##"        => $this->attributize($area["@"]["alt"]),
				"##attributes##" => $this->listAttributesAsSet($area)
			);
            $result .= str_replace(array_keys($markers), array_values($markers), $template);
        }
        return $result;
    }

    /**
     *
     * @param $area
     * @return string
     */
    protected function listAttributesAsSet($area) {
        $relAttr = $this->getAttributeKeys();
        $ret = array();
        foreach ($relAttr as $key) {
            $ret[] = $key . ':\'' . $this->attributize(array_key_exists($key, $area["@"]) ? $area["@"][$key] : '') . '\'';
        }
        return implode(',', $ret);
    }

    /**
     *
     * @return string
     */
    public function emptyAttributeSet() {
        $relAttr = $this->getAttributeKeys();
        $ret = array();
        foreach ($relAttr as $key) {
            if ($key) {
                $ret[] = $key . ':\'\'';
            }
        }
        return implode(',', $ret);
    }

    /**
     *
     * @param $v
     * @return string
     */
    protected function attributize($v) {
        $attr = preg_replace('/([^\\\\])\\\\\\\\\'/', '\1\\\\\\\\\\\'', str_replace('\'', '\\\'', $v));
        if ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] != 'utf-8') {
            $attr = '\' + jQuery.base64Decode(\'' . base64_encode($attr) . '\') + \'';
        }
        return $attr;
    }

    /**
     *
     * @return array
     */
    public function getAttributeKeys() {
		$keys = GeneralUtility::trimExplode(
			',', TYPO3Env::getExtConfValue('additionalAttributes', '')
		);
		$keys = array_diff($keys, array('alt', 'href', 'shape', 'coords'));
        $keys = array_map("strtolower", $keys);
        return array_filter($keys);
    }

    /**
     *
     * @return int
     */
    protected function getLivePid() {
        return $this->row['pid'] > 0 ? $this->row['pid'] : $this->liveRow['pid'];
    }

    /**
     *
     * @return int
     */
    protected function getLiveUid() {
        return (($GLOBALS['BE_USER']->workspace===0) || ($this->row['t3ver_oid'] == 0)) ? $this->row['uid'] : $this->row['t3ver_oid'];
    }

    /**
     *
     * @return string
     */
    protected function determineImageFieldName() {
        $imgField = $this->getFieldConf('config/userImage/field') ? $this->getFieldConf('config/userImage/field') : 'image';
        if ($this->isFlexField($this->mapField)) {
            $imgField = preg_replace('/\/[^\/]+\/(v\S+)$/', '/' . $imgField . '/\1', $this->mapField);
        }
        return $imgField;
    }

    /**
     *
     * @return string
     */
    public function getTablename() {
        return $this->table;
    }

    /**
     *
     * @return string
     */
    public function getFieldname() {
        return $this->mapField;
    }

    /**
     *
     * @return array
     */
    public function getRow() {
        return $this->row;
    }

    /**
     *
     * @return int
     */
    public function getUid() {
        return $this->row['uid'];
    }

    /**
     *
     * @param $value
     * @return void
     */
    public function useCurrentData($value) {
        $cur = $this->getCurrentData();
		if (!$this->mapperObj->compareMaps($cur, $value)) {
			$this->modifiedFlag = TRUE;
		}
        if ($this->isFlexField($this->mapField)) {
            $parts = explode(':', $this->mapField);
			// @TODO
			if(1==2){ // VERSION < 7.0
				$tools = t3lib_div::makeInstance('TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools'); //t3lib_flexformtools');
				$data = t3lib_div::xml2array($this->row[$parts[2]]);
			} else {
				$tools = GeneralUtility::makeInstance('TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools'); //t3lib_flexformtools');
				$data = GeneralUtility::xml2array($this->row[$parts[2]]);
			}
            $tools->setArrayValueByPath($parts[3], $data, $value);
            $this->row[$parts[2]] = $tools->flexArray2Xml($data);
        } else {
            $this->row[$this->mapField] = $value;
        }
    }

    /**
     *
     * @return string
     */
    public function getCurrentData() {
        if ($this->isFlexField($this->mapField)) {
            $parts = explode(':', $this->mapField);
			// @TODO
			if(1==2){ // VERSION < 7.0
				$tools = t3lib_div::makeInstance('TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools'); //t3lib_flexformtools');
				$data = t3lib_div::xml2array($this->row[$parts[2]]);
			} else {
				$tools = GeneralUtility::makeInstance('TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools'); //t3lib_flexformtools');
				$data = GeneralUtility::xml2array($this->row[$parts[2]]);
			}
            return $tools->getArrayValueByPath($parts[3], $data);
        } else {
            return $this->row[$this->mapField];
        }
    }

    /**
     *
     * @return boolean
     */
    public function hasDirtyState() {
        return $this->modifiedFlag;
    }

    /**
     *
     * @param $cfg
     * @return void
     */
    public function setFieldConf($cfg) {
        $this->fieldConf = $cfg;
    }

    /**
     *
     * @param $subKey
     * @return array
     */
    public function getFieldConf($subKey = NULL) {
        if ($subKey == NULL) {
            return $this->fieldConf;
        }
		$tools = GeneralUtility::makeInstance('TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools'); //t3lib_flexformtools');
		return $tools->getArrayValueByPath($subKey, $this->fieldConf);
    }

    /**
     *
     * @param $field
     * @return boolesan
     */
    protected function isFlexField($field) {
        $theField = $field;
        if (stristr($field, ':')) {
            $parts = explode(':', $field);
            $theField = $parts[2];
        }
        return ($GLOBALS['TCA'][$this->table]['columns'][$theField]['config']['type'] == 'flex');
    }
	
	// required?
	public function setBackPath($backPath){
		$this->backPath = $backPath;
	}
	
	// required?
	public function getBackPath(){
		return $this->backPath;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/Domain/Model/DataObject.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/imagemap_wizard/Classes/Domain/Model/DataObject.php']);
}
