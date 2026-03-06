<?php
include "conn.php";

// === Segurança de sessão ===
$secureFlag = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $secureFlag,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// === Funções CSRF ===
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

$mensagem = "";
$mensagem_tipo = "warning";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $token = $_POST["csrf_token"] ?? "";

    // Validação básica
    if (!csrf_verify($token)) {
        $mensagem = "Requisição inválida (CSRF). Atualize a página e tente novamente.";
        $mensagem_tipo = "danger";
    } elseif ($nome === "" || $email === "" || $senha === "") {
        $mensagem = "Preencha todos os campos.";
        $mensagem_tipo = "warning";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
        $mensagem_tipo = "warning";
    } else {
        // Verifica se já existe usuário com o mesmo e-mail
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res && $res->num_rows > 0) {
            $mensagem = "Já existe um usuário com esse e-mail.";
            $mensagem_tipo = "warning";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, verificado) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sss", $nome, $email, $hash);

            if ($stmt->execute()) {
                $mensagem = "Cadastro realizado com sucesso! Você já pode fazer login.";
                $mensagem_tipo = "success";
            } else {
                $mensagem = "Erro ao cadastrar. Tente novamente mais tarde.";
                $mensagem_tipo = "danger";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Cadastro - StreetRep</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/login.css">

  <style>
    body { background: #f4f4f4; }
    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .login-card {
      background: #000;
      color: #fff;
      border-radius: .6rem;
      padding: 2rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      max-width: 420px;
      width: 100%;
    }
    .login-card .form-control {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.08);
      color: #fff;
    }
    .login-card label { color: rgba(255,255,255,0.8); }
    .login-card .btn-warning { width: 100%; }
    .small-muted { color: rgba(255,255,255,0.6); font-size: .9rem; }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="logo_box text-center">
       <a href="index.php">  <img src="./img/logo_png_4.png" alt="Logo" height="170px" width="200px" id="streetrep_logo">   </a>
      </div>
      <h3 class="mb-3">Cadastro</h3>

      <?php if ($mensagem): ?>
        <?php
          $alertClass = 'alert-warning';
          if ($mensagem_tipo === 'danger') $alertClass = 'alert-danger';
          if ($mensagem_tipo === 'success') $alertClass = 'alert-success';
        ?>
        <div class="alert <?= $alertClass ?> py-2" role="alert">
          <?= $mensagem ?>
        </div>
      <?php endif; ?>

      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        
        <div class="mb-3">
          <label for="nome" class="form-label">Nome de Usuário</label>
          <input id="nome" name="nome" type="text" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Digite seu E-mail</label>
          <input id="email" name="email" type="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label">Escolha uma senha</label>
          <input id="senha" name="senha" type="password" class="form-control" required>
        </div>

        <div class="d-grid gap-3 mb-2">
          <button class="btn btn-warning" type="submit">Cadastrar</button>
          <a href="login.php" class="btn btn-outline-warning">Voltar ao Login</a>
        </div>

        <p class="small-muted mb-0 text-center">Já tem conta? Faça login.</p>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
