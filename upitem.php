<?php 
  require 'falcon.class.php';

  $falcon = new falcon;
  
  // Conexão com o banco //
  $falcon->conn('localhost','test','root','');
?>  
<html>
  <head> 
    <title>falconPHP - Exemplo upItem();</title> 
  </head>
  <body>
    <form method="POST" action="">
      <input type="text" name="item"/>
      <input type="text" name="preco"/>
      <button type="submit">Cadastrar</button>
    </form>
  </body>
</html>  
<?php 
  if($_POST){
    // Configuração do item, nome ( pode ser diferente do que esta na tabela ) e tabela.
    $falcon->config('item','produtos');
    // edita os campos vindo do $_POST, de acordo com as configurações da função config().
    $falcon->upItem('id','5',$_POST);  
  }
?>
