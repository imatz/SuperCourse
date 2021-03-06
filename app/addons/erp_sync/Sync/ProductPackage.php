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

class ProductPackage extends Master
{
  protected $table='';
	protected $file='~package~';  
  
  
  public function clear()  {    
    if (!parent::clear()) return;  
	// diagrafoyme tis quick synueseis
    $data = $this->get_shop_clear_data();
    foreach ($data as $dt)
      fn_delete_product($dt);
    // update to product_id se null stis standard gia na mh xauoyn ta proionta
	db_query('UPDATE ?:package_products SET product_id = 0');
  }  
	
  protected function get_shop_clear_data()
	{
    return db_get_fields("SELECT package_id 
				FROM ?:package_data
				WHERE creation='O'");
	}
	
}
