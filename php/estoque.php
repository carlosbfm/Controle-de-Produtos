<?php
require_once 'conexao.php';

// --- LÓGICA DE EXCLUSÃO ---
if (isset($_GET['excluir_id'])) {
    $idParaExcluir = $_GET['excluir_id'];
    try {
        $sqlDelete = "DELETE FROM produtos WHERE id = :id";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindValue(':id', $idParaExcluir);
        $stmtDelete->execute();
        header("Location: estoque.php"); 
        exit;
    } catch(PDOException $e) {
        die("Erro ao excluir: " . $e->getMessage());
    }
}

// --- LÓGICA DE BUSCA E PAGINAÇÃO ---
$busca = $_GET['busca'] ?? '';
$limite = 5; 
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaAtual < 1) $paginaAtual = 1;

$offset = ($paginaAtual - 1) * $limite; // Calcula quantos itens pular

try {
    // 1. Descobrir o total de registros (para calcular o total de páginas)
    if (!empty($busca)) {
        $sqlTotal = "SELECT COUNT(*) FROM produtos WHERE descricao ILIKE :busca";
        $stmtTotal = $pdo->prepare($sqlTotal);
        $stmtTotal->bindValue(':busca', '%' . $busca . '%');
        $stmtTotal->execute();
    } else {
        $sqlTotal = "SELECT COUNT(*) FROM produtos";
        $stmtTotal = $pdo->query($sqlTotal);
    }
    $totalRegistros = $stmtTotal->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $limite); // Arredonda pra cima

    // 2. Buscar apenas os itens da página atual
    if (!empty($busca)) {
        $sql = "SELECT * FROM produtos WHERE descricao ILIKE :busca ORDER BY id DESC LIMIT :limite OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca', '%' . $busca . '%');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "SELECT * FROM produtos ORDER BY id DESC LIMIT :limite OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $listaProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Produto</title>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="/css/estoque_style.css">
</head>
<body>
    <header>
        <h1>CADASTRO DE PRODUTO</h1>
        <nav>
            <a href="estoque.php" style="color: white; text-decoration: none;"><i class="fi fi-rr-home"></i></a>
        </nav>
    </header>

    <main>
        <div>
            <form action="estoque.php" method="GET" style="display: inline-block;">
                <i class="fi fi-rr-search"></i>
                <input type="text" name="busca" placeholder="DIGITE PARA BUSCAR..." 
                       value="<?= htmlspecialchars($busca) ?>" 
                       oninput="this.form.submit()" 
                       onfocus="var val=this.value; this.value=''; this.value=val;" 
                       autofocus>
                
                <input type="hidden" name="pagina" value="1"> 
                
                <?php if (!empty($busca)): ?>
                    <a href="estoque.php" style="color: red; font-size: 12px; margin-left: 10px; text-decoration: none;">Limpar</a>
                <?php endif; ?>
            </form>
            
            <a href="adicionar.php" style="margin-left: 20px;">
                NOVO <i class="fi fi-rr-plus"></i>
            </a> 
        </div>

        <table border="1" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Preço (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($listaProdutos) > 0): ?>
                    <?php foreach ($listaProdutos as $produto): ?>
                        <tr>
                            <td><?= $produto['id'] ?></td>
                            <td><?= $produto['descricao'] ?></td>
                            <td><?= $produto['quantidade'] ?></td>
                            <td><?= number_format($produto['preco'], 2, ',', '.') ?></td>
                            <td>
                                <a href="editar.php?id=<?= $produto['id'] ?>">
                                    <i class="fi fi-rr-document"></i>
                                </a> 
                                | 
                                <a href="?excluir_id=<?= $produto['id'] ?>" onclick="return confirm('Excluir <?= $produto['descricao'] ?>?');">
                                    <i class="fi fi-rr-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhum item encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPaginas > 1): ?>
            <div class="paginacao">
                <?php 
                // Preserva a busca na URL ao mudar de página
                $linkBusca = !empty($busca) ? "&busca=" . urlencode($busca) : "";
                ?>

                <?php if ($paginaAtual > 1): ?>
                    <a href="?pagina=<?= $paginaAtual - 1 ?><?= $linkBusca ?>">&laquo; Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?><?= $linkBusca ?>" class="<?= ($i == $paginaAtual) ? 'ativo' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a href="?pagina=<?= $paginaAtual + 1 ?><?= $linkBusca ?>">Próximo &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </main>
</body>
</html>