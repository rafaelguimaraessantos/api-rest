# 🚀 API RESTful de Usuários - CodeIgniter 3 + Docker

Uma API RESTful completa e profissional para gerenciamento de usuários, desenvolvida com **PHP 8.1**, **CodeIgniter 3** e **MySQL 8.0**, totalmente containerizada com **Docker**.

## ✨ O que esta API faz?

Esta API permite o **gerenciamento completo de usuários** através de endpoints RESTful, oferecendo:

- 📝 **Criar usuários** com validação completa
- 📋 **Listar usuários** com paginação e filtros avançados
- 🔍 **Buscar usuários** por ID, nome ou email
- ✏️ **Atualizar dados** de usuários existentes
- 🗑️ **Deletar usuários** (soft delete ou permanente)
- 🏥 **Health check** para monitoramento
- 🔒 **Segurança** com senhas criptografadas
- 📊 **Paginação inteligente** e filtros personalizados

## 🎯 Funcionalidades Principais

### ✅ CRUD Completo
- **CREATE** - Criar novos usuários
- **READ** - Listar e buscar usuários
- **UPDATE** - Atualizar dados existentes
- **DELETE** - Remover usuários (soft/hard delete)

### ✅ Recursos Avançados
- **Paginação** - Controle de limite e offset
- **Filtros** - Por status, busca textual, ordenação
- **Validação** - Dados de entrada rigorosamente validados
- **Segurança** - Senhas criptografadas, proteção XSS/SQL injection
- **CORS** - Habilitado para integração frontend
- **Health Check** - Monitoramento de status da API e banco

### ✅ Tecnologias Utilizadas
- **PHP 8.1** - Linguagem backend
- **CodeIgniter 3** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **Docker & Docker Compose** - Containerização
- **Apache** - Servidor web
- **PHPMyAdmin** - Interface de administração do banco

## 📋 Pré-requisitos

- **Docker** (versão 20.10+)
- **Docker Compose** (versão 2.0+)

## 🚀 Como Executar o Projeto

### Método 1: Setup Automático (Recomendado)

1. **Clone o repositório:**
   ```bash
   git clone <repository-url>
   cd api-rest
   ```

2. **Execute o script de setup:**
   ```bash
   chmod +x setup.sh
   ./setup.sh
   ```

3. **Aguarde a conclusão** - O script irá:
   - Baixar o CodeIgniter 3 completo
   - Construir os containers Docker
   - Inicializar o banco de dados
   - Verificar se tudo está funcionando

### Método 2: Setup Manual

1. **Baixar CodeIgniter:**
   ```bash
   chmod +x download-codeigniter.sh
   ./download-codeigniter.sh
   ```

2. **Iniciar containers:**
   ```bash
   docker-compose up -d --build
   ```

3. **Verificar status:**
   ```bash
   docker-compose ps
   ```

## 🌐 Acessar os Serviços

Após a execução bem-sucedida:

- **🔗 API Principal:** http://localhost:8080
- **🔗 PHPMyAdmin:** http://localhost:8081 (usuário: `root`, senha: `rootpassword`)
- **🔗 Health Check:** http://localhost:8080/api/health

## 📡 Endpoints da API

### 🌐 Base URL
```
http://localhost:8080/api
```

### 🏥 Health Check
```http
GET /api/health
```
Verifica o status da API e conexão com o banco de dados.

### 👥 Gerenciamento de Usuários

#### 📋 1. Listar Usuários
```http
GET /api/users
```

**Parâmetros de Query (opcionais):**
- `limit` - Número de registros (1-100, padrão: 10)
- `offset` - Registros para pular (padrão: 0)
- `status` - Filtrar por status (`active`, `inactive`)
- `search` - Buscar por nome ou email
- `order_by` - Campo para ordenação (`id`, `name`, `email`, `status`, `created_at`, `updated_at`)
- `order_dir` - Direção (`ASC`, `DESC`)

**Exemplos:**
```bash
# Listar todos
curl http://localhost:8080/api/users

# Com paginação
curl "http://localhost:8080/api/users?limit=5&offset=0"

# Filtrar usuários ativos
curl "http://localhost:8080/api/users?status=active"

# Buscar por nome
curl "http://localhost:8080/api/users?search=joão"
```

