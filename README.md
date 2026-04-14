# Instruções para Instalação e Uso do Sistema ContatosPRO

## Requisitos
- XAMPP (Apache + MySQL)
- PHP 7.0 ou superior

## Instalação

1. **Configuração do Banco de Dados**
   - Inicie o XAMPP e certifique-se de que os serviços Apache e MySQL estão ativos
   - Acesse o phpMyAdmin (geralmente em http://localhost/phpmyadmin)
   - Importe o arquivo `setup_database.sql` para criar o banco de dados e a tabela necessária

2. **Configuração dos Arquivos**
   - Extraia todos os arquivos do ZIP para a pasta htdocs do seu XAMPP (ou uma subpasta)
   - Verifique e ajuste as configurações de conexão no arquivo `config.php` se necessário:
     - DB_HOST: geralmente "localhost"
     - DB_USER: geralmente "root"
     - DB_PASS: senha do MySQL (geralmente vazia no XAMPP padrão)
     - DB_NAME: "gerenciador_contatos"

3. **Acesso ao Sistema**
   - Abra seu navegador e acesse: http://localhost/[pasta-do-projeto]/index.php

## Estrutura do Sistema

O sistema é composto por 4 páginas principais:

1. **Página Principal (index.php)**
   - Formulário para cadastro de contatos individuais
   - Botões para acessar outras funcionalidades

2. **Importar Contatos CSV (adicionar_em_massa.php)**
   - Permite importar múltiplos contatos de uma vez através de um arquivo CSV
   - Suporta arquivos CSV gerados pelo Excel
   - Inclui instruções detalhadas para formatação do arquivo

3. **Lista de Contatos (lista_contatos.php)**
   - Exibe todos os contatos cadastrados
   - Permite visualizar detalhes de cada contato

4. **Lista Avançada (lista_avancada.php)**
   - Exibe todos os contatos com opções avançadas
   - Permite editar, visualizar detalhes e excluir contatos

5. **Detalhes do Contato (detalhes.php)**
   - Exibe informações detalhadas de um contato específico
   - Acessível através do telefone na URL

6. **Editar Contato (editar.php)**
   - Permite atualizar as informações de um contato existente

## Importação CSV

Para importar contatos via CSV:
1. Crie uma planilha no Excel com três colunas: Nome, Sobrenome e Telefone
2. Preencha os dados dos contatos
3. Salve a planilha como arquivo CSV (Valores separados por vírgula)
4. Acesse a página "Importar Contatos CSV"
5. Faça o upload do arquivo
6. Marque a opção "Arquivo tem cabeçalho" se a primeira linha contiver os títulos das colunas

## Novidades na Versão Atualizada

1. **Design Moderno e Profissional**
   - Interface completamente redesenhada com visual moderno e atraente
   - Paleta de cores harmoniosa e profissional
   - Tipografia aprimorada com a fonte Poppins

2. **Logo Personalizada**
   - Logo exclusiva "ContatosPRO" em todas as páginas
   - Identidade visual consistente em todo o sistema

3. **Melhorias de Usabilidade**
   - Ícones intuitivos para todas as ações
   - Botões com efeitos visuais de feedback
   - Mensagens de confirmação e alertas aprimorados

4. **Layout Responsivo**
   - Adaptação perfeita para dispositivos móveis e desktops
   - Experiência consistente em qualquer tamanho de tela

## Observações Importantes

- O sistema utiliza Bootstrap para o layout responsivo
- O campo de telefone aceita apenas números (máximo 11 dígitos)
- Para URLs amigáveis no formato `/detalhes/11999998888`, é necessário configurar o arquivo `.htaccess` no servidor Apache

## Estrutura de Arquivos

- `index.php` - Página principal com formulário de cadastro
- `adicionar_em_massa.php` - Página para importação de contatos via CSV
- `lista_contatos.php` - Página de listagem simples
- `lista_avancada.php` - Página de listagem com opções avançadas
- `detalhes.php` - Página de detalhes do contato
- `editar.php` - Página de edição de contato
- `conexao.php` - Script de conexão com o banco de dados
- `config.php` - Configurações do banco de dados
- `setup_database.sql` - Script SQL para criar o banco de dados
- `assets/` - Pasta com recursos visuais
  - `css/style.css` - Estilos personalizados
  - `img/logo.svg` - Logo do sistema
