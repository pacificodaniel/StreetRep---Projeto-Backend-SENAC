<?php
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    http_response_code(400);
    echo json_encode(["erro" => "Parâmetro de busca ausente"]);
    exit;
}

$q = urlencode($_GET['q']);

$url = "https://nominatim.openstreetmap.org/search?format=json&q={$q}&limit=1";

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: StreetRep/1.0 (streetrep@localhost)\r\n"
    ]
];

$context = stream_context_create($opts);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro ao consultar serviço de geolocalização"]);
    exit;
}

echo $response;