#### 🔍 2. Obter Usuário Específico
```http
GET /api/users/{id}
```

**Exemplo:**
```bash
curl http://localhost:8080/api/users/1
```

#### ➕ 3. Criar Usuário
```http
POST /api/users
Content-Type: application/json
```

**Campos obrigatórios:**
- `name` - Nome (2-100 caracteres)
- `email` - Email válido e único
- `password` - Senha (mínimo 6 caracteres)

**Campos opcionais:**
- `phone` - Telefone (máximo 20 caracteres)
- `status` - Status (`active` ou `inactive`, padrão: `active`)

**Exemplo:**
```bash
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@email.com",
    "password": "123456",
    "phone": "(11) 99999-9999"
  }'
```

#### ✏️ 4. Atualizar Usuário
```http
PUT /api/users/{id}
Content-Type: application/json
```

**Campos obrigatórios:**
- `name` - Nome atualizado
- `email` - Email atualizado (deve ser único)

**Campos opcionais:**
- `password` - Nova senha (se fornecida, mínimo 6 caracteres)
- `phone` - Telefone atualizado
- `status` - Status atualizado

**Exemplo:**
```bash
curl -X PUT http://localhost:8080/api/users/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Santos Silva",
    "email": "joao.santos@email.com",
    "phone": "(11) 88888-8888"
  }'
```

#### 🗑️ 5. Deletar Usuário
```http
DELETE /api/users/{id}
```

**Parâmetros opcionais:**
- `hard=true` - Deletar permanentemente (padrão: soft delete)

**Exemplos:**
```bash
# Soft delete (desativar usuário)
curl -X DELETE http://localhost:8080/api/users/1

# Hard delete (remover permanentemente)
curl -X DELETE "http://localhost:8080/api/users/1?hard=true"
```

## 🧪 Testando a API

### 🚀 Teste Rápido
```bash
# 1. Verificar se API está funcionando
curl http://localhost:8080/api/health

# 2. Listar usuários existentes
curl http://localhost:8080/api/users

# 3. Criar um novo usuário
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"Teste User","email":"teste@email.com","password":"123456"}'
```

## 📦 Collection do Postman

### 🎯 Importar Collection Completa

Esta API inclui uma **collection completa do Postman** com todos os endpoints e testes automatizados!

**Arquivos disponíveis:**
- `postman/Users_API_Collection.json` - Collection principal
- `postman/Environment.json` - Environment com variáveis
- `postman/README.md` - Documentação da collection

### 📥 Como Importar no Postman:

1. **Abra o Postman**
2. **Clique em "Import"** (canto superior esquerdo)
3. **Selecione o arquivo** `postman/Users_API_Collection.json`
4. **Importe também** `postman/Environment.json`
5. **Selecione o environment** "Users API - Local Environment"

### ✨ O que a Collection Inclui:

- **🏥 Health Check** - Verificar status da API
- **👥 CRUD Completo** - Todos os endpoints de usuários
- **🧪 Testes de Validação** - Cenários de erro e validação
- **🤖 Testes Automatizados** - Verificação automática de respostas
- **📊 Variáveis de Ambiente** - URLs e IDs configurados
- **📝 Documentação** - Cada endpoint documentado

### 🎮 Executar Todos os Testes:

1. **Selecione a collection** "API RESTful de Usuários"
2. **Clique em "Run"** (botão play)
3. **Execute todos os testes** de uma vez
4. **Veja os resultados** automaticamente

## 📊 Exemplos de Resposta

### ✅ Sucesso - Lista de Usuários
```json
{
    "status": "success",
    "data": [
        {
            "id": "1",
            "name": "João Silva",
            "email": "joao@email.com",
            "phone": "(11) 99999-9999",
            "status": "active",
            "created_at": "2025-07-29 18:29:10",
            "updated_at": "2025-07-29 18:29:10"
        }
    ],
    "pagination": {
        "total": 4,
        "count": 1,
        "limit": 10,
        "offset": 0,
        "has_more": false
    }
}
```

### ✅ Sucesso - Health Check
```json
{
    "status": "success",
    "message": "API está funcionando",
    "timestamp": "2025-07-29 18:30:00",
    "version": "1.0.0",
    "environment": "development",
    "database": {
        "status": "connected",
        "host": "db",
        "database": "users_api"
    },
    "statistics": {
        "total": 4,
        "active": 3,
        "inactive": 1,
        "created_today": 0
    }
}
```

