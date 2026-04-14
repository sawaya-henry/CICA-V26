<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Inicializar variáveis
$mensagem = "";
$contato = null;

// Verificar se o ID foi fornecido
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Conectar ao banco de dados
    $conexao = conectarBD();
    
    // Processar formulário quando enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar'])) {
        // Limpar e obter dados do formulário
        $nome = limparDados($_POST['nome']);
        $sobrenome = limparDados($_POST['sobrenome']);
        $telefone = limparDados($_POST['telefone']);
        
        // Validar dados
        if (empty($nome) || empty($sobrenome) || empty($telefone)) {
            $mensagem = "<div class='alert alert-danger'>Todos os campos são obrigatórios!</div>";
        } else {
            // Verificar se o telefone já existe (exceto para o contato atual)
            $stmt = $conexao->prepare("SELECT id FROM contatos WHERE telefone = ? AND id != ?");
            $stmt->bind_param("si", $telefone, $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            if ($resultado->num_rows > 0) {
                $mensagem = "<div class='alert alert-danger'>Este número de telefone já está cadastrado para outro contato!</div>";
            } else {
                // Preparar e executar a query de atualização
                $stmt = $conexao->prepare("UPDATE contatos SET nome = ?, sobrenome = ?, telefone = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nome, $sobrenome, $telefone, $id);
                
                // Verificar se a atualização foi bem-sucedida
                if ($stmt->execute()) {
                    $mensagem = "<div class='alert alert-success'>Contato atualizado com sucesso!</div>";
                } else {
                    $mensagem = "<div class='alert alert-danger'>Erro ao atualizar contato: " . $stmt->error . "</div>";
                }
            }
            $stmt->close();
        }
    }
    
    // Buscar dados do contato
    $stmt = $conexao->prepare("SELECT id, nome, sobrenome, telefone FROM contatos WHERE id = ?");
    $stmt->bind_param("i", $id);
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
    $mensagem = "<div class='alert alert-danger'>ID não especificado!</div>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contato - ContatosPRO</title>
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
            <p>Sistema completo para gerenciamento de contatos</p>
        </div>
    </header>
    
    <div class="app-container">
        <!-- Exibir mensagens -->
        <?php echo $mensagem; ?>
        
        <?php if ($contato): ?>
            <div class="app-card card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-pencil-alt me-2"></i>Editar Contato</h5>
                    <a href="lista_avancada.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $contato['id']); ?>">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($contato['nome']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="sobrenome" class="form-label">Sobrenome</label>
                            <input type="text" class="form-control" id="sobrenome" name="sobrenome" value="<?php echo htmlspecialchars($contato['sobrenome']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($contato['telefone']); ?>" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="atualizar" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Atualizar Contato
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="lead">Não foi possível encontrar o contato para edição.</p>
                <a href="lista_avancada.php" class="btn btn-primary mt-3">
                    <i class="fas fa-list me-2"></i>Voltar para Lista de Contatos
                </a>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="lista_avancada.php" class="btn btn-outline-primary">
                <i class="fas fa-cogs me-2"></i>Lista Avançada
            </a>
            <a href="index.php" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-home me-2"></i>Página Principal
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
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('telefone').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                e.target.value = value;
            });
        });
    </script>
</body>
</html>
