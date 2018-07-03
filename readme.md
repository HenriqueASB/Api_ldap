# ldap-auth

## Introdução

Este microsserviço fornece uma API de autenticação que realiza a autenticação de um usuário num servidor AD (Active Directory) configurado, utilizando o protocolo LDAP, retornando os dados básicos do usuário cadastrado (autenticação ok) ou FALSE em caso de falha.

A API é um *stateless service* e não armazena de forma alguma os dados do usuário nem faz parte de seu escopo realizar qualquer tipo de gerenciamento de permissões ou operações do AD.

O ideal é que haja algum outro serviço de gerenciamento de usuários que utilize esta API como parte da etapa de autenticação.

## API / Utilização

Realizar uma requisição HTTP POST para http://endereco/api.php enviando um objeto JSON conforme abaixo (dados fictícios):
```json
{
	"action": "auth",
	"token": "SENHA_SECRETA",
	"username": "d000000",
	"password": "123456"
}
```
*Obs: O campo "token" deverá ser preenchido com um código secreto configurado na API, de forma que somente as aplicações que tenham esse token possam acessar este microsserviço.*

Caso a autenticação dê certo, será retornado (HTTP STATUS 200) um objeto com os dados do usuário:
```json
{
    "status": 200,
    "data": {
        "username": "d000000",
        "fullName": "Fulano da Silva",
        "email": "fulano@example.com"
    }
}
```

Caso contrário, será retornada a mensagem abaixo:
```json
{
    "status": 401,
    "data": "Usuário e/ou senha inválidos!"
}
```

Qualquer outro tipo de requisição não prevista irá retornar amensagem abaixo:
```json
{
    "status": 404,
    "data": "Erro 404: conteúdo não encontrado!"
}
```

## Instalação (Docker)

Requisitos:
* Docker + docker-compose

1. Clonar este repositório no servidor desejado (com docker instalado).

1. Criar arquivo `.env` na raiz do projeto, semelhante ao exemplo fornecido (`.env.sample`), e alterar com as configurações do Servidor AD (mais detalhes sobre as variáveis logo abaixo).

1. O arquivo `Dockerfile` está pré-configurado com uma proxy. Alterá-lo com a proxy do seu provedor ou simplesmente remover as configurações caso não haja necessidade de proxy.

1. Por default será criado o container `ldap-auth` na porta `5005`. Caso queira alterar, basta editar o arquivo `docker-compose-yml` com sua preferência.

1. Executar o comando `docker-compose up -d`.

## Variáveis de ambiente (.env)

Para que a aplicação funcione corretamente, é necessário configurar um arquivo `.env` conforme instruções de instalação.

Segue abaixo a descrição de cada variável necessária:

| **Variável** | **Descrição** |
|---|---|
| APP_ENV | Define o nível de exibição de erros da aplicação.<br>PROD = somente erros fatais<br> DEV = todos os erros |
| LDAP_SERVER | Endereço do servidor AD |
| LDAP_PORT | Porta do servidor AD (389 por default) |
| LDAP_BASE_DN | Distinguished Name (DN) do AD |
| LDAP_DOMAIN | Domínio da rede |
| LDAP_USERNAME_FIELD | Nome do campo para realizar o filtro do username |
| ACCESS_TOKEN | Token de acesso à API, que será fornecido para as aplicações que irão se conectar neste serviço |
| TEST_USERNAME | Usuário válido para testes de conectividade |
| TEST_PASSWORD | Senha do usuário de testes |

## Testes

O arquivo `test.php` pode ser utilizado para realizar teste de conectividade com o Servidor AD configurado.

Ele verifica se o usuário e senha informados no arquivo `.env` são válidos.

A execução do teste pode ser feita via linha de comando com o próprio PHP:
```sh
php -f test.php
```
ou utilizando NodeJS (opcional) caso esteja instalado, utilizando o comando abaixo:
```sh
npm test
```

Para testar a API já publicada, pode-se testar diretamente na linha de comando, via cURL:

```sh
curl --header "Content-Type: application/json" --request POST --data '{"action":"auth", "token":"SENHA","username":"d000000","password":"123456"}' http://endereco/api.php
```

Response:
```sh
{"status":200,"data":{"username":"d000000","fullName":"Fulano da Silva","email":"fulano@exemplo.com"}}
```

Ou ainda, utilizando o [Postman](https://www.getpostman.com/).

## Desenvolvimento / Documentação

* **api.php**  
Arquivo principal da API que irá receber as requisições HTTP POST.

* **config.php**
Arquivo com as configurações gerais de toda a aplicação. 

* **api.class.php**  
Lib simples para facilitar o roteamento e tratamento das requisições da API.  
Métodos:
    * **check()**: verifica se o campo especificado foi informado na requisição;
    * **post()**: verifica se é uma requisição válida e se o token confere;
    * **send()**: devolve uma resposta em JSON para a requisição, podendo ser informado um código HTTP de Status diferente.

* **ldap.class.php**
Classe que realiza a conexão e comunicação com o servidor AD utilizando o protocolo LDAP.  
Métodos:
    * **connect()**: prepara a conexão com os dados fornecidos (infelizmente a lib nativa do PHP não possui um tratamento de exceções adequado);
    * **authenticate()**: faz a conexão com o AD verificando se o usuário e senha são válidos;
    * **getUser():** obtém e retorna um array com os dados do usuário autenticado.

* **test.php**  
Arquivo para testes simples de conectividade com o AD (vide sessão de testes).