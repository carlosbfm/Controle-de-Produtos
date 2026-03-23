<?php
// 1. Localiza o arquivo .env (ajustado para uma pasta antes, conforme seu código)
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die("❌ Erro fatal: O arquivo .env não foi encontrado em: " . realpath(__DIR__ . '/..'));
}

// 2. Lê o arquivo .env e carrega as variáveis
$linhas = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];

foreach ($linhas as $linha) {
    $linha = trim($linha);
    // Ignora comentários e linhas inválidas
    if (strpos($linha, '#') === 0 || strpos($linha, '=') === false) continue; 
    
    list($chave, $valor) = explode('=', $linha, 2);
    $env[trim($chave)] = trim($valor);
}

// 3. Atribui as variáveis (ajustado para os nomes do seu .env)
$host    = $env['DB_HOST'] ?? die("❌ Faltou o DB_HOST no .env");
$porta   = $env['DB_PORT'] ?? die("❌ Faltou o DB_PORT no .env");
$banco   = $env['DB_NAME'] ?? die("❌ Faltou o DB_NAME no .env");
$usuario = $env['DB_USER'] ?? die("❌ Faltou o DB_USER no .env");
$senha   = $env['DB_PASS'] ?? die("❌ Faltou o DB_PASS no .env");

try {
    /**
     * ⚠️ O SEGREDO PARA O SUPABASE:
     * Colocamos user e password dentro do DSN para evitar o erro "Tenant not found".
     * A porta deve ser 6543 e o sslmode deve ser require.
     */
    $dsn = "pgsql:host=$host;port=$porta;dbname=$banco;user=$usuario;password=$senha;sslmode=require";
    
    $opcoes = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Conecta passando os dados via DSN
    $pdo = new PDO($dsn, null, null, $opcoes);

    // 4. Criação da tabela (PostgreSQL usa SERIAL para auto-incremento)
    $sqlCreate = "CREATE TABLE IF NOT EXISTS produtos (
        id SERIAL PRIMARY KEY,
        descricao VARCHAR(255) NOT NULL,
        quantidade INTEGER NOT NULL DEFAULT 0,
        preco NUMERIC(10,2) NOT NULL DEFAULT 0.00
    )";

    $pdo->exec($sqlCreate);
    
    // Se chegar aqui, funcionou! (Mantenha comentado em produção)
    // echo "✅ Conectado com sucesso ao Supabase!";

} catch(PDOException $e) {
    // Se ainda der "Tenant not found", mude no .env o @ por ponto (.) no usuário
    die("❌ Erro de conexão com banco: " . $e->getMessage());
}
?>