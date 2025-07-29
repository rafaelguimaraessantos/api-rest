#!/bin/bash

echo "🚀 Configurando API RESTful de Usuários com Docker..."

# Parar containers existentes
echo "📦 Parando containers existentes..."
docker-compose down

# Remover imagens antigas e volumes
echo "🧹 Limpando imagens antigas e volumes..."
docker-compose down --rmi all --volumes --remove-orphans 2>/dev/null || true

# Limpar cache do Docker
echo "🗑️ Limpando cache do Docker..."
docker system prune -f

# Construir e iniciar containers com rebuild forçado
echo "🔨 Construindo containers (rebuild completo)..."
docker-compose build --no-cache

echo "🚀 Iniciando containers..."
docker-compose up -d

# Aguardar containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 15

# Verificar se CodeIgniter foi baixado corretamente
echo "🔍 Verificando estrutura do CodeIgniter..."
docker exec users_api_web ls -la /var/www/html/

echo "🔍 Verificando se pasta system existe..."
if docker exec users_api_web test -d /var/www/html/system; then
    echo "✅ Pasta system encontrada!"
    docker exec users_api_web ls -la /var/www/html/system/
else
    echo "❌ ERRO: Pasta system não encontrada!"
    echo "📋 Conteúdo atual:"
    docker exec users_api_web ls -la /var/www/html/
    exit 1
fi

# Verificar status dos containers
echo "📊 Status dos containers:"
docker-compose ps

# Aguardar banco de dados estar pronto
echo "🗄️ Aguardando banco de dados estar pronto..."
until docker-compose exec -T db mysqladmin ping -h"localhost" --silent; do
    echo "Aguardando MySQL..."
    sleep 2
done

# Testar a aplicação
echo "🧪 Testando aplicação..."
sleep 5

echo "✅ Setup concluído!"
echo ""
echo "🌐 Serviços disponíveis:"
echo "   - API: http://localhost:8080"
echo "   - PHPMyAdmin: http://localhost:8081"
echo ""
echo "🧪 Teste a API:"
echo "   curl http://localhost:8080"
echo "   curl http://localhost:8080/api/health"
echo ""
echo "📝 Criar usuário:"
echo '   curl -X POST http://localhost:8080/api/users \'
echo '     -H "Content-Type: application/json" \'
echo '     -d '"'"'{"name":"João Silva","email":"joao@email.com","password":"123456"}'"'"
echo ""
echo "📋 Ver logs:"
echo "   docker-compose logs -f web"