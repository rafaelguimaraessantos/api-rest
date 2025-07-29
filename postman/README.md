# Collection Postman - API RESTful de Usuários

Esta collection contém todos os endpoints da API RESTful de usuários desenvolvida com CodeIgniter 3 e Docker.

## 📁 Arquivos

- `Users_API_Collection.json` - Collection principal com todos os endpoints
- `Environment.json` - Environment com variáveis de ambiente
- `README.md` - Este arquivo de documentação

## 🚀 Como Importar no Postman

### 1. Importar a Collection

1. Abra o Postman
2. Clique em **Import** (botão no canto superior esquerdo)
3. Selecione **File** e escolha o arquivo `Users_API_Collection.json`
4. Clique em **Import**

### 2. Importar o Environment

1. No Postman, clique no ícone de **Environment** (canto superior direito)
2. Clique em **Import**
3. Selecione o arquivo `Environment.json`
4. Clique em **Import**
5. Selecione o environment **"Users API - Local Environment"**

## 📋 Estrutura da Collection

### 🏥 Health Check
- **Health Check** - Verifica status da API e conexão com banco

### 👥 Usuários - CRUD
- **Listar Todos os Usuários** - GET /api/users
- **Listar Usuários com Paginação** - GET /api/users?limit=5&offset=0
- **Listar Usuários com Filtros** - GET /api/users?status=active&search=joão
- **Obter Usuário por ID** - GET /api/users/{id}
- **Criar Usuário** - POST /api/users
- **Criar Usuário - Dados Mínimos** - POST /api/users (apenas campos obrigatórios)
- **Atualizar Usuário** - PUT /api/users/{id}
- **Atualizar Usuário com Senha** - PUT /api/users/{id}
- **Deletar Usuário (Soft Delete)** - DELETE /api/users/{id}
- **Deletar Usuário (Hard Delete)** - DELETE /api/users/{id}?hard=true

### 🧪 Testes de Validação
- **Criar Usuário - Email Inválido** - Testa validação de email
- **Criar Usuário - Senha Muito Curta** - Testa validação de senha
- **Criar Usuário - Campos Obrigatórios Vazios** - Testa campos obrigatórios
- **Criar Usuário - Email Duplicado** - Testa unicidade do email
- **Obter Usuário - ID Inválido** - Testa validação de ID
- **Obter Usuário - Não Encontrado** - Testa usuário inexistente

## 🔧 Variáveis de Environment

- `base_url` - URL base da API (http://localhost:8080)
- `user_id` - ID do usuário para testes (padrão: 1)
- `created_user_id` - ID do usuário criado (preenchido automaticamente)

## 🧪 Testes Automatizados

Cada endpoint inclui testes automatizados que verificam:

- ✅ Status codes corretos
- ✅ Estrutura da resposta JSON
- ✅ Presença de campos obrigatórios
- ✅ Validação de dados
- ✅ Tratamento de erros

## 📝 Como Usar

### 1. Ordem Recomendada de Testes

1. **Health Check** - Verificar se API está funcionando
2. **Listar Todos os Usuários** - Ver usuários existentes
3. **Criar Usuário** - Criar um novo usuário
4. **Obter Usuário por ID** - Buscar o usuário criado
5. **Atualizar Usuário** - Modificar dados do usuário
6. **Listar com Filtros** - Testar filtros e paginação
7. **Deletar Usuário** - Remover usuário

### 2. Executar Todos os Testes

1. Selecione a collection **"API RESTful de Usuários - CodeIgniter 3"**
2. Clique em **Run** (botão com ícone de play)
3. Selecione todos os requests ou apenas os desejados
4. Clique em **Run API RESTful de Usuários**

### 3. Personalizar Variáveis

- Altere `user_id` para testar com diferentes usuários
- Modifique `base_url` se a API estiver em outro endereço
- Use `created_user_id` para referenciar usuários criados dinamicamente

## 🎯 Exemplos de Uso

### Criar Usuário Completo
```json
{
    "name": "João Silva",
    "email": "joao.silva@email.com",
    "password": "123456",
    "phone": "(11) 99999-9999",
    "status": "active"
}
```

### Criar Usuário Mínimo
```json
{
    "name": "Maria Santos",
    "email": "maria.santos@email.com",
    "password": "123456"
}
```

### Atualizar Usuário
```json
{
    "name": "João Santos Silva",
    "email": "joao.santos@email.com",
    "phone": "(11) 88888-8888",
    "status": "active"
}
```

## 🔍 Filtros Disponíveis

- `limit` - Número máximo de registros (1-100)
- `offset` - Número de registros para pular
- `status` - Filtrar por status (`active`, `inactive`)
- `search` - Buscar por nome ou email
- `order_by` - Ordenar por campo (`id`, `name`, `email`, `status`, `created_at`, `updated_at`)
- `order_dir` - Direção (`ASC`, `DESC`)

## 📊 Códigos de Status Esperados

- **200** - Sucesso
- **201** - Criado com sucesso
- **400** - Dados inválidos
- **404** - Não encontrado
- **405** - Método não permitido
- **409** - Conflito (email já existe)
- **500** - Erro interno do servidor

## 🛠️ Troubleshooting

### API não responde
- Verifique se os containers Docker estão rodando: `docker-compose ps`
- Verifique se a URL base está correta: `http://localhost:8080`

### Testes falhando
- Execute o Health Check primeiro
- Verifique se o banco de dados está funcionando
- Confirme se há usuários cadastrados para os testes

### Variáveis não funcionam
- Certifique-se de que o environment está selecionado
- Verifique se as variáveis estão definidas corretamente

## 📞 Suporte

Para problemas ou dúvidas:
1. Verifique os logs: `docker-compose logs -f web`
2. Teste endpoints individuais primeiro
3. Confirme se a API está rodando: `curl http://localhost:8080/api/health`