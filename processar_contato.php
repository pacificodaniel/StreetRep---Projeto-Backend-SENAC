<?php
ini_set('opcache.enable', 0);
header('Content-Type: application/json; charset=utf-8');
session_start();
include 'conn.php';

// Espera POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$assunto = trim($_POST['assunto'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (empty($nome) || empty($email) || empty($assunto) || empty($mensagem)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Email inválido.']);
    exit;
}

// Normaliza tamanho
if (mb_strlen($assunto) > 200) $assunto = mb_substr($assunto, 0, 200);
if (mb_strlen($mensagem) > 5000) $mensagem = mb_substr($mensagem, 0, 5000);

// Cria tabela de logs se não existir
$createTableSql = "CREATE TABLE IF NOT EXISTS contato_envios (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    nome VARCHAR(255) DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    assunto VARCHAR(255) DEFAULT NULL,
    mensagem TEXT,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!mysqli_query($conn, $createTableSql)) {
    // não crítico, mas para segurança, continua? retornamos erro
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao preparar envio.']);
    exit;
}

// Regras de limite
$limite_email_segundos = 24 * 3600; // 24h
$limite_ip_count = 3; // max 3
$limite_ip_window = 3600; // por 1 hora

// 1) Verifica se já enviou por este email nas últimas 24h
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM contato_envios WHERE email = ? AND data_envio >= (NOW() - INTERVAL 24 HOUR)");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $countEmail);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($countEmail > 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Já existe um envio deste e-mail nas últimas 24 horas. Aguarde antes de enviar novamente.']);
    exit;
}

// 2) Verifica envios por IP na última hora
$stmt2 = mysqli_prepare($conn, "SELECT COUNT(*) FROM contato_envios WHERE ip = ? AND data_envio >= (NOW() - INTERVAL 1 HOUR)");
mysqli_stmt_bind_param($stmt2, 's', $ip);
mysqli_stmt_execute($stmt2);
mysqli_stmt_bind_result($stmt2, $countIp);
mysqli_stmt_fetch($stmt2);
mysqli_stmt_close($stmt2);

if ($countIp >= $limite_ip_count) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Muitas tentativas deste IP. Aguarde antes de tentar novamente.']);
    exit;
}

// Inserir registro
$stmtIns = mysqli_prepare($conn, "INSERT INTO contato_envios (email, nome, ip, assunto, mensagem) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmtIns, 'sssss', $email, $nome, $ip, $assunto, $mensagem);
$okIns = mysqli_stmt_execute($stmtIns);
mysqli_stmt_close($stmtIns);

if (!$okIns) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha ao registrar envio.']);
    exit;
}

// Buscar e-mails dos administradores
$adminEmails = [];
$res = mysqli_query($conn, "SELECT email FROM usuarios WHERE tipo_usuario = 'ADMIN' AND email IS NOT NULL AND email != ''");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $adminEmails[] = $row['email'];
    }
    mysqli_free_result($res);
}

if (empty($adminEmails)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum administrador encontrado para receber a mensagem.']);
    exit;
}

// Monta email
$subjectMail = "[Contato StreetRep] " . $assunto;
$body = "Mensagem enviada pelo site StreetRep\n\n";
$body .= "Nome: " . $nome . "\n";
$body .= "Email: " . $email . "\n";
$body .= "IP: " . $ip . "\n";
$body .= "Assunto: " . $assunto . "\n\n";
$body .= "Mensagem:\n" . $mensagem . "\n\n";
$body .= "Data/Hora: " . date('Y-m-d H:i:s') . "\n";

$headers = [];
$headers[] = 'From: ' . $nome . ' <' . $email . '>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset="utf-8"';
$headers_str = implode("\r\n", $headers);

// Enviar para cada admin (envio individual facilita deliverability e headers corretos)
$sendFailures = [];
foreach ($adminEmails as $to) {
    // Atenção: mail() pode não funcionar no ambiente local sem configuração (sendmail/SMTP).
    $ok = @mail($to, $subjectMail, $body, $headers_str);
    if (!$ok) $sendFailures[] = $to;
}

if (!empty($sendFailures)) {
    // Ainda assim consideramos o fluxo OK (mensagem salva) mas avisamos sobre falha no envio
    echo json_encode(['sucesso' => true, 'mensagem' => 'Mensagem registrada, porém houve falha ao enviar email para alguns administradores.']);
    exit;
}

echo json_encode(['sucesso' => true, 'mensagem' => 'Mensagem enviada com sucesso aos administradores. Obrigado!']);
exit;
