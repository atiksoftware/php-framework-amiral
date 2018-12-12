<?php 

	namespace Atiksoftware\Amiral;

	class Controller
	{
		private $handle = [];

		public function __get($e)
		{ 
			# users
			if (!array_key_exists($e, $this->handle)) {
				$className = $this->toUpfirst($e);
				$classFile = DIR_CONTROLLER."/{$className}.php";
				if(\file_exists($classFile)){
					require_once $classFile; 
					$this->handle[$e] = new $className();
				}
				
			} 
			return array_key_exists($e, $this->handle) ? $this->handle[$e] : null; 
		} 


		function toUpfirst($e){
			$list = [];
			foreach(array_filter(explode("_",$e)) as $c){
				$list[] = ucfirst(strtolower($c));
			}
			return implode("_",$list);
		}


	}