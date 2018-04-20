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

use Tygh\Mailer;

abstract class Master
{
	protected $table;
	protected $file;
  	protected $shop_id;
	protected $csv_data=array();
	protected $delimiter=';';
  	protected $dependents=array();
	
  	private static $import_modules=array (
		'Sync\\Parameter',
		'Sync\\Tax',
		'Sync\\Category',
		'Sync\\Product',
		'Sync\\State',
		'Sync\\User',
		'Sync\\Profile',
		'Sync\\Phone'
	) ;
	
  	private static $export_modules=array (
		'Sync\\Order',
		'Sync\\OrderDetail'
	) ;
	
	
	public static function full_sync()
	{ 
		set_time_limit(0);
		try {
			
			$module=self::get_real_class_name(__CLASS__);
			
			if (!self::is_in_progress($module)) {
			
				self::set_activity_status($module);
				self::export_sync();
				self::import_sync();
				self::clear_activity_status($module);
				
			} else throw new \Exception("******************* ALLREADY IN SYNC *******************");
		} catch (\Exception $e) {
			echo $e;
			//self::clear_activity_status($module);
			$to=fn_erp_sync_get_setting_field('error_email');
			
			Mailer::sendMail(array(
				'to' => $to,
				'from' => 'company_site_administrator',
				'data' => array(),
				'subj' => 'Supercourse Sync Error',
				'body' => $e,
				'company_id' => 1
			), 'A', 'el');
		}
	}
	
	public static function import_sync()
	{
		$modules=array();
		foreach (self::$import_modules as $im) {
			
			$m=new $im();
			$module=$m->get_class_name();
     
			self::set_activity_status($module);
			$m->sync_bridge();
			$m->sync_shop();
			self::clear_activity_status($module);
		}
	}
	
	public static function export_sync()
	{
		$modules=array();
		foreach (self::$export_modules as $ex) {
			
			$m=new $ex();
			$module=$m->get_class_name();
			
			self::set_activity_status($module);
			$m->sync_bridge();
			$m->export();
			self::clear_activity_status($module);
	
		}
    return true;
	}
	
	protected function sync_bridge(){}
	
	protected function sync_shop(){}
	
	protected function export()
	{	
		$this->csv_data=$this->get_erp_unsynced_data();
		$this->save_csv();
	}
	
	protected function get_class_name()
	{
		return Master::get_real_class_name(get_class($this));	
	}
	
	public static function get_real_class_name($classname) 
	{
		if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
		return $classname;
	}
	
	/*
	* DB functions
	*/
	
