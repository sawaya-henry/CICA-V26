<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Inicializar variáveis
$mensagem = "";
$contatos = [];
$pesquisa = "";
$termo_pesquisa = "";

// Verificar se há pesquisa
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {
    $pesquisa = limparDados($_GET['pesquisa']);
    $termo_pesquisa = $pesquisa;
}

// Conectar ao banco de dados
$conexao = conectarBD();

// Construir query baseada na pesquisa
if (!empty($pesquisa)) {
    $sql = "SELECT id, nome, sobrenome, telefone FROM contatos 
            WHERE nome LIKE ? OR sobrenome LIKE ? OR telefone LIKE ? 
            ORDER BY nome, sobrenome";
    $stmt = $conexao->prepare($sql);
    
    if ($stmt) {
        $termo_like = "%" . $pesquisa . "%";
        $stmt->bind_param("sss", $termo_like, $termo_like, $termo_like);
        
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            
            // Verificar se há contatos
            if ($resultado->num_rows > 0) {
                // Armazenar contatos em um array
                while ($row = $resultado->fetch_assoc()) {
                    $contatos[] = $row;
                }
            } else {
                $mensagem = "<div class='alert alert-warning'>Nenhum contato encontrado para '{$pesquisa}'.</div>";
            }
            
            $stmt->close();
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao executar consulta: " . $stmt->error . "</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao preparar consulta: " . $conexao->error . "</div>";
    }
} else {
    $sql = "SELECT id, nome, sobrenome, telefone FROM contatos ORDER BY nome, sobrenome";
    $resultado = $conexao->query($sql);
    
    if ($resultado) {
        // Verificar se há contatos
        if ($resultado->num_rows > 0) {
            // Armazenar contatos em um array
            while ($row = $resultado->fetch_assoc()) {
                $contatos[] = $row;
            }
        } else {
            $mensagem = "<div class='alert alert-info'>Nenhum contato cadastrado.</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro na consulta: " . $conexao->error . "</div>";
    }
}

// Fechar conexão
fecharConexao($conexao);
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
        
        <div class="app-card card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-address-book me-2"></i>Contatos Cadastrados</h5>
                <a href="index.php" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
            
            <!-- Barra de pesquisa -->
            <div class="card-header bg-light">
                <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-2">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" name="pesquisa" 
                                   value="<?php echo htmlspecialchars($termo_pesquisa); ?>" 
                                   placeholder="Pesquisar por nome, sobrenome ou telefone...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <?php if (!empty($termo_pesquisa)): ?>
                                <a href="lista_contatos.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-end">
                            <span class="badge bg-info align-self-center">
                                <i class="fas fa-users me-1"></i> <?php echo count($contatos); ?> contato(s)
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card-body">
                <?php if (count($contatos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sobrenome</th>
                                    <th>Telefone</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contatos as $contato): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($contato['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($contato['sobrenome']); ?></td>
                                        <td><?php echo htmlspecialchars($contato['telefone']); ?></td>
                                        <td>
                                            <a href="detalhes.php?telefone=<?php echo urlencode($contato['telefone']); ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye me-1"></i>Visualizar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (empty($pesquisa) && empty($mensagem)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="lead">Nenhum contato cadastrado ainda.</p>
                        <a href="index.php" class="btn btn-primary mt-2">
                            <i class="fas fa-user-plus me-2"></i>Adicionar Contato
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
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
            <p>Henry Sawaya &copy; <?php echo date('Y'); ?> - Todos os direitos reservados</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>