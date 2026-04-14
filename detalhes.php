<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Inicializar variáveis
$mensagem = "";
$contato = null;

// Verificar se o telefone foi fornecido na URL
if (isset($_GET['telefone']) && !empty($_GET['telefone'])) {
    $telefone = limparDados($_GET['telefone']);
    
    // Conectar ao banco de dados
    $conexao = conectarBD();
    
    // Buscar contato pelo telefone
    $stmt = $conexao->prepare("SELECT id, nome, sobrenome, telefone FROM contatos WHERE telefone = ?");
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Verificar se o contato foi encontrado
    if ($resultado->num_rows > 0) {
        $contato = $resultado->fetch_assoc();
    } else {
        $mensagem = "<div class='alert alert-danger'>Contato não encontrado!</div>";
    }
    
    // Fechar statement e conexão
    $stmt->close();
    fecharConexao($conexao);
} else {
    $mensagem = "<div class='alert alert-danger'>Telefone não especificado!</div>";
}

// Configurar o .htaccess para URLs amigáveis (isso não afeta o funcionamento atual)
// Criar arquivo .htaccess se necessário
$htaccess = <<<EOT
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^detalhes/([0-9]+)$ detalhes.php?telefone=$1 [L]
</IfModule>
EOT;

// Não vamos escrever o arquivo .htaccess aqui, apenas mostrar como seria
// file_put_contents('.htaccess', $htaccess);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CICA - Contatos Integrados Condominio Aruã</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Cabeçalho com logo -->
    <header class="app-header">
        <div class="container">
            <div class="logo-container">
                <img src="assets\img\LogoPadrão-750px.png" alt="ContatosPRO Logo">
            </div>
            <p>CICA - Contatos Integrados Condominio Aruã</p>
        </div>
    </header>
    
    <div class="app-container">
        <!-- Exibir mensagens -->
        <?php echo $mensagem; ?>
        
        <?php if ($contato): ?>
            <div class="app-card card mb-4 detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($contato['nome'] . ' ' . $contato['sobrenome']); ?></h5>
                    <a href="lista_contatos.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar para Lista
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 detail-label">Nome:</div>
                        <div class="col-md-8 detail-value"><?php echo htmlspecialchars($contato['nome']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 detail-label">Sobrenome:</div>
                        <div class="col-md-8 detail-value"><?php echo htmlspecialchars($contato['sobrenome']); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 detail-label">Telefone:</div>
                        <div class="col-md-8 detail-value">
                            <i class="fas fa-phone me-2 text-primary"></i><?php echo htmlspecialchars($contato['telefone']); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="lead">Não foi possível encontrar o contato solicitado.</p>
                <a href="lista_contatos.php" class="btn btn-primary mt-3">
                    <i class="fas fa-list me-2"></i>Voltar para Lista de Contatos
                </a>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-outline-primary">
                <i class="fas fa-home me-2"></i>Página Principal
            </a>
            <a href="lista_avancada.php" class="btn btn-outline-info ms-2">
                <i class="fas fa-cogs me-2"></i>Lista Avançada
            </a>
        </div>
    </div>
    
    <!-- Rodapé -->
    <footer class="app-footer">
        <div class="container">
            <p>Henry Sawaya &copy; <?php echo date('Y'); ?> - Todos os direitos reservados V26.02</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
