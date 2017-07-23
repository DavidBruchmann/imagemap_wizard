<?php

$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
$extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY);

#$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = array(
#    0 => 'LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:imagemap.title',
#    1 => 'imagemap_wizard',
#    2 => 'EXT:' . $_EXTKEY . '/Resources/Public/img/tt_content_image.gif',
#);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
	array(
		'LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:imagemap.title',
		$_EXTKEY,
		'EXT:' . $_EXTKEY . '/Resources/Public/img/tt_content_imagemap_v2_24x24.gif'
	),
	'CType'
);
# TypeError: parent.opener.imagemapwizard_valueChanged is not a function[Learn More]
$tempColumns = array (
	'tx_imagemapwizard_links' => array(
		'label' => 'LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang.xml:tt_content.tx_imagemapwizard_links',
		'config' => array (
			'type' => 'user',
			'userFunc' => 'Barlian\ImagemapWizard\Controller\TceFormUserElementController->renderForm',
			'cols' => 80,
			'rows' => 15,
			'wizards' => array(
				'imagemap' => array(
					'type' => 'popup',
					'title' => 'ImageMap',
					'JSopenParams' => 'height=700,width=780,status=0,menubar=0,scrollbars=1',
					'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/img/link_popup.gif',
					'module' => array(
						'name' => 'wizard_imagemap',
						'urlParameters' => array(
							'mode' => 'wizard',
							'ajax' => '0'
						),
					),
				),
				'_VALIGN' => 'middle',
				'_PADDING' => '4',
			),
			'softref'=>'tx_imagemapwizard',
		),
	),
);

/*
http://localhost/_typo3/_PROJECTS/dgpt.de/2016/v7/typo3/index.php?
	route=%2Frecord%2Fedit&
	token=ec2119c9fbf65fe26056d15634072a36faa4671d&
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
	P[returnUrl]=%2F_typo3%2F_PROJECTS%2Fdgpt.de%2F2016%2Fv7%2Ftypo3%2Findex.php%3F
		route=%252Frecord%252Fedit%26
		token=ec2119c9fbf65fe26056d15634072a36faa4671d%26
		edit%5Btt_content%5D%5B 1258 %5D%3Dedit%26
		returnUrl%3D%252F_typo3%252F_PROJECTS%252Fdgpt.de%252F2016%252Fv7%252Ftypo3%252Findex.php%253FM%253D
			web_list%2526moduleToken%253Dd70a1ea63ea6277436dbc055f1b756e741ece4d6%2526id%253D5&
			P[formName]=editform&
			P[itemName]=data%
			5Btt_content%5D%5B 1258 %5D%5Btx_imagemapwizard_links%5D&
			P[hmac]=7726b5417ddcffce8c94b7c8a62c287932e4c996&
			P[fieldChangeFunc][0]=imagemapwizard_valueChanged%28field%29%3B&
			P[fieldChangeFuncHash]=2133ee975b6f32ef788022b18ec036af61477b06&P[currentValue]=&
			P[currentSelectedValues]=
	
	http://localhost/_typo3/_PROJECTS/dgpt.de/2016/v7/typo3/index.php?route=%2
		Fwizard%2Frecord%2Fbrowse&
		token=5a958c669ee4f5c582511cb265bd9279cee5c5d7&
		mode=db&
		bparams=data[pages][5][content_from_pid]|||pages
*/

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content",$tempColumns,1);

$imwizardConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imagemap_wizard']);

$GLOBALS['TCA']['tt_content']['types']['imagemap_wizard'] = $GLOBALS['TCA']['tt_content']['types']['image'];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tt_content',
	'tx_imagemapwizard_links',
	($imwizardConf['allTTCtypes'] ? '' : 'imagemap_wizard'),
	'after:image'
);

#if (TYPO3_MODE=='BE')    {

	// CSH context sensitive help
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
		'tt_content',
		$extPath.'Resources/Private/Language/locallang_csh_ttc.xml'
	);

	$icons = array(
		'redo' => $extRelPath . 'Resources/Public/img/arrow_redo.png',
		'link' => $extRelPath . 'Resources/Public/img/link_edit.png',
		'zoomin' => $extRelPath . 'Resources/Public/img/magnifier_zoom_in.png',
		'zoomout' => $extRelPath . 'Resources/Public/img/magnifier_zoom_out.png',
	);
	// TODO:
	\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($icons, $_EXTKEY);

	// HOOKS:
	$GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']['Barlian\ImagemapWizard\Hook\WiziconHook'] =
		$extPath.'Classes/Hook/WiziconHook.php';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['tx_imagemapwizard'] =
		'Barlian\ImagemapWizard\Hook\PageRendererHooks->FixBackPath';
		//'EXT:'.$_EXTKEY.'/Classes/Hook/PageRendererHooks.php:&PageRendererHooks->FixBackPath';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['tx_imagemapwizard'] =
		'Barlian\ImagemapWizard\Hook\PageRendererHooks->RestoreBackPath';
		//'EXT:'.$_EXTKEY.'/Classes/Hook/PageRendererHooks.php:&PageRendererHooks->RestoreBackPath';
		
#}

?>