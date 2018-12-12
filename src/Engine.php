<?php 

	namespace Atiksoftware\Amiral;
	use \Atiksoftware\Schema\Merger;
	class Engine
	{

		public $db;
		public $_last_index = false;
		public $_prefix = "";

		function __construct(){
			global $class;
			$this->db = Database::getDB();
			$this->__constructed();
		}

		function __constructed(){

		}

		/************************/
		public function _default(){
			return null;
		}
		public function _schema(){
			return null;
		}
		public function _aggregate(){
			return null;
		}

		/************************/

		function getTableName(){
			return strtolower(get_class($this));
		}

		/************************/

		public function event_insert_before($data,$isBulk = false){return null;}
		public function event_insert_after($data,$isBulk = false){return null;}
		public function event_update_before($when, $data, $overwrite = false, $multiple = true, $isBulk = false, $fixData = false){return null;}
		public function event_update_after($when, $data, $overwrite = false, $multiple = true, $isBulk = false, $fixData = false){return null;}
		public function event_remove_before($when){return null;}
		public function event_remove_after($when){return null;}
		public function event_info_before($when,$fixData){return null;}
		public function event_info_after($when,$fixData){return null;}
		public function event_select_before($when, $order, $project, $limit, $skip){return null;}
		public function event_select_after($when, $order, $project, $limit, $skip){return null;}
		
		/************************/



		function Migrate($data){
			return array_replace($this->_default(),$data);
		}
		function MakeSchema($data = []){
			$schema = $this->_schema();
			if(!$schema){
				return $data;
			}
			$schemaMerger = new Merger();
			$schemaMerger->setSchema($schema);
			return $schemaMerger->Migrate($data);
		}

		function Insert($data,$isBulk = false){
			$this->event_insert_before($data,$isBulk);
			$this->db->setCollection($this->getTableName());
			if($isBulk){
				foreach($data as $k => $v){
					if(!isset($v["_id"])){
						$data[$k]["_id"] = $this->newId();
					}
				}
			}else{
				if(!isset($data["_id"])){
					$data["_id"] = $this->newId();
				}
			}
			$this->db->insert($data,$isBulk);
			$this->event_insert_after($data,$isBulk);
			return $data["_id"];
		}

		function Update($when, $data, $overwrite = false, $multiple = true, $isBulk = false, $fixData = false){
			$this->event_update_before($when, $data, $overwrite, $multiple, $isBulk, $fixData);
			$this->db->setCollection($this->getTableName());
			if($fixData){
				if($isBulk){
					foreach($data as $k => $v){
						$data[$k] = $this->MakeSchema($v);
					}
				}else{
					$data = $this->MakeSchema($data);
				}
			}
			$this->db->when($when)->Update($data, $overwrite, $multiple, $isBulk);
			$this->event_update_after($when, $data, $overwrite, $multiple, $isBulk, $fixData);
		}



		function Select($when = [],$order = [], $project = [], $limit = 0, $skip = 0 ){
			$this->event_select_before($when, $order, $project, $limit, $skip);
			$this->db->setCollection($this->getTableName());
			$sorgu = $this->db
				->when($when)
				->orderBy($order)
				->projectBy($project)
				->limit($limit)
				->skip($skip)
				->select();
			$list = [];
			if(count($sorgu)){
				foreach($sorgu as $row){
					$list[$row["_id"]] = $row;
				}
			}
			$this->event_select_after($when, $order, $project, $limit, $skip);
			return $list;
		}

		function Info($when = [],$fixData = true ){
			$this->event_info_before($when,$fixData);
			$this->db->setCollection($this->getTableName());
			$_aggregate = $this->_aggregate();
			if($_aggregate){
				$pipeline  = [];
				foreach($_aggregate as $ev){
					$pipeline[] = [
						'$lookup' => [
							'from'         => $ev["from"] ,
							'localField'   => $ev["with"] ?? "_id",
							'foreignField' => $ev["to"] ?? "_id",
							'as'           =>  $ev["as"] ,
						]
					];
					if(isset($ev["single"]) && $ev["single"]){
						$pipeline[] = [ '$unwind' =>  [ 'path' => '$'.$ev["as"], 'preserveNullAndEmptyArrays' => true ] ];
					}
				}
				// echo "<pre> ";
				// print_r($pipeline);
				// exit;
				$sorgu = $this->db->command([
					'aggregate' => $this->getTableName(),
					'pipeline' => $pipeline
				]);
			}else{
				$sorgu = $this->db->when( $when )->limit( 1 )->select();
			}

			if(count($sorgu)){
				$row = array_shift($sorgu);
				$this->event_info_after($when,$fixData);
				return $this->MakeSchema($row);
			}else{
				$this->event_info_after($when,$fixData);
				return false;
			}
		}

		function Remove($when = []){
			$this->event_remove_before($when);
			$this->db->setCollection($this->getTableName());
			$this->db->when( $when )->remove();
			$this->event_remove_after($when);
		}

		function Count($when = []){
			$this->db->setCollection($this->getTableName());
			return $this->db->when($when)->Count();
		}

		function Fields($key = "", $value = "",$when = []){
			$this->db->setCollection($this->getTableName());
			$list = [];
			$sorgu = $this->Select($when);
			$_value = $value;
			foreach($sorgu as $row){
				$value = $_value;
				preg_match_all("/{{(.*?)}}/", $value, $output);
				if(count($output)){
					for($i = 0; $i < count($output[0]);$i++){
						$value = str_replace( $output[0][$i] ,  \Swain\Arr\To::Deepget($row,$output[1][$i])  ,$value);
					}
				}
				$list[$row[$key]] = $value;
			}
			return $list;
		}

		function newId(){
			$this->db->setCollection($this->getTableName());
			$li = $this->_last_index;
			if(!$li){
				$list = $this->Select([],["_id" => -1], $project = ["_id"], 1);
				if(count($list)){
					$l = array_pop($list);
					$li = $l["_id"];
				}else{
					return $this->_prefix.dechex(100000000);
				}
			}
			$li = substr($li, strlen($this->_prefix) );
			$i = hexdec($li);
			$ni = $this->_prefix.dechex($i + 1);
			$this->_last_index = $ni;
			return $ni;
		}


	}