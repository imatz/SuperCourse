<?php
/***************************************************************************
*                          Farlaf 2014                                    *
****************************************************************************/

/* Categories templates */
$schema ['addons/banners/blocks/carousel.tpl'] = array (
	'settings' => array (
		'skin' => array (
			'type' => 'selectbox',
			'values' => array (
				'D' => 'default',
				'fashionSkin' => 'fashionSkin',
				'iSkin' => 'iSkin',
				'airSkin' => 'airSkin',
				'gracefulSkin' => 'gracefulSkin',
				'lightingSkin' => 'lightingSkin',
			),
			'default_value' => 'D'
		),
		'transitionStyle' => array (
			'type' => 'selectbox',
			'values' => array (
				'N' => 'none',
				'fade' => 'fade',
				'backslide' => 'backSlide',
				'godown' => 'goDown',
				'fadeup' => 'scaleUp',
				
				
				'fallDown' => 'fallDown',
				'fallRotate' => 'fallRotate',
				'scaleRotate' => 'scaleRotate',
				'3dflip' => '3dflip',
				'scaleIn' => 'scaleIn',
				'flipCenter' => 'flipCenter',
				
				
			),
			'default_value' => 'fadeup'
		),
		'arrows' => array(
			'type' => 'checkbox',
			'default_value' => 'Y'
		),
		'pager' => array (
			'type' => 'selectbox',
			'values' => array (
				'N' => 'none',
				'D' => 'dots',
				'P' => 'pages',
			),
			'default_value' => 'D'
		),
		'delay' => array (
			'type' => 'input',
			'default_value' => '3'
		),
		'progress_bar' => array (
			'type' => 'selectbox',
			'values' => array (
				'N' => 'none',
				'T' => 'top',
				'B' => 'bottom',
			),
			'default_value' => 'T'
		),
		'stop_on_hover' => array(
			'type' => 'checkbox',
			'default_value' => 'Y'
		),
	),
);

return $schema;
