<?php
// Arquivo de conexão com o banco de dados
require_once 'config.php';

// Função para conectar ao banco de dados
function conectarBD() {
    $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verifica se houve erro na conexão
    if ($conexao->connect_error) {
        die("Falha na conexão: " . $conexao->connect_error);
    }
    
    // Define o charset como utf8mb4
    $conexao->set_charset("utf8mb4");
    
    return $conexao;
}

// Função para fechar a conexão
function fecharConexao($conexao) {
    $conexao->close();
}

// Função para limpar dados de entrada
function limparDados($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}
?>
