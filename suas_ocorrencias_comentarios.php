<?php ini_set('opcache.enable', 0); ?>
<?php
session_start();
include "conn.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// pega id da ocorrência pela querystring
$id_ocorrencia = intval($_GET['id_ocorrencia'] ?? 0);
if ($id_ocorrencia <= 0) {
    // mostra página básica com aviso (mantendo header/footer)
    $ocorrencia = null;
    $comentarios = [];
} else {
    // busca dados da ocorrência
    $sqlOc = "SELECT id, titulo, descricao, gravidade, aval_positivo, aval_negativo, DATE_FORMAT(data, '%d/%m/%Y %H:%i') AS data_formatada
              FROM ocorrencias WHERE id = ?";
    $stmt = $conn->prepare($sqlOc);
    $stmt->bind_param("i", $id_ocorrencia);
    $stmt->execute();
    $resOc = $stmt->get_result();
    $ocorrencia = $resOc->fetch_assoc();
    $stmt->close();

    // busca comentários/avaliações ligados (mais recentes primeiro)
    $sqlCom = "SELECT a.id, a.tipo, a.comentario, a.criado_em, u.nome AS usuario
               FROM avaliacoes a
               LEFT JOIN usuarios u ON a.id_usuario = u.id
               WHERE a.id_ocorrencia = ?
               ORDER BY a.criado_em DESC";
    $stmt = $conn->prepare($sqlCom);
    $stmt->bind_param("i", $id_ocorrencia);
    $stmt->execute();
    $resCom = $stmt->get_result();
    $comentarios = $resCom->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreetRep — Comentários</title>
    <link rel="stylesheet" href="/STREETREP/css/suas_ocorrencias_comentarios.css">
    <!-- link do awesome fonts (biblioteca) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!--link do bootstrap css (biblioteca) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      /* Estilos mínimos rápidos para combinar com o tema escuro do site */
      body { background-color: #0f0f0f; color: #fff; }
      .comentario_content { width: 92%; max-width: 1000px; margin: 28px auto 80px; }
      .ocorrencia-header { background: #151515; border: 1px solid #2b2b2b; padding: 16px; border-radius: 8px; margin-bottom: 16px; }
      .comentario-card { background: #161616; border: 1px solid #2d2d2d; padding: 14px; border-radius: 8px; margin-bottom: 12px; color: #eee; }
      .comentario-meta { font-size: 0.9rem; color: #bdbdbd; }
      .positivo { color: #ffd43b; font-weight: 700; } /* destaque amarelo/positivo */
      .negativo { color: #ff6b6b; font-weight: 700; } /* destaque vermelho/negativo */
      .sem-comentario { color: #9a9a9a; font-style: italic; }
      .btn-back { margin-bottom: 12px; }
    </style>
</head>
<body>
     <header>
        <div class="header_area1">
            <a href=""> <img src="img/logo_png_4.png" alt="logo" height="250px" width="250px" class="page_logo"></a>
            <a href="" id="logo_a">STREET REP</a>
        </div>
        <nav class="menu_header">
            <div class="menu_header_links">
                <a href="index.php">Home</a>
                <a href="page1.php" id="menu_header_Mapa">Mapa</a>
                <a href="suas_avaliacoes.php" id="a_suas_avaliacoes"> Suas Avaliações</a>
                <a href="suas_ocorrencias.php" id="a_suas_ocorrencias"> Suas Ocorrências</a>
                <a href="suporte.php">Suporte</a>
                <a href="configuracoes.php">Configurações</a>
            </div>
            <div class="login_information">
                <p class="text-warning">👤 <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?></p>
                <p class="text-warning">Verificado: <?php echo ($_SESSION['verificado'] ?? false) ? 'Sim ✅' : 'Não ❌'; ?></p>
                <a href="logout.php" class="btn btn-warning" id="button_sair_page1">Sair</a>
            </div>
        </nav>
     </header>

    <main id="id_main">
        <div class="comentario_content">
            <a href="suas_ocorrencias.php" class="btn btn-outline-warning btn-back">← Voltar para suas ocorrências</a>

            <h2 class="text-warning mb-3">Comentários da Ocorrência</h2>

            <?php if (!$ocorrencia): ?>
                <div class="alert alert-warning">Ocorrência inválida ou não informada.</div>
            <?php else: ?>
                <!-- bloco com dados da ocorrência -->
                <div class="ocorrencia-header">
                  <h4 class="text-warning mb-1"><?= htmlspecialchars($ocorrencia['titulo']) ?></h4>
                  <div class="comentario-meta mb-2">
                    <strong>Gravidade:</strong> <?= htmlspecialchars($ocorrencia['gravidade']) ?> &nbsp;•&nbsp;
                    <strong>Data:</strong> <?= htmlspecialchars($ocorrencia['data_formatada'] ?? '') ?>
                  </div>
                  <p style="white-space:pre-wrap;"><?= nl2br(htmlspecialchars($ocorrencia['descricao'] ?? '')) ?></p>
                  <div class="mt-2">
                    <span class="me-3">✔️ <?= intval($ocorrencia['aval_positivo'] ?? 0) ?></span>
                    <span>❌ <?= intval($ocorrencia['aval_negativo'] ?? 0) ?></span>
                  </div>
                </div>

                <!-- seção de comentários -->
                <?php if (count($comentarios) === 0): ?>
                    <div class="alert alert-secondary">Ainda não há comentários para esta ocorrência.</div>
                <?php else: ?>
                    <?php foreach ($comentarios as $c): ?>
                        <div class="comentario-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="<?= ($c['tipo'] === 'positivo') ? 'positivo' : 'negativo' ?>">
                                        <?= ($c['tipo'] === 'positivo') ? '✅ Positiva' : '❌ Negativa' ?>
                                    </div>
                                    <div style="font-weight:700;"><?= htmlspecialchars($c['usuario'] ?? 'Usuário desconhecido') ?></div>
                                </div>
                                <div class="text-end comentario-meta">
                                    <?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?>
                                </div>
                            </div>

                            <hr style="border-color:#2b2b2b;margin:10px 0;">

                            <p style="margin-bottom:6px;"><?= $c['comentario'] ? nl2br(htmlspecialchars($c['comentario'])) : "<span class='sem-comentario'>(Sem comentário)</span>" ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-section">
      <h4>StreetRep</h4>
      <p>© 2025 - Desenvolvido por Daniel Pacifico</p>
    </div>

    <div class="footer-section">
      <h4>Links</h4>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="#">Sobre</a></li>
        <li><a href="#">Contato</a></li>
        <li><a href="#">Política de Privacidade</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h4>Siga-nos</h4>
      <ul class="social-links">
       <a href="#"><i class="fa-brands fa-instagram" style="color: #FFD43B; font-size: 20px;"></i> </a>
       <a href="#"><i class="fa-brands fa-x-twitter" style="color: #FFD43B;"></i> </a>
       <a href="#"><i class="fa-brands fa-discord" style="color: #FFD43B;"></i></a>
      </ul>
    </div>
  </div>
</footer>

<!--link do bootstrap js(biblioteca) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
