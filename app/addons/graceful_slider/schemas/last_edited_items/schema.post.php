<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

$schema['slides.update'] = array(
    'func' => array('fn_get_slide_name', '@slide_id'),
    'text' => 'slides'
);

return $schema;
