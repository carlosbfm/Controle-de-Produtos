<?php
require_once 'conexao.php';

$mensagem = "";
$tipoAlerta = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['price']; 

    try {
        $sql = "INSERT INTO produtos (descricao, quantidade, preco) VALUES (:descricao, :quantidade, :preco)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':preco', $preco);
        $stmt->execute();

        $mensagem = "<strong>Sucesso!</strong> O produto <b>$descricao</b> foi cadastrado no sistema!";
        $tipoAlerta = "sucesso";

    } catch(PDOException $e) {
        $mensagem = "<strong>Erro ao salvar:</strong> " . $e->getMessage();
        $tipoAlerta = "erro";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar produto</title>
    <link rel="stylesheet" href="/css/adicionar.css">
</head>
<body>
    <header>CADASTRE UM NOVO PRODUTO</header>   

    <?php if(!empty($mensagem)): ?>
        <div class="alerta <?= $tipoAlerta ?>">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="descricao">Nome do produto</label>
        <input type="text" placeholder="Ex: COCA ZERO" id="descricao" name="descricao" required>
        
        <label for="quantidade">Quantidade</label>
        <input type="number" name="quantidade" id="quantidade" placeholder="Ex: 5" required>
        
        <label for="price">Preço do produto</label>
        <input type="number" name="price" step="0.01" min="0" placeholder="Ex: 99.90" required>
        
        <button type="submit">Adicionar</button>
    </form>
    
    <a href="estoque.php" class="btn-voltar">Voltar</a>
</body>
</html>