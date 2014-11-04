<?php
class fSQL{
	private $data = array();
	var $con;
	var $col = array();
	public function __set($name,$value){
		
		$this->data[$name] = $value;		

	}

	public function __get($name)
    	{
        	if (array_key_exists($name, $this->data)) {
	            return $this->data[$name];
        	}
	}

	public function __construct($host,$user,$pass,$db,$type = 'mysql'){
	
		$this->con = new PDO("$type:host=$host;dbname=$db",$user,$pass);

	}


	public function from($table){
		return new fSQL_Table($table,$this->con);
	}


}

class fSQL_Table extends fSQL{

	var $table;
	var $SQL;
	var $SQL_R;
	var $CON;
	var $CON_TABLE;
	var $cols = array();
	var $create_new_row = false;
	var $VAR_WHERE = '';
	var $rowCount = 0;
	var $lastId = null;

	
	
	
	public function __construct($table,$resource){
		$this->table = $table;
		$this->CON = $resource;
		$consulta = $resource->prepare("desc $table");
		$consulta->execute();

		while($retorno = $consulta->fetch(PDO::FETCH_NUM)){
			array_push($this->cols,$retorno[0]);
			$this->$retorno[0] = null;
			$name = "f_".$retorno[0];
			$this->$name = null; 
			
		}


		$this->SQL = "SELECT `".implode("`,`",$this->cols)."` FROM $table ";
		$this->exec();

	}


	public function where($where){

		$this->SQL= " SELECT `".implode("`,`",$this->cols)."` FROM $this->table WHERE $where";
		$this->VER_WHERE = $where;
		$this->exec();

	}

	private function exec(){
		$this->CON_TABLE = $this->CON->prepare($this->SQL);
		$this->CON_TABLE->execute();
		$this->rowCount = $this->CON_TABLE->rowCount();
		
	}


	public function run(){
		$fetch  = $this->CON_TABLE->fetch(PDO::FETCH_ASSOC);
		if($fetch!=false){
			foreach($this->cols as $collum){
				$this->$collum = $fetch[$collum];
				$j = "f_".$collum;
				$this->$j = $fetch[$collum];
			}
		}
		return $fetch;
	}



	public function SQL($sql){
		return $this->CON->query($sql);
	}


	public function newRow(){
		$this->create_new_row = true;
		foreach($this->cols as $name){
                       $f = "f_".$name;
                       $this->$f = null;                
                       
		}

	}

	public function save(){
	
		if($this->create_new_row==false){
			
			$cl = array();
			$wh = array();
			foreach($this->cols as $name){
				$f = "f_".$name;
				if($this->$name!=$this->$f){
					array_push($cl,"`$name`='".$this->$name."'");
				}
				array_push($wh,"`$name`='".$this->$f."'");
			}
			
			

			$this->SQL_R = "UPDATE $this->table SET ".implode(",",$cl)." WHERE ".implode(" AND ",$wh);
		

			
		

		}else{
			$cl = array();
	                foreach($this->cols as $name){
        	                array_push($cl,$this->$name);
                	}

	                $this->SQL_R = "INSERT INTO $this->table (`".implode("`,`",$this->cols)."`) VALUES('".implode("','",$cl)."')";

		}
		$sql_temp = $this->SQL_R;
//		$this->SQL_R = null;
		$j = $this->CON->query($sql_temp);
		if($this->create_new_row==true){
			$this->create_new_row = false;
			$this->lastId = $this->CON->lastInsertId();
		}
		
                return $j;
		

	}


	public function deleteRow(){
		 $cl = array();	
		 foreach($this->cols as $name){
                                $f = "f_".$name;
                                array_push($cl,"`$name`='".$this->$name."'");
                               
                               
                 }

		$sql = "DELETE FROM $this->table where ".implode(" AND ",$cl);
                
		return $this->CON->query($sql);

		

	}


}

/*
$fSQL = new fSQL("localhost","root","admin","intra");

$table = $fSQL->from("lis_ref_emp_contatos");

$table->where("id_ref_empcontatos>0");

while($table->run()){
	echo $table->id_contatos." | ".$table->id_empresa."\n";
}

$table->newRow();

$table->id_contatos = rand(0,100);
$table->id_empresa = rand(0,rand(1,30));
$table->id_ref_empcontatos = null;

if($table->save())
	echo "OK";
else
	echo "Erro".$table->SQL_R;
*/
?>
