<?php

/**
 * Definitions of routes
 */
return array(
    // Register RTE browse links wizard
    'wizard_imagemap' => array(
        'path' => '/wizard/tx_imagemapwizard/imagemap',
        'target' => \Barlian\ImagemapWizard\Controller\ImagemapWizardController::class . '::WizardAction'
        #'target' => 'tx_imagemapwizard_wizard::mainAction'
    ),
	/*
    // Register RTE select image wizard
    'rtehtmlarea_wizard_select_image' => array(
        'path' => '/rte/wizard/image',
        'target' => \TYPO3\CMS\Rtehtmlarea\Controller\SelectImageController::class . '::mainAction'
    ),
    // Register RTE user elements wizard
    'rtehtmlarea_wizard_user_elements' => array(
        'path' => '/rte/wizard/userelements',
        'target' => \TYPO3\CMS\Rtehtmlarea\Controller\UserElementsController::class . '::mainAction'
    ),
    // Register RTE parse html wizard
    'rtehtmlarea_wizard_parse_html' => array(
        'path' => '/rte/wizard/parsehtml',
        'target' => \TYPO3\CMS\Rtehtmlarea\Controller\ParseHtmlController::class . '::mainAction'
    ),
	*/
);
