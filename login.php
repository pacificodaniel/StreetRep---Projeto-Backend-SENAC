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

// Mensagens
$mensagem = '';
$mensagem_tipo = 'warning';

// Função CSRF simples
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Criar usuário de teste via GET
if (isset($_GET['create_test']) && $_GET['create_test'] === '1') {
    $test_email = 'teste@teste.com';
    $test_pass = '123';
    $test_name = 'Usuário Teste';
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $test_email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $mensagem = "Usuário de teste já existe: $test_email";
        $mensagem_tipo = 'warning';
    } else {
        $hash = password_hash($test_pass, PASSWORD_DEFAULT);
        $ins = $conn->prepare("INSERT INTO usuarios (nome, email, senha, verificado, tipo) VALUES (?, ?, ?, 1, 'ADMIN')");
        $ins->bind_param("sss", $test_name, $test_email, $hash);
        if ($ins->execute()) {
            $mensagem = "Usuário de teste criado: <strong>$test_email</strong> / senha: <strong>$test_pass</strong>. Remova ?create_test=1 depois.";
            $mensagem_tipo = 'success';
        } else {
            $mensagem = "Falha ao criar usuário de teste.";
            $mensagem_tipo = 'danger';
        }
        $ins->close();
    }
    $stmt->close();
}

// Proteção brute-force
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
$MAX_ATTEMPTS = 6;
$LOCK_TIME = 0; // 5 minutos = 300
if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
    $mensagem = "Muitas tentativas. Tente novamente mais tarde.";
    $mensagem_tipo = 'danger';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha_raw = $_POST['senha'] ?? '';
        $token = $_POST['csrf_token'] ?? '';

        if (!csrf_verify($token)) {
            $mensagem = "Requisição inválida (CSRF). Atualize a página e tente novamente.";
            $mensagem_tipo = 'danger';
        } elseif ($email === '' || $senha_raw === '') {
            $mensagem = "Por favor informe e-mail e senha.";
            $mensagem_tipo = 'warning';
        } else {
            $stmt = $conn->prepare("SELECT id, senha, nome, verificado, tipo_usuario FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if (password_verify($senha_raw, $row['senha'])) {
                  session_regenerate_id(true);
                  $_SESSION['usuario_id'] = $row['id'];
                  $_SESSION['usuario_email'] = $email;
                  $_SESSION['usuario_nome'] = $row['nome'];
                  $_SESSION['tipo_usuario'] = $row['tipo_usuario']; // ADMIN, USUARIO, etc.
                  $_SESSION['verificado'] = (bool)$row['verificado']; // ✅ adiciona aqui
                  // reset attempts
                  $_SESSION['login_attempts'] = 0;
                  unset($_SESSION['login_locked_until']);
                  header('Location: page1.php');
                  exit();
              }
               else {
                    $_SESSION['login_attempts']++;
                    $mensagem = ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) ?
                        "Muitas tentativas incorretas. Bloqueado." :
                        "E-mail ou senha inválidos.";
                    $mensagem_tipo = ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) ? 'danger' : 'warning';
                }
            } else {
                $_SESSION['login_attempts']++;
                $mensagem = ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) ?
                    "Muitas tentativas incorretas. Bloqueado." :
                    "E-mail ou senha inválidos.";
                $mensagem_tipo = ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) ? 'danger' : 'warning';
            }
            $stmt->close();
        }
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - StreetRep</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/login.css">
  <style>
    body { background: #f4f4f4; }
    .login-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .login-card { background: #000; color: #fff; border-radius: .6rem; padding: 2rem; box-shadow: 0 8px 24px rgba(0,0,0,0.2); max-width: 420px; width: 100%; }
    .login-card .form-control { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #fff; }
    .login-card label { color: rgba(255,255,255,0.8); }
    .login-card .btn-primary { width: 100%; }
    .small-muted { color: rgba(255,255,255,0.6); font-size: .9rem; }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="logo_box">
        <a href="index.php"><img src="./img/logo_png_4.png" alt="" height="170px" width="200px" id="streetrep_logo"></a>
      </div>
      <h3 class="mb-3">Entrar</h3>
      <?php if ($mensagem): 
        $alertClass = 'alert-warning';
        if ($mensagem_tipo === 'danger') $alertClass = 'alert-danger';
        if ($mensagem_tipo === 'success') $alertClass = 'alert-success';
      ?>
      <div class="alert <?= $alertClass ?> py-2" role="alert"><?= $mensagem; ?></div>
      <?php endif; ?>
      <form method="post" action="" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="mb-3">
          <label for="email" class="form-label">E‑mail</label>
          <input id="email" name="email" type="email" class="form-control" required autocomplete="username">
        </div>
        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input id="senha" name="senha" type="password" class="form-control" required autocomplete="current-password">
        </div>
        <div class="d-grid mb-2 gap-3">
          <button class="btn btn-warning" type="submit">Entrar</button>
          <a href="cadastro.php" class="btn btn-warning">Cadastro</a>
        </div>
        <p class="small-muted mb-0">Não tem conta? Peça para o administrador criar uma conta.</p>
      </form>
      <hr class="my-3" style="border-color: rgba(255,255,255,0.06)">
      <div class="text-center small-muted">
        Exemplo / dev: para criar usuário de teste acesse <code>?create_test=1</code> (apenas manualmente)
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
