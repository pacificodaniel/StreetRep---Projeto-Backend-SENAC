<?php
include "conn.php";
header('Content-Type: application/json');

$res = $conn->query("SELECT id, titulo, descricao, gravidade, latitude, longitude FROM ocorrencias");
$ocorrencias = [];
while ($row = $res->fetch_assoc()) {
    $ocorrencias[] = $row;
}

echo json_encode($ocorrencias);
?>