<?php

# $this->backPath = '';
# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this);

// Remove if possible (2 lines):
# $this->addCssFile('sysext/core/Resources/Public/JavaScript/Contrib/extjs/resources/css/ext-all-notheme.css');
# $this->addCssFile('sysext/t3skin/extjs/xtheme-t3skin.css');
// end (Remove if possible)

// Remove if possible (6 lines):
# $this->addJsFile('sysext/core/Resources/Public/JavaScript/Contrib/require.js');
# $this->addJsFile('sysext/core/Resources/Public/JavaScript/Contrib/jquery/jquery-2.1.4.js');
# $this->addJsFile('sysext/core/Resources/Public/JavaScript/Contrib/extjs/adapter/ext-base-debug.js');
# $this->addJsFile('sysext/core/Resources/Public/JavaScript/Contrib/extjs/ext-all-debug.js');
# $this->addJsFile('sysext/core/Resources/Public/JavaScript/Contrib/extjs/locale/ext-lang-en.js');
# $this->addJsFile('sysext/lang/Resources/Public/JavaScript/Typo3Lang.js');
// end (Remove if possible)

$jQuery = 'TYPO3.jQuery';

// @TODO: change this solution?
# $this->addJsExtensionFile("Resources/Public/js/jquery-1.4.min.js");
$this->addJsExtensionFile('Resources/Public/js/'.($jQuery ? $jQuery.'.' : '').'jquery-ui-1.7.2.custom.min.js');
$this->addJsExtensionFile('Resources/Public/js/'.($jQuery ? $jQuery.'.' : '').'jquery.base64.js');
$this->addJsExtensionFile('Resources/Public/js/'.($jQuery ? $jQuery.'.' : '').'wizard.all.js.ycomp.js');

$existingFields = $this->data->listAreas(
	"\tcanvasObject.addArea(new area##shape##Class(),'##coords##','##alt##','##link##','##color##',0);\n"
);

$javaScriptCode = '
	// jQuery.noConflict();
	function imagemapwizard_valueChanged(field) {
		'.$jQuery.'.ajaxSetup({
			url: "'.$this->getAjaxURL('wizard.php').'",
			global: false,
			type: "POST",
			success: function(data, textStatus) {
				if(textStatus==\'success\') {
					jQuery("#'.$this->getId().'").html(data);
				}
			},
			data: { context:"tceform",
					ajax: "1",
					formField:field.name,
					value:field.value,
					table:"'.$this->data->getTablename().'",
					field:"'.$this->data->getFieldname().'",
					uid:"'.$this->data->getUid().'",
					config:"'.addslashes(serialize($this->data->getFieldConf())).'"
			}
		});
		'.$jQuery.'.ajax();
	}
	jQuery(document).ready(function(){
		canvasObject = new previewCanvasClass();
		canvasObject.init("'.$this->getId().'-canvas","'.$this->data->getThumbnailScale('previewImageMaxWH',200).'");
		'.$existingFields.'
		jQuery(".imagemap_wiz_message").css({top: (canvasObject.getMaxH()/2-35)+"px", left: "20px"}).animate({left: "60px",opacity: "show"}, 750).animate({left: "60px"}, 6000).animate({left: "20px", opacity: "hide"}, 750);
		jQuery(".imagemap_wiz_message_close").click(function() {
			jQuery(".imagemap_wiz_message").animate({left: "20px", opacity: "hide"}, {duration:250, queue:false});
		});
	});
';
$javaScriptCode = str_replace('jQuery', $jQuery, $javaScriptCode);

