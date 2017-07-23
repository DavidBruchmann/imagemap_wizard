<?php

$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('imagemap_wizard') . 'Classes/';
$default = array(
	'Barlian\\ImagemapWizard\\Controller\\TceFormUserElementController' => $extensionClassesPath . 'Controller/TceFormUserElementController.php',
	'Barlian\ImagemapWizard\Controller\ImagemapWizardController' => $extensionClassesPath . 'Controller/ImagemapWizardController.php',
	'Barlian\ImagemapWizard\Controller\TypoScriptParserController' => $extensionClassesPath . 'Controller/TypoScriptParserController.php',
	'Barlian\ImagemapWizard\Domain\Model\DataObject' => $extensionClassesPath . 'Domain/Model/DataObject.php',
	'Barlian\ImagemapWizard\Domain\Model\Mapper' => $extensionClassesPath . 'Domain/Model/Mapper.php',
	'Barlian\ImagemapWizard\Domain\Model\Typo3Env' => $extensionClassesPath . 'Domain/Model/Typo3Env.php',
	'Barlian\ImagemapWizard\Hook\PageRendererHooks' => $extensionClassesPath . 'Hook/PageRendererHooks.php',
	'Barlian\ImagemapWizard\Hook\SoftRefParserObjHook' => $extensionClassesPath . 'Hook/SoftRefParserObjHook.php',
	'Barlian\ImagemapWizard\Hook\WiziconHook' => $extensionClassesPath . 'Hook/WiziconHook.php',
	'Barlian\ImagemapWizard\View\AbstractView' => $extensionClassesPath . 'View/AbstractView.php',
	'Barlian\ImagemapWizard\View\TceformView' => $extensionClassesPath . 'View/TceformView.php',
	'Barlian\ImagemapWizard\View\WizardView' => $extensionClassesPath . 'View/WizardView.php',
	// 'Barlian\Wizard\ImageMapWizard' => $extensionClassesPath . 'Wizard/ImageMapWizard.php',
);
return $default;

?>