<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

namespace Sync;
use Tygh\Registry;


class Db
{
    private static $sync_initiated = false ;
	private static $current_db='';
	
	
	public static function use_shop()
	{	
		if (Registry::get('config.db_name')==self::$current_db) return true;
		if (db_connect_to(array(), Registry::get('config.db_name'))) {
            return true;
        } else {
            throw new \Exception('Unable to establish store db connection');
        }
	}
	
	public static function use_bridge()
	{	
		if ('bridge'==self::$current_db) return true;
		$params = array(
			'dbc_name' => 'bridge',
			'table_prefix' => ''
		);
		if (!self::$sync_initiated) {
			if (db_initiate('localhost', 'root', '', 'supercourse_bridge', $params))
				self::$sync_initiated=true;
			else 
				throw new \Exception('Unable to connect to bridge db');
		} 
		
		if (db_connect_to($params, 'supercourse_bridge')) {
            return true;
        } else {
            throw new \Exception('Unable to establish bridge db connection');
        }
		
	}
	
}
