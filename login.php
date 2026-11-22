<?php
session_start();
include "conexao.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conn, $sql);
    $usuario = mysqli_fetch_assoc($resultado);

    if ($usuario && password_verify($senha, $usuario["senha"])) {
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["usuario_nome"] = $usuario["nome"];
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Email ou senha incorretos.";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <p><?php echo $msg; ?></p>

    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Entrar">
    </form>

    <br>
    <a href="cadastro.php">Não tem uma conta? Cadastre-se</a>
</body>
</html>