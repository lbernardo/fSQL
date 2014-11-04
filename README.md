fSQL
====

Conexão com banco de dados por meio de objetos

<h1>Iniciando</h1>
===
Para iniciar o fSQL é preciso criar uma instância da classe fSQL.
<br>
<code>$fSQL = new fSQL($host,$user,$pass,$db,$type);</code>
Onde:
<ul>
 <li>$host: Servidor do banco de dados</li>
 <li>$user: Usuário do banco de dados</li>
 <li>$pass: Senha do banco de dados</li>
 <li>$db : Nome do banco de dados</li>
 <li>$type: Tipo de conexão (Padrão: mysql | Tipos: DRIVERS PDO)</li>
</ul>
==
<h1>Trabalhando com Tabelas</h1>
Para selecionar tabelas você deve usar a instância do fSQL e chamar o metodo fSQL->from(TABELA) passando como argumento<br>
o nome da tabela, onde o sistema retornará uma instância para uma sub-classe (fSQL_Table), onde a mesma executara as 
funções dentro da tabela
<br>
Ex:<br>
  <code>$tabela = $fSQL->from("teste"); // Seleciona a tabela teste( Select col_1,col_2,...,col_n FROM teste )</code>
