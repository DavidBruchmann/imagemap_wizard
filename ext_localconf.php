<?php

$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
#require_once($extPath . 'Classes/Controller/AbstractBaseController.php');
#require_once($extPath . 'Classes/Controller/TceFormUserElementController.php');
#require_once($extPath . 'Classes/Controller/ImagemapWizardController.php');
#require_once($extPath . 'Classes/Controller/TypoScriptParserController.php');
	
#if (TYPO3_MODE=='BE') {
	
	$imwizardConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imagemap_wizard']);
	$CType = 'textmedia';
	$availableMediaCTypes = ['textmedia','image','textpic'];
	if(isset($imwizardConf['defaultImageCtype']) && in_array($imwizardConf['defaultImageCtype'],$availableMediaCTypes)){
		$CType = $imwizardConf['defaultImageCtype'];
	}
	
	// HOOK:
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['softRefParser']['tx_imagemapwizard'] = 
		'EXT:'.$_EXTKEY.'/Classes/Hook/SoftRefParserObjHook.php:&SoftRefParserObjHook';
	
	# $GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']['tx_imagemapwizard_wizicon'] =
	#	$extPath.'Classes/class.tx_imagemapwizard_wizicon.php';
	
	#tx_imagemapwizard_parser::applyImageMap
	$typoscript = '
		includeLibs.imagemap_wizard = EXT:imagemap_wizard/Classes/Controller/TypoScriptParserController.php
		tt_content.imagemap_wizard < tt_content.'.$CType.'
		tt_content.imagemap_wizard.20.imgMax = 1
		tt_content.imagemap_wizard.20.maxW >
		tt_content.imagemap_wizard.20.1.imageLinkWrap >
		tt_content.imagemap_wizard.20.1.params = usemap="#***IMAGEMAP_USEMAP***"
		tt_content.imagemap_wizard.20.1.stdWrap.postUserFunc = Barlian\ImagemapWizard\Controller\TypoScriptParserController->applyImageMap
		tt_content.imagemap_wizard.20.1.stdWrap.postUserFunc.map.data = field:tx_imagemapwizard_links
		tt_content.imagemap_wizard.20.1.stdWrap.postUserFunc.map.name = field:titleText // field:altText // field:imagecaption // field:header
		tt_content.imagemap_wizard.20.1.stdWrap.postUserFunc.map.name.crop = 20
		tt_content.imagemap_wizard.20.1.stdWrap.postUserFunc.map.name.case = lower
		';
	if($imwizardConf['allTTCtypes']) {
		$typoscript .= '
			tt_content.imagemap_wizard.20.imgMax >
			tt_content.'.$CType.'.20 < tt_content.imagemap_wizard.20
			tt_content.imagemap_wizard.20.imgMax = 1
		';
	}
	if(1==2){ // some condition here
		// Not nice but working:
		// creating dummy plugin to satisfy ContentObjectRenderer::isClassAvailable (Since TYPO3 Version 7.x)
		$typoscript.= '
		plugin.tx_imagemapwizard_parser = USER
		plugin.tx_imagemapwizard_parser.includeLibs = EXT:imagemap_wizard/Classes/Controller/TypoScriptParserController.php
		';
		# tt_content.imagemap_wizard.20.1.stdWrap.postUserFunc.includeLibs = EXT:imagemap_wizard/Classes/Controller/TypoScriptParserController.php
	}
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
		$_EXTKEY,
		'setup',
		$typoscript,
		'defaultContentRendering'
	);
	// @TODO: display pageTSConfig for templavoila only when it's installed:
	$pageTSConfig = '
		mod.wizards.newContentElement.wizardItems.common.elements.imagemap {
			icon = EXT:imagemap_wizard/Resources/Public/img/tt_content_imagemap_v2_24x24.gif
			title = LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:imagemap.title
			description = LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:imagemap.description
			tt_content_defValues {
				CType = imagemap_wizard
			}
		}
		mod.wizards.newContentElement.wizardItems.common.show := addToList(imagemap)
	';
	/*
		templavoila.wizards.newContentElement.wizardItems.common.elements.imagemap {
			icon = EXT:imagemap_wizard/Resources/Public/img/tt_content_imagemap.gif
			title = LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:imagemap.title
			description = LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:imagemap.description
			tt_content_defValues {
				CType = imagemap_wizard
			}
		}
		templavoila.wizards.newContentElement.wizardItems.common.show := addToList(imagemap)
	';
	*/
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig($pageTSConfig);

#}
