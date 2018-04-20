<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

$schema['central']['marketing']['items']['slides'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'slides.manage',
    'position' => 500
);

if (!empty($schema['top']['addons']['items']['statistics'])) {
    $schema['top']['addons']['items']['statistics']['subitems']['slides'] = array(
        'href' => 'statistics.slides',
        'position' => 900
    );
}

return $schema;
