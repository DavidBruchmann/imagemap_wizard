<?php
defined('TYPO3_MODE') || die();

$_EXTKEY = 'imagemap_wizard';
$table = 'tt_content';
$field = 'tx_imagemapwizard_links';

// --- getting default element for images like configured in extension-manager ---
$imwizardConf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] );

$tmp_imagemap_wizard_columns = [
    $field => [
        'exclude' => true,
        'label' => 'LLL:EXT:imagemap_wizard/Resources/Private/Language/locallang_db.xlf:tx_imagemapwizard_domain_model_imagemapwizard.tx_imagemapwizard_links',
        'config' => [
			'type' => 'user', // 'text',
			'userFunc' => 'Barlian\ImagemapWizard\Controller\TceFormUserElementController->renderForm',
			'cols' => 80, // 40,
			'rows' => 15,
			'wizards' => array(
				'imagemap' => array(
					'type' => 'popup',
					'title' => 'ImageMap',
					'JSopenParams' => 'height=700,width=780,status=0,menubar=0,scrollbars=1',
					'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/img/link_popup.gif',
					// https://stackoverflow.com/questions/38099950/how-to-add-custom-wizards-in-typo3-7-tca/41931597#41931597
					'module' => array(
						'name' => 'wizard_imagemap',
						'urlParameters' => array(
							'mode' => 'wizard',
							'ajax' => '0'
							// 'any' => '... parameters you need'
						),
					),
				),
				'_VALIGN' => 'middle',
				'_PADDING' => '4',
			),
			'softref'=>'tx_imagemapwizard',
		    // 'eval' => 'trim',
		]
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $tmp_imagemap_wizard_columns);  /* , 1 */
	// @see https://forge.typo3.org/issues/54899
	// @see https://forum.typo3.org/index.php/t/201223/
	// $addTofeInterface DEPRECATED: Usage of feInterface is no longer part of the TYPO3 CMS Core. Please check EXT:statictemplates.

// --- The CType like 'textmedia' is used to copy the configuration on the imagemap-configuraton ---
$CType = 'textmedia';
$availableMediaCTypes = ['textmedia','image','textpic'];
if(isset($imwizardConf['defaultImageCtype']) && in_array($imwizardConf['defaultImageCtype'],$availableMediaCTypes)){
	$CType = $imwizardConf['defaultImageCtype'];
}

// --- copy configuration from default element for images
$GLOBALS['TCA'][$table]['types'][$_EXTKEY] = $GLOBALS['TCA'][$table]['types'][$CType];

// --- Determining where the Wizard has to be shown in the BE-Form ---
$CTypeMediaField = 'assets';
if(in_array($CType,['image','textpic'])){
	$CTypeMediaField = 'image';
}

// --- showing the wizard ---
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	$table,
	$field,
	($imwizardConf['allTTCtypes'] ? '' : $_EXTKEY),
	'after:'.$CTypeMediaField
);
