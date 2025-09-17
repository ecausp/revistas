# Sistema de Orçamento das Revistas

## Motivação

Este sistema surgiu da necessidade local da ECA-USP em gerenciar os pedidos de serviços de editora e gráfica. De início, pensamos em utilizar o sistema da FFLCH [uspdev/grafica](https://github.com/uspdev/grafica), observamos que a ECA possui um fluxo e organizacional diferente. Na FFLCH existem os setores Gráfica e Editora, já na ECA não temos estes setores e contratamos terceiros. Desta maneira, desenvolvemos este sistema para atender a demanda local, mas gerenralizando, caso outra unidade tenha o mesmo fluxo.

## Conceito

As revistas podem realizar pedidos seguindo o [fluxo](graficaDiagramaUML.png) abaixo:

1. O Financeiro faz uma consulta para serviços de gráfica e editoração e cria uma Licitação. Na conclusão, obtém a Empresa vencedora e cadastra no sistema o valor total licitado, vigência, distribuição dos limites (serviços e quantidades) entre as revistas, tipos de serviços, quantidades, valor unitário de cada serviço, contato e credenciais de acesso desta Empresa.
2. A Revista recebe notificação da conclusão da Licitação;
3. A Revista cria o Pedido de Serviço, visualiza as tabelas de valores contratados, recursos financeiros disponíveis e gerencia os pedidos;
4. A Empresa verifica se os serviços solicitados nos pedidos estão de acordo;
5. O Financeiro verifica se os valores estão de acordo e faz o empenho;
6. O Sistema debita o valor dos serviços, atualiza o saldo da Revista e notifica a Empresa para execução dos serviços;
7. A Revista confere e recebe o serviço;
8. O Sistema notifica o Financeiro para pagamento;
9. O Financeiro efetua o pagamento.

## Procedimentos de Deploy

```
composer install
cp .env.example .env
# no arquivo .env configure as credenciais mysql, senha única e replicado
php artisan key:generate
php artisan migrate
php artisan serve
```

## Seeder de Licitações

Para popular o banco de dados com dados de teste, usamos um seeder. Para isso, basta usar o comando

```
php artisan db:seed --class=LicitacaoSeeder
```

Para alterar a quantidade de dados gerados, modificar o arquivo `database\seeders\LicitacaoSeeder.php`;
Para alterar o modo com que esses dados falsos são gerados, modificar o arquivo `database\factories\LicitacaoFactory.php`;
