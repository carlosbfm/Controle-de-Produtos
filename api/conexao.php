<?php

// ============================================
// 1. OBTÉM VARIÁVEIS DE AMBIENTE DA VERCEL
// ============================================
// Na Vercel, as variáveis são definidas no dashboard e acessadas via getenv()
$host    = getenv('DB_HOST');
$porta   = getenv('DB_PORT');
$banco   = getenv('DB_NAME');
$usuario = getenv('DB_USER');
$senha   = getenv('DB_PASSWORD');

// ============================================
// 2. VALIDAÇÃO DAS VARIÁVEIS
// ============================================
// Verifica se todas as variáveis foram configuradas na Vercel
if (!$host || !$porta || !$banco || !$usuario || !$senha) {
    die("❌ Erro fatal: Variáveis de ambiente não configuradas na Vercel. 
    Verifique: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD");
}

// ============================================
// 3. CONEXÃO COM SUPABASE
// ============================================
try {
   
    $dsn = "pgsql:host=$host;port=$porta;dbname=$banco;sslmode=require";
    
    $opcoes = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Conecta passando usuário e senha separadamente (mais seguro)
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);

    // ============================================
    // 4. CRIAÇÃO DA TABELA (se não existir)
    // ============================================
    $sqlCreate = "CREATE TABLE IF NOT EXISTS produtos (
        id SERIAL PRIMARY KEY,
        descricao VARCHAR(255) NOT NULL,
        quantidade INTEGER NOT NULL DEFAULT 0,
        preco NUMERIC(10,2) NOT NULL DEFAULT 0.00
    )";

    $pdo->exec($sqlCreate);
    
    // Conexão bem sucedida (comentado para não poluir a saída)
    // echo "✅ Conectado com sucesso ao Supabase!";

} catch(PDOException $e) {
    // Exibe erro detalhado
    die("❌ Erro de conexão com banco: " . $e->getMessage());
}
?>