# UsersAPI

Uma API que realiza um CRUD (Create, Update, Read e Delete) de usuários construída em PHP puro. Para tornar o projeto 
mais interessante foi implementado o registro de consumo do usuário em um bar. Um sistema de rotas baseado em front controller
foi implementado. Não há nesse projeto a utilização de frameworks.
## Executando o projeto
### Requisitos
- Um servidor Apache2
- PHP 8.1
- MySQL 8.0

### Configurando um Vhost para o projeto.
1. Crier um arquivo no diretório correspondente do apache na sua distribuição linux e o nomeie como mosyle-test.conf.
2. Cole o seguinte conteúdo dentro do arquivo:

``` xml
<VirtualHost *:80>
	# The ServerName directive sets the request scheme, hostname and port that
	# the server uses to identify itself. This is used when creating
	# redirection URLs. In the context of virtual hosts, the ServerName
	# specifies what hostname must appear in the request's Host: header to
	# match this virtual host. For the default virtual host (this file) this
	# value is not decisive as it is used as a last resort host regardless.
	# However, you must set it for any further virtual host explicitly.
	#ServerName www.example.com

	ServerAdmin test@gmail.com
	ServerName mosyle-test

	DocumentRoot {path}/mosyle-test/public

	# Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
	# error, crit, alert, emerg.
	# It is also possible to configure the loglevel for particular
	# modules, e.g.
	#LogLevel info ssl:warn

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined

	# For most configuration files from conf-available/, which are
	# enabled or disabled at a global level, it is possible to
	# include a line for only one particular virtual host. For example the
	# following line enables the CGI configuration for this host only
	# after it has been globally disabled with "a2disconf".
	#Include conf-available/serve-cgi-bin.conf

	<Directory {path}/mosyle-test/public>
		Options Indexes FollowSymLinks
		AllowOverride All
		Order allow,deny
		Allow from all	
	</Directory>
</VirtualHost>

```
3. Crie o host correpondente no arquivo de hosts da sua distribuição.
```
127.0.0.1	 localhost
127.0.1.1    mosyle-test
```
4. Execute os seguintes comando para habilitar o host
```shell
sudo a2ensite mosyle-test.conf
sudo systemctl reload apache2
```

Pronto, se tudo correu como esperado a aplicação deverá estar pronta para receber requisições.

## Configurando o projeto
1. No arquivo global.php configure sua conexão com o banco de dados, sem ainda setar o nome do banco.
```php
<?php  
  
const DATABASE_CONNECTION = [  
    'HOST' => 'localhost',  
    'PORT' => '3306',  
    'USERNAME' => '',  
    'PASSWORD' => '',  
    'DATABASE_NAME' => ''  
];
```
3. Execute o seguinte comando no diretório raiz do projeto para rodar as queries DDL na sua conexão
```shell
php install_database.php
```
3. De volta ao arquivo global.php, sete a chave DATABASE_NAME como 'bar'.
```php
<?php  
  
const DATABASE_CONNECTION = [  
    'HOST' => 'localhost',  
    'PORT' => '3306',  
    'USERNAME' => 'root',  
    'PASSWORD' => '12345',  
    'DATABASE_NAME' => 'bar'  
];
```

Seguindo esses passos corretamente, a aplicação deve estar pronta para uso.
### Algumas observações
- A classe de roteamento que foi implementada no projeto é relativamente simples, mas funciona. Visto isso, alguns problemas podem acontecer em casos de requisições que requerem parâmetros e esses não são enviados, como por exemplo, um response Not Found ao invés de um BadRequest.
- O Router também avalia explicitamente o método utilizado na requisição. Ou seja:
```php

// Se a rota '/users/{id}' for acessada utilizando o método PUT, mas sem o ID do usuário,
// um response 404 Not Found será lançado, ao invés de um 400 Bad Request, ou mesmo ou 500 Server Error. 

Router::add('/users/{id}', 'PUT', UserController::class, 'edit');

// Se a rota '/users' for acessada utilizando o método GET por exemplo,
// um response 404 Not Found será lançado, ao invés de um 405 Method Not Allowed.

Router::add('/users', 'POST', UserController::class, 'create');
```
