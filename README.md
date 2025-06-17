# E-commerce Simples Laravel

Desenvolvedor: Brenio Filho.

Aplicação teste de e-commerce desenvolvida com Laravel e Supabase.



## Tecnologias Utilizadas

- Laravel (Framework PHP)
- ESLint e Prettier (Formatação e qualidade de código)
- PostgreeSQL (Banco de dados)

## Requisitos do Sistema (Sem Docker)

- PHP 8.1 ou superior
- Composer
- XAMPP, WAMP, MAMP ou servidor web similar
- Docker (opcional para facilitação de execução)

## Instalação Local (Sem Docker)

1. Clone o repositório e acesse a pasta do projeto:
```bash
cd ecommerceApp
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
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

5. Execute as migrações do banco de dados (a ser implementado):
```bash
php artisan migrate
```

6. Inicie o servidor:
```bash
php artisan serve
```

Os endpoints estarão disponíveis em `http://localhost:8000`

## Instalação com Docker

1. Instale o Docker no seu computador

2. Execute na raiz do projeto, onde existem os arquivos do Docker, os comandos abaixo:

```bash
    docker compose up
```
3. O endpoint estará ouvindo requisições corretamente.

## Comandos Úteis em outros casos
### Comandos Laravel
```bash
# Criar migration
php artisan make:migration nome_da_migration

# Criar controller
php artisan make:controller NomeController

# Criar model
php artisan make:model NomeModel

# Limpar cache
php artisan cache:clear
```

### Comandos Docker
```bash
# Iniciar containers
docker-compose up

# Parar containers
docker-compose down

# Logs
docker-compose logs -f

# Acessar container da aplicação
docker-compose exec app bash

# Acessar MySQL
docker-compose exec mysql mysql -u root -p

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
- `node_modules/` - Dependências JavaScript (NPM)

## Documentação de Rotas / Endpoints
- A documentação das rotas e endpoints está disponível no arquivo DOCS.md, na raiz deste projeto.

## Desenvolvimento

O projeto utiliza:
- ESLint para linting de JavaScript/TypeScript
- Prettier para formatação de código
- Vite para compilação de assets
- PHPUnit para testes

## Informações Adicionais
Este é apenas um repositório teste sem fins de utilização para produção.