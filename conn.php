<?php
// conn.php - conexão MySQLi
$conn = mysqli_connect("localhost", "root", "", "streetrep");
if (!$conn) {
    // Em ambiente de produção você trataria isso de forma diferente.
    die('Erro de conexão: ' . mysqli_connect_error());
}
// Não feche a tag PHP
