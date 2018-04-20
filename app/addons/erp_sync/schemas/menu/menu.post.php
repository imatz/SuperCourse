<?php
//
// Author Ioannis Matziaris [imatz] - imatzgr@gmail.com - February 2013
//

$schema['top']['addons']['items']['erp_sync_activity'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'erp_sync.activity',
    'position' => 900
);

$schema['top']['addons']['items']['erp_sync_clear'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'erp_sync.clear',
    'position' => 901
);

return $schema;