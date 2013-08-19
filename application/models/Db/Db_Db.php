<?php

class Db_Db 
{ 
   public static function conn(){ 
      $db = Zend_Db::factory('Pdo_Mysql', array(
		'host'     => 'erpdevel.db.4288105.hostedresource.com',
		'username' => 'erpdevel',
		'password' => 'et7Hsy45G',
		'dbname'=>"erpdevel",
		'port' => 3306 )
	);
      return $db; 
   } 
}