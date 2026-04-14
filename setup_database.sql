-- Script SQL para criar o banco de dados e a tabela de contatos

-- Cria o banco de dados (se não existir)
CREATE DATABASE IF NOT EXISTS gerenciador_contatos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados para uso
USE gerenciador_contatos;

-- Cria a tabela de contatos
CREATE TABLE IF NOT EXISTS contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL UNIQUE, -- Telefone como chave única para busca e URL
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemplo de inserção (opcional, para teste)
-- INSERT INTO contatos (nome, sobrenome, telefone) VALUES ('Fulano', 'De Tal', '11999998888');