### ❌ Erro - Validação
```json
{
    "status": "error",
    "message": "Dados inválidos",
    "errors": {
        "name": "Nome é obrigatório",
        "email": "Email inválido",
        "password": "Senha deve ter pelo menos 6 caracteres"
    }
}
```

### ❌ Erro - Email Duplicado
```json
{
    "status": "error",
    "message": "Email já está em uso"
}
```

## 🔒 Validações e Regras de Negócio

### ✅ Campos Obrigatórios (Criação)
- **`name`** - Nome completo (2-100 caracteres, apenas letras e espaços)
- **`email`** - Email válido e único (máximo 150 caracteres)
- **`password`** - Senha segura (mínimo 6 caracteres)

### 🔧 Campos Opcionais
- **`phone`** - Telefone (máximo 20 caracteres, formato válido)
- **`status`** - Status do usuário (`active` ou `inactive`, padrão: `active`)

### ✏️ Campos Obrigatórios (Atualização)
- **`name`** - Nome atualizado (2-100 caracteres)
- **`email`** - Email atualizado (deve ser único)
- **`password`** - Opcional (se fornecida, mínimo 6 caracteres)
- **`phone`** - Opcional (formato válido)
- **`status`** - Opcional (`active` ou `inactive`)

### 🛡️ Regras de Segurança
- **Senhas criptografadas** com `password_hash()`
- **Validação rigorosa** de todos os campos de entrada
- **Proteção contra SQL Injection** e XSS
- **Email único** por usuário
- **Soft delete** por padrão (preserva dados)

## 🔧 Comandos Docker Úteis

### 🚀 Gerenciamento de Containers
```bash
# Iniciar todos os serviços
docker-compose up -d

# Parar todos os serviços
docker-compose down

# Rebuild completo (após mudanças no código)
docker-compose up -d --build

# Ver status dos containers
docker-compose ps

# Ver logs em tempo real
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f web
```

### 🔍 Debug e Manutenção
```bash
# Acessar container da aplicação
docker exec -it users_api_web bash

# Acessar MySQL diretamente
docker exec -it users_api_db mysql -u root -p

# Verificar estrutura de arquivos
docker exec users_api_web ls -la /var/www/html/

# Limpar tudo e recomeçar
docker-compose down --volumes --rmi all
./setup.sh
```

## 🗄️ Banco de Dados

### 📊 Informações de Conexão
- **Host**: localhost:3306
- **Usuário**: root
- **Senha**: rootpassword
- **Database**: users_api
- **Charset**: utf8mb4

### 🌐 PHPMyAdmin
- **URL**: http://localhost:8081
- **Usuário**: root
- **Senha**: rootpassword

### 📋 Estrutura da Tabela `users`
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 📁 Estrutura do Projeto

```
users-api-docker/
├── docker-compose.yml          # Configuração dos containers
├── Dockerfile                  # Imagem da aplicação
├── apache-config.conf          # Configuração do Apache
├── database/
│   └── init.sql               # Schema inicial do banco
├── src/                       # Código da aplicação
│   ├── .htaccess
│   ├── index.php
│   └── application/
│       ├── config/
│       │   ├── config.php
│       │   ├── database.php
│       │   └── routes.php
│       ├── controllers/
│       │   └── api/
│       │       ├── Users.php
│       │       └── Health.php
│       ├── libraries/
│       │   └── REST_Controller.php
│       └── models/
│           └── User_model.php
└── README.md
```

## 🔍 Testando a API

### Usando cURL

**Health check:**
```bash
curl http://localhost:8080/api/health
```

**Listar usuários:**
```bash
curl http://localhost:8080/api/users
```

**Criar usuário:**
```bash
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Maria Santos",
    "email": "maria@email.com",
    "password": "123456",
    "phone": "(11) 88888-8888"
  }'
```

**Atualizar usuário:**
```bash
curl -X PUT http://localhost:8080/api/users/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Maria Silva Santos",
    "email": "maria.silva@email.com"
  }'
```

