<?php

########################################################################
# Extension Manager/Repository config file for ext "imagemap_wizard".
#
# Auto generated 18-04-2012 20:12
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Imagemap Wizard',
	'description' => 'Provides an TYPO3 Wizard which enables interactive Imagemap-Creation - related to the TYPO3-Linkwizard.',
	'category' => 'be',
	'shy' => 0,
	'version' => '6.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_content',
	'clearcacheonload' => TRUE,
	'lockType' => '',
	'author' => 'Tolleiv Nietsch, David Bruchmann',
	'author_email' => 'extensions@<myfirstname>.de, david.bruchmann@gmail.com',
	'author_company' => '??, Webdevelopment Barlian',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.7-5.6.99', // @TODO
			'typo3' => '6.2.0-7.9.99', // @TODO
			'cms' => '0.0.0',
			'css_styled_content' => '0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => '',
);
