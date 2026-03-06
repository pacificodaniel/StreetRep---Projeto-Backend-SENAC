<?php
session_start();
include 'conn.php';
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'ADMIN'){
    echo json_encode(['sucesso'=>false,'mensagem'=>'Acesso negado']);
    exit;
}

// Pega todas requisições pendentes
$result = $conn->query("SELECT id, usuario_id FROM requisicoes_verificacao WHERE status='PENDENTE'");
while($row = $result->fetch_assoc()){
    $stmt = $conn->prepare("UPDATE requisicoes_verificacao SET status='APROVADO' WHERE id=?");
    $stmt->bind_param("i",$row['id']);
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE usuarios SET verificado=1 WHERE id=?");
    $stmt2->bind_param("i",$row['usuario_id']);
    $stmt2->execute();
}

echo json_encode(['sucesso'=>true]);
