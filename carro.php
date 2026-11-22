<?php
session_start();
include "conexao.php";

// proteção: só entra quem fez login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // dados do dono
    $nome     = mysqli_real_escape_string($conn, $_POST["nome"]);
    $cpf      = mysqli_real_escape_string($conn, $_POST["cpf"]);
    $telefone = mysqli_real_escape_string($conn, $_POST["telefone"]);

    // dados do carro
    $placa  = mysqli_real_escape_string($conn, $_POST["placa"]);
    $marca  = mysqli_real_escape_string($conn, $_POST["marca"]);
    $modelo = mysqli_real_escape_string($conn, $_POST["modelo"]);
    $ano    = (int) $_POST["ano"];

    // verifica se a placa já existe
    $sqlPlaca = "SELECT id FROM carros WHERE placa = '$placa'";
    $resPlaca = mysqli_query($conn, $sqlPlaca);

    if (mysqli_num_rows($resPlaca) > 0) {
        $msg = "Esta placa já está cadastrada.";
    } else {
        // verifica se o dono (CPF) já existe
        $sqlCpf = "SELECT id FROM clientes WHERE cpf = '$cpf'";
        $resCpf = mysqli_query($conn, $sqlCpf);

        if (mysqli_num_rows($resCpf) > 0) {
            // dono já existe: usa o id dele
            $cliente = mysqli_fetch_assoc($resCpf);
            $cliente_id = $cliente["id"];
        } else {
            // dono novo: cadastra e pega o id que acabou de ser criado
            $sqlCliente = "INSERT INTO clientes (nome, cpf, telefone)
                           VALUES ('$nome', '$cpf', '$telefone')";
            mysqli_query($conn, $sqlCliente);
            $cliente_id = mysqli_insert_id($conn);
        }

        // cadastra o carro ligado ao dono
        $sqlCarro = "INSERT INTO carros (placa, marca, modelo, ano, cliente_id)
                     VALUES ('$placa', '$marca', '$modelo', $ano, $cliente_id)";

        if (mysqli_query($conn, $sqlCarro)) {
            $msg = "Carro cadastrado com sucesso!";
        } else {
            $msg = "Erro ao cadastrar: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Carro</title>
</head>
<body>
    <h2>Cadastrar Carro</h2>
    <p>Olá, <?php echo $_SESSION["usuario_nome"]; ?> | <a href="sair.php">Sair</a></p>
    <p><?php echo $msg; ?></p>

    <form method="POST" action="">
        <h3>Dados do dono</h3>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="cpf">CPF (só números):</label>
        <input type="text" id="cpf" name="cpf" maxlength="11" required><br><br>

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone"><br><br>

        <h3>Dados do carro</h3>
        <label for="placa">Placa:</label>
        <input type="text" id="placa" name="placa" maxlength="8" required><br><br>

        <label for="marca">Marca:</label>
        <input type="text" id="marca" name="marca" required><br><br>

        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" required><br><br>

        <label for="ano">Ano:</label>
        <input type="number" id="ano" name="ano"><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>