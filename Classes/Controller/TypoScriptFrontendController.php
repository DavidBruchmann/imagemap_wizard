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

use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use TYPO3\CMS\Core\Localization\Locales;
# use TYPO3\CMS\Core\Localization\LocalizationFactory;

class TypoScriptFrontendController extends \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
{

    /**
     * copy of TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::__construct()
	 * just the function initPageRenderer() is not called.
	 * So it's possible to call TSFE from BE without disturbing the BE-pageRenderer
     *
     * @param array $TYPO3_CONF_VARS The global $TYPO3_CONF_VARS array. Will be set internally in ->TYPO3_CONF_VARS
     * @param mixed $id The value of GeneralUtility::_GP('id')
     * @param int $type The value of GeneralUtility::_GP('type')
     * @param bool|string $no_cache The value of GeneralUtility::_GP('no_cache'), evaluated to 1/0
     * @param string $cHash The value of GeneralUtility::_GP('cHash')
     * @param string $jumpurl The value of GeneralUtility::_GP('jumpurl'), unused since TYPO3 CMS 7. Will have no effect in TYPO3 CMS 8 anymore
     * @param string $MP The value of GeneralUtility::_GP('MP')
     * @param string $RDCT The value of GeneralUtility::_GP('RDCT')
     * @see \TYPO3\CMS\Frontend\Http\RequestHandler
     */
    public function __construct($TYPO3_CONF_VARS, $id, $type, $no_cache = '', $cHash = '', $jumpurl = '', $MP = '', $RDCT = '')
    {
		// TODO: get the right version
		// if(version=7.6) {
			// Setting some variables:
			$this->TYPO3_CONF_VARS = $TYPO3_CONF_VARS;
		// }
		$this->id = $id;
		$this->type = $type;
		if ($no_cache) {
			if ($this->TYPO3_CONF_VARS['FE']['disableNoCacheParameter']) {
				$warning = '&no_cache=1 has been ignored because $TYPO3_CONF_VARS[\'FE\'][\'disableNoCacheParameter\'] is set!';
				$this->getTimeTracker()->setTSlogMessage($warning, 2);
			} else {
				$warning = '&no_cache=1 has been supplied, so caching is disabled! URL: "' . GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL') . '"';
				$this->disableCache();
			}
			GeneralUtility::sysLog($warning, 'cms', GeneralUtility::SYSLOG_SEVERITY_WARNING);
		}
		$this->cHash = $cHash;
		// TODO: get the right version
		// if(version=7.6) {
			$this->jumpurl = $jumpurl;
		// }
		$this->MP = $this->TYPO3_CONF_VARS['FE']['enable_mount_pids'] ? (string)$MP : '';
		$this->RDCT = $RDCT;
		// TODO: get the right version
		// if(version=7.6) {
			$this->clientInfo = GeneralUtility::clientInfo();
		// }
		$this->uniqueString = md5(microtime());
		$this->csConvObj = GeneralUtility::makeInstance(CharsetConverter::class);
		$tmpPageRenderer = $this->initPageRenderer();
		// Call post processing function for constructor:
		if (is_array($this->TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['tslib_fe-PostProc'])) {
			$_params = ['pObj' => &$this];
			foreach ($this->TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['tslib_fe-PostProc'] as $_funcRef) {
				GeneralUtility::callUserFunction($_funcRef, $_params, $this);
			}
		}
		$this->cacheHash = GeneralUtility::makeInstance(CacheHashCalculator::class);
		$this->initCaches();
    }

    /**
     * copy of TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::__construct()
	 * just without setting backPath and template
     */
    protected function initPageRenderer()
    {
			#if($this->pageRenderer !== null) {
			#	return;
			#}
		if(TYPO3_MODE==='BE'){
			$this->tmpPageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
			$this->tmpPageRenderer->disableCompressJavascript();
			$this->tmpPageRenderer->disableCompressCss();
			$this->tmpPageRenderer->disableConcatenateFiles();
			$this->tmpPageRenderer->disableConcatenateJavascript();
			$this->tmpPageRenderer->disableConcatenateCss();
			$this->tmpPageRenderer->disableRemoveLineBreaksFromTemplate();
			$this->tmpPageRenderer->setBackPath('./');
			/*
			$this->pageRenderer->setRequireJsPath($path);
			$this->pageRenderer->setExtJsPath($path);
			$this->pageRenderer->setTemplateFile($file);
			$this->pageRenderer->setBodyContent($content);
			$this->pageRenderer->setBaseUrl($baseUrl);
			$this->pageRenderer->etIconMimeType($iconMimeType);
			$this->pageRenderer->
			$this->pageRenderer->
			*/
			// $this->pageRenderer->setTemplateFile('EXT:frontend/Resources/Private/Templates/MainPage.html');
			
		}
    }

    /**
     * Initializing the getLL variables needed.
     *
     * @return void
     */
    public function initLLvars()
    {
        /*
		// Init languageDependencies list
        $this->languageDependencies = [];
        // Setting language key and split index:
        $this->lang = $this->config['config']['language'] ?: 'default';
        $this->pageRenderer->setLanguage($this->lang);

        // Finding the requested language in this list based
        // on the $lang key being inputted to this function.
        // @var $locales Locales
        $locales = GeneralUtility::makeInstance(Locales::class);
        $locales->initialize();

        // Language is found. Configure it:
        if (in_array($this->lang, $locales->getLocales())) {
            $this->languageDependencies[] = $this->lang;
            foreach ($locales->getLocaleDependencies($this->lang) as $language) {
                $this->languageDependencies[] = $language;
            }
        }

        // Setting charsets:
        $this->renderCharset = $this->csConvObj->parse_charset($this->config['config']['renderCharset'] ? $this->config['config']['renderCharset'] : 'utf-8');
        // Rendering charset of HTML page.
        $this->metaCharset = $this->csConvObj->parse_charset($this->config['config']['metaCharset'] ? $this->config['config']['metaCharset'] : $this->renderCharset);
		*/
    }
	
}