/*
$wizardSetup = array(
	// wizConf is set in TCA
	'wizConf' => array(
		'[WIZARD_IDENTIFIER]' => array(
			'title' => '...',
			'icon' => '...',
			'module' => array(
				'name' => '...',
				'urlParameters' => array(),
			),
			'script' => '...',
			'type' => '...', // ['userFunc', 'script', 'colorbox', 'popup', 'slider', 'select', 'suggest']
			'enableByTypeConfig' => true, // [true | false]
			'RTEonly' => false, // [true | false]
			'notNewRecords' => true, // [true | false]
			'userFunc' => '',
			'params' => array(),
			'exampleImg' => '',
			'popup_onlyOpenIfSelected' => true, // [true | false]
			'JSopenParams' => '...',
			'mode' => ['append' | 'prepend']
		),
		'_POSITION' => '...', // ['left' | 'top' | 'bottom' | 'right' or 'aside']
	),
	'additionalWizardConf' => array(
		'fieldChangeFunc' => array('imagemapwizard_valueChanged(field);'), // $PA['fieldChangeFunc']
		'itemFormElName' => ...  // for flexform: 'data[' . $table . '][' . $row['uid'] . '][' . $field . ']'.'...'
		'fieldConf' => array(
			'config' => array(
				'type' => '...',
				'maxitems' => '...',
				'renderType' => '...',
			),
		),
	),


	// @see: https://docs.typo3.org/typo3cms/TCAReference/7.6/AdditionalFeatures/SpecialConfigurationOptions/Index.html
	'specConf' => array(
		'wizards' => array(
			'parameters' => '' // 'defaultExtras' => 'richtext[]:rte_transform[mode=tx_examples_transformation-ts_css]'
		),
	),
);
*/

$additionalWizardConf = array('fieldChangeFunc'=>array('imagemapwizard_valueChanged(field);'));
//TODO: make it by pageRenderer
echo $this->getJsExtensionIncludes();

?>
<div id="<?php echo $this->getId(); ?>" style="position:relative">

    <?php
        ob_start();
    ?>
    <div class="imagemap_wiz" style="padding:5px;overflow:hidden;position:relative">
        <div id="<?php echo $this->getId(); ?>-canvas" style="position:relative;top:5px;left:5px;overflow:hidden;">
        <?php
            echo $this->data->renderThumbnail('previewImageMaxWH',200);
        ?>
        </div>
    </div>
    <?php
        $imagepreview = ob_get_contents();
        ob_end_clean();
		# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($imagepreview);
		if($this->form){
			# \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array('file'=>__FILE__,'line'=>__LINE__,'$this->form'=>$this->form));
			if($this->form instanceof TYPO3\CMS\Core\Http\Response){
				\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(debug_backtrace());
			}
			// $this->form is instance of class \TYPO3\CMS\Backend\Form\Element\UserElement
			// function renderWizards() is defined in \TYPO3\CMS\Backend\Form\Element\AbstractFormElement
			echo $this->form->renderWizards(
				array($imagepreview,''),        // @param array  $itemKinds  Array with the real item in the first value
				$this->wizardConf,              // @param array  $wizConf    The "wizards" key from the config array for the field (from TCA)
				$this->data->getTablename(),    // @param string $table      Table name
				$this->data->getRow(),          // @param array  $row        The record array
				$this->data->getFieldname(),    // @param string $field      The field name
				$additionalWizardConf,          // @param array  $PA         Additional configuration array.
				$this->formName,                // @param string $itemName   The field name
				array(),                        // @param array  $specConf   Special configuration if available.
				0                               // @param bool   $RTE        Whether the RTE could have been loaded.
			);
		}
		else {
			echo 'No form found; Obviously something is wrong :-(';
			#\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this);
			#\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(parent::form);
			#\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(debug_backtrace());
			/*
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(array(
				'id' => $this->getId(),
				'LL:form.is_dirty' => $this->getLL('form.is_dirty',1),
				'data' => array(
					'existingFields' => $this->data->listAreas("\tcanvasObject.addArea(new area##shape##Class(),'##coords##','##alt##','##link##','##color##',0);\n"),
				)
			));
			*/
		}
    ?>

    <?php
        if($this->data->hasDirtyState()) {
            echo '<div class="imagemap_wiz_message" style="display:none;width:170px;height:70px;padding:20px 40px 10px 40px;position:absolute;z-index:999;background: url('.$this->getTplSubpath().'img/form-tooltip.png) no-repeat;">';
            $this->getLL('form.is_dirty',1);
            echo '<div class="imagemap_wiz_message_close" style="display:block;position:absolute;right:15px;top:15px;cursor:pointer">[x]</div></div>';
        }
    ?>
    <script type="application/javascript">
	<?php echo $javaScriptCode; ?> 
    </script>
    <input type="hidden" name="<?php echo $this->formName; ?>" value="<?php echo htmlspecialchars($this->data->getCurrentData()); ?>" />
</div>
