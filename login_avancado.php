<?php
session_start();

$senha_correta = "@23646"; // troque isso

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["senha"] === $senha_correta) {
        $_SESSION["acesso_avancado"] = true;
        header("Location: lista_avancada.php");
        exit;
    } else {
        $erro = "Senha incorreta!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/css/login.css">
<title>Acesso Avançado</title>
</head>
<body>

<div class="main-content">
    <div class="login-box">
        <div class="cadeado">🔒</div>
        <h2>Área Avançada</h2>

        <?php if (isset($erro)) echo "<div class='erro'>$erro</div>"; ?>

        <form method="post">
            <div class="input-group">
                <label>Senha de Acesso</label>
                <input type="password" name="senha" placeholder="Digite a senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>

        <a href="index.php" class="btn-voltar">Voltar</a>
    </div>
</div>

<!-- Rodapé -->
<footer class="app-footer">
    <div class="container">
        <p>Henry Sawaya &copy; <?php echo date('Y'); ?> - Todos os direitos reservados V26.02</p>
    </div>
</footer>

</body>
</html>