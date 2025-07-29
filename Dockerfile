FROM php:8.1-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    wget \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite e headers do Apache
RUN a2enmod rewrite headers

# Configurar Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Definir diretório de trabalho
WORKDIR /var/www/html

# Limpar diretório
RUN rm -rf /var/www/html/*

# Baixar CodeIgniter 3 completo
RUN wget https://github.com/bcit-ci/CodeIgniter/archive/refs/tags/3.1.13.zip -O codeigniter.zip \
    && unzip codeigniter.zip \
    && cp -r CodeIgniter-3.1.13/* . \
    && cp -r CodeIgniter-3.1.13/.* . 2>/dev/null || true \
    && rm -rf CodeIgniter-3.1.13 codeigniter.zip

# Verificar se a pasta system existe
RUN ls -la /var/www/html/ && \
    if [ ! -d "/var/www/html/system" ]; then \
        echo "ERRO: Pasta system não encontrada!" && exit 1; \
    fi

# Copiar arquivos customizados da aplicação (sobrescrever)
COPY src/ /var/www/html/

# Criar diretórios necessários e definir permissões
RUN mkdir -p application/logs application/cache \
    && chmod 777 application/logs application/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Verificar estrutura final
RUN echo "=== Estrutura final ===" && ls -la /var/www/html/

# Expor porta 80
EXPOSE 80