<?php

/**
 * Definitions for routes provided by EXT:backend
 * Contains all AJAX-based routes for entry points
 *
 * Currently the "access" property is only used so no token creation + validation is made
 * but will be extended further.
 */
return array(
    'imagemap' => array(
        'path' => '/tx_imagemapwizard/imagemap',
        'target' => \Barlian\ImagemapWizard\Controller\ImagemapWizardController::class . '::main'
        #'target' => 'EXT:imagemap_wizard/classes/wizard/class.tx_imagemapwizard_wizard.php:tx_imagemapwizard_wizard->mainAction'
    ),
	/*
    // Spellchecker
    'rtehtmlarea_spellchecker' => array(
        'path' => '/rte/spellchecker',
        'target' => \TYPO3\CMS\Rtehtmlarea\Controller\SpellCheckingController::class . '::main'
    ),
	*/
);
