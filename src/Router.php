<?php 

	namespace Atiksoftware\Amiral;

	class Router
	{

		static function Road($i = 0){
			return @(self::Roads())[$i] ?: false;
		}

		static function Roads(){ 
			$paths = [];
			if (Kernel::isCLI()) {
				global $argv;
				$paths = $argv;
				array_shift($paths);
			}
			else {
				$paths = explode( "/", isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "" );
			} 
			$_roads = array_filter($paths, function($v) {
				return trim($v) != '' && $v != "..";
			}, ARRAY_FILTER_USE_BOTH); 
			return $_roads ?: ["index"];
		}

		static function URLBasePath(){
			return str_replace($_SERVER["PATH_INFO"],'',$_SERVER["REQUEST_URI"]); 
		}


		static function Link($link){
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
			$base = rtrim(ltrim( self::URLBasePath(),"/"),"/");
			return $actual_link."/".$base."/".ltrim($link,"/");
		}


	}