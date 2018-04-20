<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

$schema['slides'] = array(
        'controller' => 'slides',
        'mode' => 'update',
        'type' => 'tpl_tabs',
        'params' => array(
            'object_id' => '@slide_id',
            'object' => 'slides'
        ),
        'table' => array(
            'name' => 'slides',
            'key_field' => 'slide_id',
        ),
        'request_object' => 'slide_data',
        'have_owner' => true,
);

return $schema;