	public static function is_in_progress($module)
	{
		try {
			Db::use_bridge();
			$status=db_get_row("SELECT * FROM modules WHERE module=?s",$module);
			
			if (empty($status['status'])) throw new \Exception("Unable to get Module status [$module]");
			
			if ('A'==$status['status']) {
				$now = time();
				$last_activity = (int) $status['last_activity_timestamp'];
				if ($now - $last_activity > 1800) { // ekane panv apo 30 mins -> reset 
					self::reset_activity_status();
					return false;
				} else {
					return true;
				}
			} else {			
				$ret = false;
			}
			
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		if (!isset($ret)) throw new \Exception("Unable to get Module status [$module]");
		return $ret;
	}
	
	public static function get_activity_status()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT * FROM modules");
			
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	public static function reset_activity_status()
	{
		try {
			Db::use_bridge();
			db_query("UPDATE modules SET status='I', begin_timestamp=0, end_timestamp=NULL, last_activity_timestamp=NULL");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	private static function set_activity_status($module)
	{
		try {
			Db::use_bridge();
			db_query("UPDATE modules SET status='A', begin_timestamp=?i, end_timestamp=NULL, last_activity_timestamp=NULL WHERE module=?s", time(),$module);
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	private static function clear_activity_status($module)
	{
		try {
			Db::use_bridge();
			db_query("UPDATE modules SET status='I', end_timestamp=?i WHERE module=?s", time(), $module);
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	private static function update_activity_status($module)
	{
		try {
			Db::use_bridge();
			$time=time();
			db_query("UPDATE modules SET last_activity_timestamp=?i WHERE module=?s", $time, $module);
			db_query("UPDATE modules SET last_activity_timestamp=?i WHERE module='Master'", $time);
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	protected function reset_erp_updated()
	{
		try {
			Db::use_bridge();
			db_query("UPDATE {$this->table} SET erp_updated=0 WHERE erp_updated<>-1 AND shop_updated<>-1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	protected function get_shop_unsynced_data()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT * FROM {$this->table} WHERE IFNULL(shop_updated,0)=0 AND erp_updated=1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
  
  	protected function get_shop_clear_data()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_fields("SELECT {$this->shop_id} FROM {$this->table} WHERE {$this->shop_id} IS NOT NULL");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	protected function get_erp_unsynced_data()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT * FROM {$this->table} WHERE IFNULL(erp_updated,0)=0 AND shop_updated=1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	protected function get_bridge_row($where_data)
	{
		try {
			Db::use_bridge();
			$row= db_get_row("SELECT * FROM {$this->table} WHERE ?w",$where_data);
			Db::use_shop();
			return $row;			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	protected function clear_bridge()
	{
		try {
			Db::use_bridge();
			db_query("DELETE FROM {$this->table}");
			Db::use_shop();			
			$this->update_activity_status($this->get_class_name());
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
  
	protected function update_bridge($where_data,$data=array())
	{
		try {
			Db::use_bridge();
			db_query("UPDATE {$this->table} SET ?u WHERE ?w",$data,$where_data);
			Db::use_shop();			
			$this->update_activity_status($this->get_class_name());
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	protected function update_shop($where_data,$data=array())
	{
		$data['shop_updated']=1;
		$data['shop_timestamp']=time();
		$data['erp_updated']=0;
		$this->update_bridge($where_data,$data);
	}
	
	protected function update_csv($where_data,$data=array())
	{
		$data['shop_updated']=0;
		$data['erp_updated']=1;
		$data['erp_timestamp']=time();
		$this->update_bridge($where_data,$data);	
	}
	
	protected function insert_bridge($data)
	{
		try {
			Db::use_bridge();
			db_query("INSERT INTO {$this->table} ?e",$data);
			Db::use_shop();		
			$this->update_activity_status($this->get_class_name());			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	protected function insert_shop($data)
	{
		$data['shop_updated']=1;
		$data['shop_timestamp']=time();
		$data['erp_updated']=0;
		$this->insert_bridge($data);		
	}
	
	protected function insert_csv($data)
	{
		$data['shop_updated']=0;
		$data['erp_updated']=1;
		$data['erp_timestamp']=time();
		$this->insert_bridge($data);		
	}
	
	protected function delete_bridge($where_data)
	{
		$data=array();
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['erp_updated']=-1;
		$data['erp_timestamp']=time();
		$this->update_bridge($where_data,$data);	
	}

	/*
  * Clear process
	*/
  
  // edv mpainoyn ayta poy telika diagrafontai vste na emfanistoyn anapoda
  private static $ins_queue;

  public static function ins_enqueue($module) 
  {
    self::$ins_queue[]=$module;
  }

  public static function ins_queue_find($module) 
  {
    if (in_array($module, self::$ins_queue))
      return true;
    
    return false;
  }
  
  public static function clear_modules($modules = array()) 
  {
    self::$ins_queue = array();
    $master_module=self::get_real_class_name(__CLASS__);
   
    if (!self::is_in_progress($master_module)) {
    
      self::set_activity_status($master_module);
     
      foreach ($modules as $module) {
        $module = 'Sync\\'.$module;
        $mod = new $module();        
        $mod->clear();
      }
   
      self::clear_activity_status($master_module);
    } else self::ins_enqueue('Another operation has acquired the bridge mutex! Current operation aborted.');
    
    return self::$ins_queue;
  }

  protected function clear() 
  { 
    if (Master::ins_queue_find($this->file)) {
     // echo $this->table." allready cleared!\n";
      return false;
    }
    
    $dependents = $this->get_dependents();
    
    if (!empty($dependents)) {
      foreach ($dependents as $dep) {
        $dep='Sync\\'.$dep;
        $dmod = new $dep();
        $dmod->clear();
      }
    }
    
    Master::ins_enqueue($this->file);    
    return true;
  }

  protected function get_dependents()
  {
    return $this->dependents;
  }
  
	/*
	* CSV functions
	*/
	
	protected function load_csv()
	{ 
		$csv = new parseCSV();
		$csv->delimiter = $this->delimiter;
		$files=glob(BRIDGE_ROOT."*{$this->file}*.{csv,rd}",GLOB_BRACE);
		foreach($files as $file) {
			$csv->parse($file);
			if (!empty($csv->data) && is_array($csv->data)) $this->csv_data=array_merge($this->csv_data,$csv->data);
			rename($file,str_replace(array('.csv','.rd'),'.rd',$file));
		}	
	}
	
	protected function save_csv()
	{
		$csv = new parseCSV();
		$csv->delimiter = $this->delimiter;
		$counter = 0;
		do {
			$counter++;
			$cnt=str_pad($counter, 4, '0', STR_PAD_LEFT);
			$newname = date('Ymd') . '_' . $cnt . '_'. $this->file . '.csv';
			$newpath = BRIDGE_ROOT.$newname;
		} while (file_exists($newpath));
		if (!empty($this->csv_data)) {
			$header=array_keys($this->csv_data[0]);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			$csv->save($newpath, array(), false, $header);
			
			foreach ($this->csv_data as $cd) {
				unset($cd['shop_updated']);
				unset($cd['shop_timestamp']);
				unset($cd['erp_updated']);
				unset($cd['erp_timestamp']);
				$csv->save($newpath, array($cd), true, $header);
				$this->update_csv($cd,$cd);
			}
		}
	}
	
	protected function keep_last_csv()
	{
		$files=glob(BRIDGE_ROOT."*{$this->file}*.{csv,rd}",GLOB_BRACE);
		//bgale to teleytaio
		array_pop($files);
		foreach($files as $file) {
			rename($file,str_replace(array('.csv','.rd'),'.ign',$file));
		}
	}
	
	protected function mark_csv()
	{
		$files=glob(BRIDGE_ROOT."*{$this->file}*.rd");
		foreach($files as $file) {
			rename($file,str_replace('.rd','.ok',$file));
		}
	}
  
  	protected function clean_line_feeds($field)
  	{
    	$feeds = array("\r\n", "\n", "\r");
    	return str_replace($feeds, '', $field);
  	}
	
}
