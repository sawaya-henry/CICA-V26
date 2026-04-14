<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Inicializar variáveis
$mensagem = "";
$sucessos = 0;
$falhas = 0;
$erros = [];

// Processar formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['importar'])) {
    // Verificar se o arquivo foi enviado sem erros
    if (isset($_FILES["arquivo_csv"]) && $_FILES["arquivo_csv"]["error"] == 0) {
        $arquivo_tmp = $_FILES["arquivo_csv"]["tmp_name"];
        $nome_arquivo = basename($_FILES["arquivo_csv"]["name"]);
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        
        // Verificar se é um arquivo CSV
        if ($extensao == "csv") {
            // Abrir o arquivo para leitura
            if (($handle = fopen($arquivo_tmp, "r")) !== FALSE) {
                // Conectar ao banco de dados
                $conexao = conectarBD();
                
                // Preparar a query para inserção
                $stmt = $conexao->prepare("INSERT INTO contatos (nome, sobrenome, telefone) VALUES (?, ?, ?)");
                
                // Pular a primeira linha se for cabeçalho
                if (isset($_POST['tem_cabecalho']) && $_POST['tem_cabecalho'] == 1) {
                    fgetcsv($handle, 1000, ",");
                }
                
                // Processar cada linha do CSV
                while (($dados = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Verificar se a linha tem pelo menos 3 colunas
                    if (count($dados) >= 3) {
                        $nome = limparDados($dados[0]);
                        $sobrenome = limparDados($dados[1]);
                        $telefone = limparDados($dados[2]);
                        
                        // Verificar se todos os campos estão preenchidos
                        if (!empty($nome) && !empty($sobrenome) && !empty($telefone)) {
                            // Remover caracteres não numéricos do telefone
                            $telefone = preg_replace("/[^0-9]/", "", $telefone);
                            
                            // Verificar se o telefone tem um formato válido
                            if (strlen($telefone) >= 8 && strlen($telefone) <= 11) {
                                $stmt->bind_param("sss", $nome, $sobrenome, $telefone);
                                
                                // Tentar inserir o contato
                                if ($stmt->execute()) {
                                    $sucessos++;
                                } else {
                                    // Log generic error if insertion fails (after removing UNIQUE constraint, this shouldn't be for duplicates)
                                    $erros[] = "Erro ao inserir: $nome $sobrenome ($telefone) - " . $stmt->error;
                                    $falhas++;
                                }
                            } else {
                                $erros[] = "Telefone inválido: $nome $sobrenome ($telefone)";
                                $falhas++;
                            }
                        } else {
                            $erros[] = "Campos obrigatórios faltando: " . implode(", ", $dados);
                            $falhas++;
                        }
                    } else {
                        $erros[] = "Formato de linha inválido: " . implode(", ", $dados);
                        $falhas++;
                    }
                }
                
                // Fechar o arquivo e a conexão
                fclose($handle);
                $stmt->close();
                fecharConexao($conexao);
                
                // Exibir mensagem de resultado
                if ($sucessos > 0) {
                    $mensagem = "<div class='alert alert-success'>$sucessos contato(s) importado(s) com sucesso!</div>";
                    if ($falhas > 0) {
                        $mensagem .= "<div class='alert alert-warning'>$falhas contato(s) não puderam ser importados. Veja os detalhes abaixo.</div>";
                    }
                } else {
                    $mensagem = "<div class='alert alert-danger'>Nenhum contato foi importado. Verifique o formato do arquivo e tente novamente.</div>";
                }
            } else {
                $mensagem = "<div class='alert alert-danger'>Não foi possível abrir o arquivo CSV.</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-danger'>Por favor, envie um arquivo CSV válido.</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro no upload do arquivo: " . $_FILES["arquivo_csv"]["error"] . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Contatos via CSV - ContatosPRO</title>
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
        <!-- Exibir mensagens de sucesso ou erro -->
        <?php echo $mensagem; ?>
        
        <!-- Exibir erros detalhados se houver -->
        <?php if (count($erros) > 0): ?>
            <div class="app-card card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Detalhes dos Erros</h5>
                </div>
                <div class="card-body">
                    <div class="error-list">
                        <ul>
                            <?php foreach ($erros as $erro): ?>
                                <li><?php echo htmlspecialchars($erro); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="app-card card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-file-csv me-2"></i>Importar Contatos via CSV</h5>
                <a href="index.php" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Instruções:</h6>
                    <ol>
                        <li>Crie uma planilha no Excel com três colunas: Nome, Sobrenome e Telefone</li>
                        <li>Preencha os dados dos contatos</li>
                        <li>Salve a planilha como arquivo CSV (Valores separados por vírgula)</li>
                        <li>Faça o upload do arquivo abaixo</li>
                        <li>Marque a opção "Arquivo tem cabeçalho" se a primeira linha contiver os títulos das colunas</li>
                    </ol>
                </div>
                
                <div class="csv-example">
                    <p><strong>Exemplo de formato CSV:</strong></p>
                    Nome,Sobrenome,Telefone<br>
                    João,Silva,11999998888<br>
                    Maria,Santos,21988887777<br>
                    Carlos,Oliveira,31977776666
                </div>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="arquivo_csv" class="form-label">Selecione o arquivo CSV:</label>
                        <input type="file" class="form-control" id="arquivo_csv" name="arquivo_csv" accept=".csv" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="tem_cabecalho" name="tem_cabecalho" value="1" checked>
                        <label class="form-check-label" for="tem_cabecalho">Arquivo tem cabeçalho (primeira linha contém títulos)</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="importar" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Importar Contatos
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="lista_contatos.php" class="btn btn-info">
                <i class="fas fa-list me-2"></i>Visualizar Lista
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
</body>
</html>
