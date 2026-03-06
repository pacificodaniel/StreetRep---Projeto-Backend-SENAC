<?php
session_start();
include 'conn.php';

header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Usuário não logado']);
    exit;
}

$nome = $_POST['nomeCompleto'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$email = $_POST['emailConfirm'] ?? '';
$rede = $_POST['redeSocial'] ?? '';
$usuario_id = $_SESSION['usuario_id'];


if(!$nome || !$cpf || !$email){
    echo json_encode(['sucesso'=>false,'mensagem'=>'Campos obrigatórios faltando']);
    exit;
}

// Inserir na tabela de verificações
$stmt = $conn->prepare("INSERT INTO requisicoes_verificacao (usuario_id, nome_completo, cpf, email, rede_social, status) VALUES (?, ?, ?, ?, ?, 'PENDENTE')");
$stmt->bind_param("issss", $usuario_id, $nome, $cpf, $email, $rede);

if($stmt->execute()){
    echo json_encode(['sucesso'=>true]);
} else {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Erro ao salvar no banco']);
}
?>
