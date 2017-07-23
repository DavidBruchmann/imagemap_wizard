<?php

namespace Barlian\ImagemapWizard\Wizard;

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
use \TYPO3\CMS\Core\Utility\BackendUtility;


/**
 * 
 *
 * @author	Tolleiv Nietsch <info@tolleiv.de>
 */
class ImageMapWizard extends \TYPO3\CMS\Backend\Controller\Wizard\AbstractWizardController {
{
    /**
     * Wizard parameters, coming from FormEngine linking to the wizard.
     *
     * @var array
     */
    public $wizardParameters;

    /**
     * Serialized functions for changing the field...
     * Necessary to call when the value is transferred to the FormEngine since the form might
     * need to do internal processing. Otherwise the value is simply not be saved.
     *
     * @var string
     */
    public $fieldChangeFunc;

    /**
     * @var string
     */
    protected $fieldChangeFuncHash;

    /**
     * Form name (from opener script)
     *
     * @var string
     */
    public $fieldName;

    /**
     * Field name (from opener script)
     *
     * @var string
     */
    public $formName;

    /**
     * ID of element in opener script for which to set color.
     *
     * @var string
     */
    public $md5ID;

    /**
     * Error message if image not found.
     *
     * @var string
     */
    public $imageError = '';

    /**
     * Document template object
     *
     * @var DocumentTemplate
     */
    public $doc;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    protected $exampleImg;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->getLanguageService()->includeLLFile('EXT:lang/locallang_wizards.xlf');
        $GLOBALS['SOBE'] = $this;

        $this->init();
    }

    /**
     * Initialises the Class
     *
     * @return void
     */
    protected function init()
    {
        // Setting GET vars (used in frameset script):
        $this->wizardParameters = GeneralUtility::_GP('P');
        // Setting GET vars (used in colorpicker script):
        $this->colorValue = GeneralUtility::_GP('colorValue');
        $this->fieldChangeFunc = GeneralUtility::_GP('fieldChangeFunc');
        $this->fieldChangeFuncHash = GeneralUtility::_GP('fieldChangeFuncHash');
        $this->fieldName = GeneralUtility::_GP('fieldName');
        $this->formName = GeneralUtility::_GP('formName');
        $this->md5ID = GeneralUtility::_GP('md5ID');
        $this->exampleImg = GeneralUtility::_GP('exampleImg');
        // Resolving image (checking existence etc.)
        $this->imageError = '';
        if ($this->exampleImg) {
            $this->pickerImage = GeneralUtility::getFileAbsFileName($this->exampleImg);
            if (!$this->pickerImage || !@is_file($this->pickerImage)) {
                $this->imageError = 'ERROR: The image "' . $this->exampleImg . '" could not be found!';
            }
        }
        $update = [];
        if ($this->areFieldChangeFunctionsValid()) {
            // Setting field-change functions:
            $fieldChangeFuncArr = unserialize($this->fieldChangeFunc);
            unset($fieldChangeFuncArr['alert']);
            foreach ($fieldChangeFuncArr as $v) {
                $update[] = 'parent.opener.' . $v;
            }
        }
        // Initialize document object:
        $this->doc = GeneralUtility::makeInstance(DocumentTemplate::class);
        $this->getPageRenderer()->loadRequireJsModule(
            'TYPO3/CMS/Backend/Wizard/Colorpicker',
            'function(Colorpicker) {
				Colorpicker.setFieldChangeFunctions({
					fieldChangeFunctions: function() {'
                        . implode('', $update) .
                    '}
				});
			}'
        );
        // Start page:
        $this->content .= $this->doc->startPage($this->getLanguageService()->getLL('colorpicker_title'));
    }

    /**
     * Injects the request object for the current request or subrequest
     * As this controller goes only through the main() method, it is rather simple for now
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function mainAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->main();

        $this->content .= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);

        $response->getBody()->write($this->content);
        return $response;
    }

    /**
     * Main Method, rendering either colorpicker or frameset depending on ->showPicker
     *
     * @return void
     */
    public function main()
    {
        // Show frameset by default:
        if (!GeneralUtility::_GP('showPicker')) {
            $this->frameSet();
        } else {
            // Putting together the items into a form:
            $content = '
			<form name="colorform" method="post" action="' . htmlspecialchars(BackendUtility::getModuleUrl('wizard_colorpicker')) . '">
			...
			</form>
			';
		}
		#$this->content .= '<h2>' . $this->getLanguageService()->getLL('colorpicker_title', true) . '</h2>';
		$this->content = '<h2>WIZARD IMAGEMAP</h2>';
		$this->content .= $content;
	}
	
	public function processAjaxRequest(){
		
	}

    /**
     * Returns the sourcecode to the browser
     *
     * @return void
     * @deprecated since TYPO3 CMS 7, will be removed in TYPO3 CMS 8, use mainAction() instead
     */
    public function printContent()
    {
        GeneralUtility::logDeprecatedFunction();
        $this->content .= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);
        echo $this->content;
    }

    /**
     * Returns a frameset so our JavaScript Reference isn't lost
     * Took some brains to figure this one out ;-)
     * If Peter wouldn't have been I would've gone insane...
     *
     * @return void
     */
    public function frameSet()
    {
        $this->getDocumentTemplate()->JScode = $this->getDocumentTemplate()->wrapScriptTags('
				if (!window.opener) {
					alert("ERROR: Sorry, no link to main window... Closing");
					close();
				}
		');
        $this->getDocumentTemplate()->startPage($this->getLanguageService()->getLL('colorpicker_title'));

        // URL for the inner main frame:
        $url = BackendUtility::getModuleUrl(
            'wizard_colorpicker',
            [
                'showPicker' => 1,
                'colorValue' => $this->wizardParameters['currentValue'],
                'fieldName' => $this->wizardParameters['itemName'],
                'formName' => $this->wizardParameters['formName'],
                'exampleImg' => $this->wizardParameters['exampleImg'],
                'md5ID' => $this->wizardParameters['md5ID'],
                'fieldChangeFunc' => serialize($this->wizardParameters['fieldChangeFunc']),
                'fieldChangeFuncHash' => $this->wizardParameters['fieldChangeFuncHash'],
            ]
        );
        $this->content = $this->getPageRenderer()->render(PageRenderer::PART_HEADER) . '
			<frameset rows="*,1" framespacing="0" frameborder="0" border="0">
				<frame name="content" src="' . htmlspecialchars($url) . '" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto" noresize="noresize" />
				<frame name="menu" src="' . htmlspecialchars(BackendUtility::getModuleUrl('dummy')) . '" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize" />
			</frameset>
		</html>';
    }

    /**
     * Determines whether submitted field change functions are valid
     * and are coming from the system and not from an external abuse.
     *
     * @return bool Whether the submitted field change functions are valid
     */
    protected function areFieldChangeFunctionsValid()
    {
        return $this->fieldChangeFunc && $this->fieldChangeFuncHash && $this->fieldChangeFuncHash === GeneralUtility::hmac($this->fieldChangeFunc);
    }

    /**
     * @return PageRenderer
     */
    protected function getPageRenderer()
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }

}

?>