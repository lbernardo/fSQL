<?php
class fSQL{
	private $data = array();
	var $con;
	var $col = array();
	
	// Seta nova variavel
	public function __set($name,$value){
		
		$this->data[$name] = $value;		

	}
	
	// Pega nova variavel
	public function __get($name)
    	{
        	if (array_key_exists($name, $this->data)) {
	            return $this->data[$name];
        	}
	}

	// Inicia conexão
	public function __construct($host,$user,$pass,$db,$type = 'mysql'){
	
		$this->con = new PDO("$type:host=$host;dbname=$db",$user,$pass);

	}


	// Seleciona uma tabela
	// fSQL_TABLE from(NOME_TABELA)
	public function from($table){
		return new fSQL_Table($table,$this->con);
	}


}

// Classe para controle da tabela
class fSQL_Table extends fSQL{

	// A tabela
	var $table;
	// SQL 
	var $SQL;
	// SQL RESOURCE
	var $SQL_R;
	// Consulta
	var $CON;
	// Consulta da tabela
	var $CON_TABLE;
	// Colunas
	var $cols = array();
	// Se deseja criar uma nova linha
	var $create_new_row = false;
	// WHERE
	var $VAR_WHERE = '';
	// Número de linhas
	var $rowCount = 0;
	// Ultimo id
	var $lastId = null;

	
	
	// Metodo contrutor da tabela 
		// (NOME_TABELA,RESOURCE de CONEXÃO PDO)
	public function __construct($table,$resource){
		// Marca tabela atual
		$this->table = $table;
		// Salva conexão
		$this->CON = $resource;
		// Realiza consulta das colunas da tabela
		$consulta = $resource->prepare("desc $table");
		// GEra
		$consulta->execute();
		// Verifica o nome das colunas executando um while
		while($retorno = $consulta->fetch(PDO::FETCH_NUM)){
			// Insere as colunas no metodo de coluna
			array_push($this->cols,$retorno[0]);
			// Cria colunas como objeto
			$this->$retorno[0] = null;
			// Cria nomes temporarios
			$name = "f_".$retorno[0];
			// Cria colunas com nome temporario
			$this->$name = null; 
			
		}

		// Gera SQL
		$this->SQL = "SELECT `".implode("`,`",$this->cols)."` FROM $table ";
		// Executa SQL
		$this->exec();

	}


	// Executa WHERE
	// VOID where(SQL_WHERE)
	public function where($where){
		// Muda SQL para SQL com WHERE
		$this->SQL= " SELECT `".implode("`,`",$this->cols)."` FROM $this->table WHERE $where";
		// Salva where em uma variavel
		$this->VER_WHERE = $where;
		// Executa consulta
		$this->exec();

	}

	// Executa Consulta
	// VOID exec(void)
	private function exec(){
		// Consulta da tabela
		$this->CON_TABLE = $this->CON->query($this->SQL);
		// Salva o número de linhas encontradas
		$this->rowCount = $this->CON_TABLE->rowCount();
		
	}

	// Retorna valores salvando como objetos
	// RESOURCE run(void)
	public function run(){
		// Consulta fetch
		$fetch  = $this->CON_TABLE->fetch(PDO::FETCH_ASSOC);
		// Se retornar algum valor
		if($fetch!=false){
			// Lê todos os nomes  das colunas
			foreach($this->cols as $collum){
				// Salva as colunas 
				$this->$collum = $fetch[$collum];
				// Salva a coluna temporaria
				$j = "f_".$collum;
				$this->$j = $fetch[$collum];
			}
		}
		return $fetch;
	}


	// Gera uma consulta SQL
	// PDOStatement SQL(SQL)
	public function SQL($sql){
		return $this->CON->query($sql);
	}

	// Diz para classe que deseja criar uma nova linha na tabela
	// VOID newRow(null)
	public function newRow(){
		// Confirma que é uma nova linha
		$this->create_new_row = true;
		// Lê todas as colunas
		foreach($this->cols as $name){
		      // Muda colunas temporarias para NULL (entendera mais na frente)
                       $f = "f_".$name;
                       $this->$f = null;                                       
		}

	}

	// Salva Linha na tabela
	// PDOStatement save(VOID)
	public function save(){
	
		// Verifica se não esta inserindo uma nova linha, então esta atualizando a linha atual
		if($this->create_new_row==false){
			
			$cl = array();
			$wh = array();
			// Lê todas as colunas
			foreach($this->cols as $name){
				$f = "f_".$name;
				// Se estiver diferente a coluna temporaria com a coluna verdadeira
				if($this->$name!=$this->$f){ // Foi realiza edição nesta coluna
					// Insere em array temporario edição
					array_push($cl,"`$name`='".$this->$name."'");
				}
				// Adiciona where na consulta
				array_push($wh,"`$name`='".$this->$f."'");
			}		

			//SQL RESOURCE (INSERT)
			$this->SQL_R = "UPDATE $this->table SET ".implode(",",$cl)." WHERE ".implode(" AND ",$wh);
		
	
		

		}else{ // Esta inserindo uma nova linha
			$cl = array(); // Coluna
			// Lê todas as colunas
	                foreach($this->cols as $name){
				// Insere em um array de colunas
        	                array_push($cl,$this->$name);
                	}

			// SQK RESOURCE (INSERT)
	                $this->SQL_R = "INSERT INTO $this->table (`".implode("`,`",$this->cols)."`) VALUES('".implode("','",$cl)."')";

		}
		// SQL temporario é o SQL RESOURCE (UPDATE/INSERT)
		$sql_temp = $this->SQL_R;
		// Executa SQL temporario
		$j = $this->CON->query($sql_temp);
		// Se foi marcado como nova linha
		if($this->create_new_row==true){	
			// Salva como linha antiga
			$this->create_new_row = false;
			// Retorna ultimo id
			$this->lastId = $this->CON->lastInsertId();
		}
		
		// Retorna PDOStatement
                return $j;
		

	}


	// Deleta linha atual
	// PDOStatement deleteRow(VOID)
	public function deleteRow(){
		 $cl = array();// Colunas
 		 // Lê todas as colunas		
		 foreach($this->cols as $name){
				// colunas temporarias
                                $f = "f_".$name;
				// Cria where
                                array_push($cl,"`$name`='".$this->$name."'");                              
                 }

		// SQL para deletar linha
		$sql = "DELETE FROM $this->table where ".implode(" AND ",$cl);
                
		// Retorna PDOSatement
		return $this->CON->query($sql);	

	}


}
?>
