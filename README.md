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
<br>
<br>
Para retornar o número de linhas encontradas na consulta usa-se o codigo abaixo:<br>
<code>print $tabela->rowCount; // Retorna o número de linhas</code>
<br>

Para executar uma consulta partircular usa:
<br>
<code>$tabela->where("col_1='0' AND col_2='n' OR col_3 LIKE '%4%'"); // Usando comando SQL normal
<br>

Para retornar valores, é preciso executar o método "fTable::run()", onde ele salva em um membro do objeto com o nome da coluna. Caso deseje mostrar uma lista o método "fTable::run()" deve estar em um loop
<br>
Ex:
<br>
<pre>
 $tabela->run(); // Salvo os dados nos membros do objeto
 print $tabela->col_1; // Imprimo a coluna "col_1" da tabela
 print $tabela->col_2; // Imprimo a coluna "col_2" da tabela
</pre>
<br>
Ex Lista:<br>
<pre>
 while($tabela->run()){<br>
  print $tabela->con_1; // Imprimo a coluna "col_1" da linha corrida
 }</br>
 </pre>
 <br><br>
 
 Para inserir uma nova linha na tabela, devo acessar os membros e colocar os valores desejados(caso não selecione algum membro o valor padrão irá como "NULL") antecedendo com a chamada do método fTable::newRow(). E para salvar dados alterados uso o método fTable::save() <br>
 Ex:<br>
 <pre>
  $tabela->newRow(); // Digo que vou inserir novas linhas
  $tabela->col_1 = "Ola mundo";  // Digo que a coluna "col_1" tera o valor "Ola mundo"
  $tabela->col_2 = "JJ"; // Digo que a coluna "col_2" tera o valor "JJ";
  $tabela->save(); // Salvo alterações na tabela (Comando usado tanto para UPDATE como para INSERT)
 </pre>
 <br><br>
 Para realizar atualização de algum dado da tabela, usaremos a mesma forma acima, entretanto retirando o método fTable::newRow(); Isso fará com que alteremos a linha corrida<br>
 Ex:<br>
 <pre>
 $tabela->col_1 = "Ola mundo2"; // Coluna assume valor "Ola mundo2"
 $tabela->save(); // Salvo alterações na tabela
 </pre>
 <br>
 Para realizar exclusão de linhas usa-se o método fTable::deleteRow(), ele irá excluir a corrida. Por exemplo, se tiver dentro de um loop ira excluir a linha do loop atual
 
 
