<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

$schema['graceful_slider'] = array (
	'content' => array (
		'items' => array (
			'remove_indent' => true,
			'hide_label' => true,
			'type' => 'enum',
			'object' => 'slides',
			'items_function' => 'fn_get_slides',
			'fillings' => array (
				'manually' => array (
					'picker' => 'addons/graceful_slider/pickers/slides/picker.tpl',
					'picker_params' => array (
						'type' => 'links',
					),
					'params' => array (
						'sort_by' => 'position',
						'sort_order' => 'asc'
					)
				),
				'newest' => array (
					'params' => array (
						'sort_by' => 'timestamp',
						'sort_order' => 'desc',
						'request' => array (
							'cid' => '%CATEGORY_ID%'
						)
					)
				),
			),
		),
	),
	'templates' => array (
		'addons/graceful_slider/blocks/slider.tpl' => array(
			'settings' => array (
				'skin' => array (
					'type' => 'selectbox',
					'values' => array (
						'default_skin' => 'default',
						'fashionSkin' => 'fashionSkin',
						'iSkin' => 'iSkin',
						'airSkin' => 'airSkin',
						'gracefulSkin' => 'gracefulSkin',
						'lightingSkin' => 'lightingSkin',
					),
					'default_value' => 'default_skin'
				),
				'mode' => array (
					'type' => 'selectbox',
					'values' => array (
						'H' => 'horizontal',
						'V' => 'vertical',
						'F' => 'fade',
					),
					'default_value' => 'F'
				),
				'full_width' => array (
					'type' => 'checkbox',
					'default_value' => 'N'
				),
				'limit_central' => array (
					'type' => 'checkbox',
					'default_value' => 'N'
				),
				'limit_central_max_size' => array (
					'type' => 'input',
					'default_value' => '1195'
				),
				'controls' => array(
					'type' => 'checkbox',
					'default_value' => 'Y'
				),
				'pager' => array (
					'type' => 'selectbox',
					'values' => array (
						'N' => 'none',
						'F' => 'full',
						'S' => 'short',
					),
					'default_value' => 'F'
				),
				'auto' => array (
					'type' => 'checkbox',
					'default_value' => 'Y'
				),
				'auto_controls' => array (
					'type' => 'checkbox',
					'default_value' => 'N'
				),
				'delay' => array (
					'type' => 'input',
					'default_value' => '4'
				),
				'infinite_loop' => array(
					'type' => 'checkbox',
					'default_value' => 'Y'
				),
				'slider_bg' => array (
					'type' => 'input',
					'default_value' => '#F6F5F3'
				),
				'image_width_full' => array (
					'type' => 'checkbox',
					'default_value' => 'Y'
				),

			),
		)
	),
	'wrappers' => 'blocks/wrappers',
);

return $schema;
