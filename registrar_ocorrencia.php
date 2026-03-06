<?php
header('Content-Type: application/json; charset=utf-8');
require 'conn.php';
session_start();

$id_usuario = $_SESSION['usuario_id'] ?? null;

if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(['mensagem' => 'Você precisa estar logado.']);
    exit;
}

if (!($_SESSION['verificado'] ?? false)) {
    http_response_code(403);
    echo json_encode(['mensagem' => 'Usuário não verificado.']);
    exit;
}



// Dados do formulário
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$gravidade = trim($_POST['gravidade'] ?? '');
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;

// Validação básica
if (  $titulo === '' ||   $descricao === '' ||  $gravidade === '' ||  $lat === null ||    $lng === null) {

    http_response_code(400);
    echo json_encode(['mensagem' => 'Preencha todos os campos obrigatórios.']);
    exit;
   





}

// ---------- Upload de imagem (opcional) ----------
$caminhoImagem = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidas)) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Formato de imagem inválido.']);
        exit;
    }

    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    $nomeArquivo = uniqid('ocorrencia_', true) . '.' . $ext;
    $destino = 'uploads/' . $nomeArquivo;

    if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
        http_response_code(500);
        echo json_encode(['mensagem' => 'Erro ao salvar a imagem.']);
        exit;
    }

    $caminhoImagem = $destino;
}

// ---------- Insert ----------
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO ocorrencias 
     (id_usuario, titulo, descricao, gravidade, latitude, longitude, imagem) 
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "isssdds",
    $id_usuario,
    $titulo,
    $descricao,
    $gravidade,
    $lat,
    $lng,
    $caminhoImagem
);

$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    echo json_encode(['mensagem' => 'Ocorrência registrada com sucesso!']);
} else {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro ao registrar ocorrência.']);
}

mysqli_stmt_close($stmt);
