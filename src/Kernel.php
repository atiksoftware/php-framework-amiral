<?php 

	namespace Atiksoftware\Amiral;
	use \Atiksoftware\Amiral\Tools;
	class Kernel
	{

		static function Run(){ 
			Security::AllowOrigin();
			self::loadControllers();
			self::loadEngine();
			
		}

		static function loadControllers(){
			global $class;
			$class = new Controller(); 
		}


		static function getEngineFile(){
			$_roads = Router::Roads();  
			while (count($_roads)) {
				# .../haberler/kategori/ekonomi.php
				$will_include_file = DIR_ENGINE."/".implode("/",$_roads).".php";
				if( file_exists($will_include_file) ){ return $will_include_file; break; }
				# .../haberler/kategori/index.php
				$will_include_file = DIR_ENGINE."/".implode("/",$_roads)."/index.php";
				if( file_exists($will_include_file) ){ return $will_include_file; break; }
				# .../haberler_kategori_ekonomi.php
				$will_include_file = DIR_ENGINE."/".implode("_",$_roads).".php";
				if( file_exists($will_include_file) ){ return $will_include_file; break; }
				# .../haberler.kategori.ekonomi.php
				$will_include_file = DIR_ENGINE."/".implode(".",$_roads).".php";
				if( file_exists($will_include_file) ){ return $will_include_file; break; }
				# Dizin parçaları doğru dosya bulunana kadar birer birer geri gelecek.
				array_pop($_roads);
			}
			return false;
		}
 		static function loadEngine(){
			global $class;
			global $data;
			global $return;

			$data = false;
			if (!self::isCLI() && self::isPOST()) {
				$data = Security::cleanXSS($_POST);
			}
			

			$return = [];

			$engineFile = self::getEngineFile();
			if($engineFile){
				if(file_exists(DIR_ENGINE."/.header.php")){
					require_once DIR_ENGINE."/.header.php";
				} 
				require_once $engineFile; 
				if(file_exists(DIR_ENGINE."/.footer.php")){
					require_once DIR_ENGINE."/.footer.php";
				} 
				else{
					View::Display($return);
				}
			}
			else{
				View::Display([
					"success"    => false,
					"reason"     => "no_function",
					"descripton" => "Bu isteği karşılacak bir fonksiyon bulunamadı",
					"roads"      => Router::Roads()
				],true);
				return false;
			}
		}







		static function isCLI(){
			return php_sapi_name() == "cli";
		}

		static function isPOST(){
			return $_SERVER['REQUEST_METHOD'] == 'POST';
		}
		static function isGET(){
			return $_SERVER['REQUEST_METHOD'] == 'POST';
		}

	}