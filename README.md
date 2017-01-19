# falconPHP

A proposta desta classe é de agilizar o desenvolvimento de scripts de interação com o banco de dados, sendo assim tendo funções que facilitam o gerenciamento de conteúdo.

A classe ainda está sendo atualizada, mas por falta de tempo não tive como ajustar algumas coisas.

Exemplos :
- Funções


-- conn();
--- Está função faz com que tudo funcione, ela estabelece uma conexão com o banco de dados utilizando PDO e sem esta função nossas outras funções não vão funcionar pois são dependentes de que a conexão seja estabelecida, os parametros da função são : host, banco de dados, usuario, senha.


-- config();
--- Com esta função você define as configurações básicas como nome do item e tabela, config('nome do item','tabela'), esta configuração sera utilizada pela class para adicionar, deletar e editar items.


-- imagesConfig();
--- Com esta função você configura a pasta, tabela (db), campo(db) e qualidade de imagem  (0 a 100), estas configurações são validas para todas imagens que serão inseridas.


-- addItem();
--- Com esta função você podera adicionar um 'item' ao banco de dados apenas inserindo a variavel $_POST vinda do formulário, a função possui 3 parametros sendo eles 2 opcionais : imagens(variavel do tipo $_FILES), miniatura( variavel do tipo $_FILES) e 1 obrigatorio dados (variavel do tipo $_POST), não se preocupe, seus dados serão inseridos de acordo com as configurações feitas pela função config() ou seja nada de declarar os campos e os valores que serão inseridos ( assim como é feito em querys normais ), caso algum de seus campos for do tipo matriz ( array ) ele automaticamente será serializado.


-- upItem();
--- Com esta função você podera editar seus 'items' os parametros são : criterio ( podendo ser id,nome e etc ), campo a ser buscado pelo criterio ( podendo ser id,nome e etc ), imagens ( variavel do tipo $_FILES ), dados ( variavel do tipo $_POST ), miniatura ( variavel do tipo $_FILES).

-- del();
--- Com esta função você vai poder deletar um ou mais 'items' os parametros são : criterio ( podendo ser id,nome e etc ), campo a ser buscado pelo criterio ( podendo ser id,nome e etc ), redirecionamento.

A classe possui mais funções a serem exploradas, que tal contribuir para melhora-la ?.
