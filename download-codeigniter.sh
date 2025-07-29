#!/bin/bash

echo "📥 Baixando CodeIgniter 3 completo..."

# Criar diretório temporário
mkdir -p temp_ci

# Baixar CodeIgniter 3.1.13
cd temp_ci
wget https://github.com/bcit-ci/CodeIgniter/archive/refs/tags/3.1.13.zip -O codeigniter.zip

# Extrair
unzip codeigniter.zip

# Copiar arquivos para src (mantendo nossa customização)
cd ..
cp -r temp_ci/CodeIgniter-3.1.13/system src/
cp temp_ci/CodeIgniter-3.1.13/index.php src/index.php

# Verificar se copiou corretamente
if [ -d "src/system" ]; then
    echo "✅ Pasta system copiada com sucesso!"
    ls -la src/
else
    echo "❌ Erro ao copiar pasta system"
    exit 1
fi

# Limpar arquivos temporários
rm -rf temp_ci

echo "✅ CodeIgniter 3 baixado e configurado!"
echo "🚀 Agora execute: ./setup.sh"