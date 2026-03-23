<?php
require_once 'conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: estoque.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: estoque.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['price'];

    try {
        $sql = "UPDATE produtos SET descricao = :d, quantidade = :q, preco = :p WHERE id = :id";
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->bindValue(':d', $descricao);
        $stmtUpdate->bindValue(':q', $quantidade);
        $stmtUpdate->bindValue(':p', $preco);
        $stmtUpdate->bindValue(':id', $id);
        $stmtUpdate->execute();

        header("Location: estoque.php");
        exit;
    } catch(PDOException $e) {
        die("Erro ao atualizar: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/editar.css">
</head>
<body>
    <header>EDITAR PRODUTO</header>

    <form action="" method="POST">
        <label>Nome do produto</label>
        <input type="text" name="descricao" value="<?= htmlspecialchars($produto['descricao']) ?>" required>

        <label>Quantidade</label>
        <input type="number" name="quantidade" value="<?= $produto['quantidade'] ?>" required>

        <label>Preço</label>
        <input type="number" name="price" step="0.01" value="<?= $produto['preco'] ?>" required>

        <button type="submit">Salvar Alterações</button>
    </form>

    <a href="estoque.php" class="btn-voltar">Cancelar</a>
</body>
</html>