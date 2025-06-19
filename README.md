# E-commerce Simples Laravel

Desenvolvedor: Brenio Filho.

Aplicação teste de e-commerce desenvolvida com Laravel e Supabase.



## Tecnologias Utilizadas

- Laravel (Framework PHP)
- Prettier (Formatação e qualidade de código)
- PostgreeSQL (Banco de dados)

## Requisitos do Sistema (Sem Docker)

- PHP 8.1 ou superior
- Composer
- XAMPP, WAMP, MAMP ou servidor web similar
- Docker (opcional para facilitação de execução)

## Advise

- Utilizei para fazer o banco de dados do projeto o Supabase, mas às vezes ele dá um bug de timeout e/ou com o Docker ele fica com problemas para aceitar conexões IPv4, que é o padrão do Docker, então tive que fazer um workaround para que funcionasse. Caso não funcione com o Docker, tente a instalação local, e caso o Supabase dê timeout, tente novamente que a requisição deve funcionar normal -- corrigiria isto com mais tempo, encontrei este bug no meio do projeto.

## Instalação Local (Sem Docker)

1. Clone o repositório e acesse a pasta do projeto:
```bash
cd ecommerceApp
```

2. Instale o PHP, o Composer e suas dependências:
- Vai ser necessário que você instale o PHP, adicione eles às suas variáveis de ambiente, e logo em seguida instale o Composer. Após isto, rode este comando dentro da pasta do projeto:

```bash
composer install
```

3. Adicione o arquivo ENV:
```bash
cat .env
```

4. Configure seu banco de dados no arquivo `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=conexao.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD="senhaDoBanco"
DB_CONNECTION_TIMEOUT=300
```

- Estas informações devem estar disponíveis no e-mail que enviei sobre o projeto.

5. Execute as migrações do banco de dados (a ser implementado):
```bash
php artisan migrate 
php artisan db:seed
```

6. Configure seu PHP.ini para ter acesso ao pdo_pgsql e pgsql
- Estas são configurações para liberar os drivers de acesso do PHP ao tipo de banco de dados Postgree, que geralmente veêm desabilitadas por padrão.

Você precisa entrar no arquivo php.ini e descomentar (tirar o ";") da frente, onde deverá parecer desta forma após a edição:

```bash
    extension=pdo_pgsql
    extension=pdo_sqlite
    extension=pgsql
```

7. Inicie o servidor e começe a fazer as requisições com:
```bash
php artisan serve 
```

Os endpoints estarão disponíveis em `http://localhost:8000`
Informações sobre como fazer as requisições estão disponíveis em DOCS.md no diretório raiz deste projeto.

## Instalação com Docker

1. Instale o Docker no seu computador

2. Adicione estas configurações no seu Docker Engine ou nas configurações JSON do seu Docker Daemon:

Devido a limitações do banco de dados Supabase de receber requisições em IPv4, teremos que habilitar o Docker para trabalhar apenas com IPv6, o procedimento para este é o abaixo:

```bash
No windows:
    1: Habilitar IPv6 no Docker Desktop

    2. Abra o Docker Desktop.

    3. Clique no ícone de engrenagem (Settings) no canto superior direito.
    4. No menu lateral, vá para a seção Docker Engine.
    
    Você verá uma tela de edição de texto para o arquivo de configuração. Por favor, adicione as seguintes linhas dentro das chaves 

    {    
        "ipv6": true,
        "fixed-cidr-v6": "2001:db8:1::/64"
    }

    5. Clique no botão "Apply & Restart". O Docker irá reiniciar para aplicar as novas configurações.

    Importante: Se já houver algum texto lá, apenas adicione as novas linhas, separando-as com uma vírgula. O JSON deve ficar parecido com isto caso exista algum texto lá:

    {
      "builder": {
        "gc": {
          "enabled": true,
          "defaultKeepStorage": "20GB"
        }
      },
      "experimental": false,
      "ipv6": true,
      "fixed-cidr-v6": "2001:db8:1::/64"
    }

    ----------------------------------------------------------

No Linux:

    1. Edite o arquivo de configuração do daemon do Docker, localizado em: 
        /etc/docker/daemon.json

    2. Configure os seguintes parâmetros:
        {
            "ipv6": true,
            "fixed-cidr-v6": "2001:db8:1::/64"
        }

    3. Salve o arquivo de configuração.
        Para que as alterações tenham efeito, reinicie o serviço do Docker com o seguinte comando:
        
        sudo systemctl restart docker
    
```

3. Após habilitar o IPv6 no Docker, inicie o Docker Engine e execute na raiz do projeto, onde existem os arquivos do Docker, os comandos abaixo:

```bash
    docker-compose up
```
4. O endpoint estará ouvindo requisições corretamente em `http://localhost:8000`

## Comandos Úteis em outros casos

### Comandos Docker
```bash
# Iniciar containers
docker-compose up

# Parar containers
docker-compose down \ CTRL + C

# Refazer todos os containers e volumes
docker system prune -a
docker volume prune -a
docker-compose up
```

## Estrutura de Diretórios
- `routes/` - Rotas da aplicação
- `app/` - Código principal da aplicação Laravel
- `config/` - Configurações
- `database/` - Migrações e seeders
- `public/` - Arquivos públicos
- `resources/` - Views e assets
- `storage/` - Arquivos de upload e logs
- `tests/` - Testes automatizados
- `vendor/` - Dependências PHP (Composer)

## Documentação de Rotas / Endpoints
- A documentação das rotas e endpoints está disponível no arquivo DOCS.md, na raiz deste projeto.

## Informações Adicionais
Este é apenas um repositório teste sem fins de utilização para produção.