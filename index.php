<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Inicializar variáveis
$nome = $sobrenome = $telefone = "";
$mensagem = "";

// Processar formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se é um cadastro individual
    if (isset($_POST['cadastrar'])) {
        // Limpar e obter dados do formulário
        $nome = limparDados($_POST['nome']);
        $sobrenome = limparDados($_POST['sobrenome']);
        $telefone = limparDados($_POST['telefone']);
        
        // Validar dados
        if (empty($nome) || empty($sobrenome) || empty($telefone)) {
            $mensagem = "<div class='alert alert-danger'>Todos os campos são obrigatórios!</div>";
        } else {
            // Conectar ao banco de dados
            $conexao = conectarBD();
            
            // Preparar e executar a query
            $stmt = $conexao->prepare("INSERT INTO contatos (nome, sobrenome, telefone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $sobrenome, $telefone);
            
            // Verificar se o cadastro foi bem-sucedido
            if ($stmt->execute()) {
                $mensagem = "<div class='alert alert-success'>Contato cadastrado com sucesso!</div>";
                // Limpar campos após cadastro bem-sucedido
                $nome = $sobrenome = $telefone = "";
            } else {
                // Verificar se é erro de duplicidade de telefone
                if ($conexao->errno == 1062) {
                    $mensagem = "<div class='alert alert-danger'>Este número de telefone já está cadastrado!</div>";
                } else {
                    $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . $stmt->error . "</div>";
                }
            }
            
            // Fechar statement e conexão
            $stmt->close();
            fecharConexao($conexao);
        }
    }
}
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
        <!-- Exibir mensagens de sucesso ou erro -->
        <?php echo $mensagem; ?>
        
        <div class="app-card card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-user-plus me-2"></i>Cadastrar Novo Contato</h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $nome; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sobrenome" class="form-label">Sobrenome</label>
                            <input type="text" class="form-control" id="sobrenome" name="sobrenome" value="<?php echo $sobrenome; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $telefone; ?>" placeholder="Ex: 11999998888" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="cadastrar" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cadastrar Contato
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mb-4">
            <a href="adicionar_em_massa.php" class="btn btn-success">
                <i class="fas fa-file-csv me-2"></i>Importar Contatos CSV
            </a>
            <a href="lista_contatos.php" class="btn btn-info">
                <i class="fas fa-list me-2"></i>Visualizar Lista
            </a>
        </div>
        
        <div class="text-center mt-4">
            <a href="lista_avancada.php" class="btn btn-outline-primary">
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
    <script>
        // Máscara para o campo de telefone
        document.getElementById('telefone').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