**Deletar usuário (soft delete):**
```bash
curl -X DELETE http://localhost:8080/api/users/1
```

**Deletar usuário (hard delete):**
```bash
curl -X DELETE "http://localhost:8080/api/users/1?hard=true"
```

## 🛡️ Segurança

- Senhas criptografadas com `password_hash()`
- Validação rigorosa de entrada
- Headers de segurança configurados
- Proteção contra XSS e SQL injection
- CORS configurado
- Logs de erro detalhados

## 🚨 Códigos de Status HTTP

| Código | Significado | Quando Ocorre |
|--------|-------------|---------------|
| **200** | ✅ Sucesso | Operação realizada com sucesso |
| **201** | ✅ Criado | Usuário criado com sucesso |
| **400** | ❌ Dados Inválidos | Erro de validação nos dados enviados |
| **404** | ❌ Não Encontrado | Usuário ou endpoint não existe |
| **405** | ❌ Método Não Permitido | Método HTTP não suportado |
| **409** | ❌ Conflito | Email já está em uso |
| **500** | ❌ Erro Interno | Erro no servidor |
| **503** | ❌ Serviço Indisponível | API ou banco fora do ar |

## 🛠️ Desenvolvimento e Customização

### 📁 Estrutura do Projeto
```
api-rest/
├── 🐳 docker-compose.yml          # Configuração dos containers
├── 🐳 Dockerfile                  # Imagem da aplicação
├── ⚙️ apache-config.conf          # Configuração do Apache
├── 📊 database/init.sql           # Schema inicial do banco
├── 📮 postman/                    # Collection do Postman
│   ├── Users_API_Collection.json
│   ├── Environment.json
│   └── README.md
├── 🚀 setup.sh                    # Script de instalação automática
├── 📥 download-codeigniter.sh     # Script para baixar CodeIgniter
└── 📂 src/                        # Código da aplicação
    ├── .htaccess
    ├── index.php
    └── application/
        ├── config/
        ├── controllers/api/
        ├── libraries/
        └── models/
```

### 🔧 Desenvolvimento Local
Para desenvolvimento com hot reload:
```bash
# Edite docker-compose.yml e descomente:
volumes:
  - ./src:/var/www/html

# Rebuild
docker-compose up -d --build
```

### 📝 Logs e Debug
```bash
# Logs da aplicação
docker-compose logs -f web

# Logs do Apache
docker exec users_api_web tail -f /var/log/apache2/error.log

# Logs do MySQL
docker-compose logs -f db
```

## 🆘 Troubleshooting

### ❓ Problemas Comuns

**🔴 API não responde:**
```bash
# Verificar containers
docker-compose ps

# Verificar logs
docker-compose logs web

# Restart completo
docker-compose down && docker-compose up -d
```

**🔴 Erro 404 nos endpoints:**
```bash
# Verificar se CodeIgniter foi baixado
docker exec users_api_web ls -la /var/www/html/system/

# Se não existir, execute:
./download-codeigniter.sh
docker-compose up -d --build
```

**🔴 Banco de dados não conecta:**
```bash
# Verificar MySQL
docker exec users_api_db mysql -u root -p -e "SHOW DATABASES;"

# Aguardar inicialização completa
docker-compose logs -f db
```

## 🎯 Próximos Passos

Esta API está pronta para:
- ✅ **Produção** - Com Docker e configurações de segurança
- ✅ **Integração Frontend** - CORS habilitado
- ✅ **Testes Automatizados** - Collection Postman completa
- ✅ **Monitoramento** - Health check endpoint
- ✅ **Escalabilidade** - Arquitetura containerizada

## 🤝 Contribuição

1. **Fork** o projeto
2. **Crie uma branch** para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. **Abra um Pull Request**

## 📄 Licença

Este projeto está sob a **licença MIT**. Veja o arquivo `LICENSE` para mais detalhes.

---

## 🎉 Conclusão

Você agora tem uma **API RESTful completa e profissional** para gerenciamento de usuários, com:

- 🐳 **Docker** para fácil deployment
- 📮 **Collection Postman** para testes
- 🔒 **Segurança** implementada
- 📊 **Documentação** completa
- 🧪 **Testes** automatizados

**🚀 Comece agora:** Execute `./setup.sh` e sua API estará funcionando em minutos!