<?php
session_start();
include 'conn.php';
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'ADMIN'){
    echo json_encode(['sucesso'=>false,'mensagem'=>'Acesso negado']);
    exit;
}

$id = $_POST['id'] ?? 0;
$id = intval($id);

if(!$id){
    echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido']);
    exit;
}

// Pega o id do usuário da requisição
$stmt = $conn->prepare("SELECT usuario_id FROM requisicoes_verificacao WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if(!$res){
    echo json_encode(['sucesso'=>false,'mensagem'=>'Requisição não encontrada']);
    exit;
}

$usuario_id = $res['usuario_id'];

// Atualiza status da requisição
$stmt = $conn->prepare("UPDATE requisicoes_verificacao SET status='APROVADO' WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

// Atualiza usuário como verificado
$stmt = $conn->prepare("UPDATE usuarios SET verificado=1 WHERE id=?");
$stmt->bind_param("i",$usuario_id);
$stmt->execute();

echo json_encode(['sucesso'=>true]);
