<?php 

	namespace Atiksoftware\Amiral;

	class View 
	{


		static function Display($data, $exit = false){
			if(!Kernel::isCLI()){
				if(is_array($data)){
					header('Content-Type: application/json');
				}  
				if(is_string($data) && $data[0] == '{' ){
					header('Content-Type: application/json');
				} 
			} 
			echo json_encode($data,JSON_PRETTY_PRINT);
			if($exit){
				exit(0);
			}
		}

	}