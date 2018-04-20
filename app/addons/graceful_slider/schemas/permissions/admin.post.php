<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

$schema['slides'] = array (
    'permissions' => 'manage_slides',
);
$schema['tools']['modes']['update_status']['param_permissions']['table']['slides'] = 'manage_slides';

return $schema;
