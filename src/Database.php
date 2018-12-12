<?php 

	namespace Atiksoftware\Amiral;

	class Database
	{
		static public $db = false;

		static function getDB(){
			if(!self::$db){  
				self::$db = new \Atiksoftware\Database\MongoDB();
				self::$db->connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
				self::$db->setDatabase(DB_DATABASE);
			}
			return self::$db ;
		}


	}