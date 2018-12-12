<?php 

	namespace Atiksoftware\Amiral;

	class Auth
	{

		static function Protect($authKey = "" , $protectForOnlyWrite = false){
			if(Kernel::isCLI()){
				return true;
			}

			if ($protectForOnlyWrite && !Kernel::isPOST()) {
				return true;
			}

			global $class;
			if($class->users->user){
				if(isset($class->users->user["auths"])){
					$auths = $class->users->user["auths"];
					if(in_array($authKey,$auths)){
						return true;
					}
					if(array_key_exists($authKey,$auths)){
						if(Kernel::isGET()){
							return true;
						}
						if(Kernel::isPOST() && $auths[$authKey]["write"]){
							return true;
						} 
					}
				}
			}
			View::Display([
				"success" => false,
				"reason" => "no_auth",
				"descripton" => "Bu işlemi yapmaya izniniz yok",
				"authName" => $authKey,
				"protectForOnlyWrite" => $protectForOnlyWrite,
			],true);
		}


	}