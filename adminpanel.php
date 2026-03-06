<?php
session_start();
include 'conn.php';

// Verifica se é ADMIN
if (!isset($_SESSION['usuario_id']) || (strtoupper($_SESSION['tipo_usuario'] ?? '') !== 'ADMIN')) {
    header("Location: configuracoes.php");
    exit;
}

// Buscar requisições
$search = trim($_GET['search'] ?? '');
$like = "%$search%";

// Query corrigida: u.nome (coluna correta) e alias para usuario_nome para manter compatibilidade com o HTML abaixo
$sql = "SELECT r.id, u.nome AS usuario_nome, r.nome_completo, r.cpf, r.email, r.rede_social, r.status, r.data_solicitacao
        FROM requisicoes_verificacao r
        JOIN usuarios u ON r.usuario_id = u.id
        WHERE r.nome_completo LIKE ? OR u.nome LIKE ?
        ORDER BY r.id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    // mostra erro SQL para debug (remova em produção)
    die("Erro na query SQL: " . $conn->error);
}

$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();
$requisicoes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel Admin - Requisições</title>
<link rel="stylesheet" href="/STREETREP/css/configuracoes.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <div class="header_area1">
        <a href=""><img src="img/logo_png_4.png" alt="logo" height="250px" width="250px" class="page_logo"></a>
        <a href="" id="logo_a">STREET REP</a>
    </div>
    <nav class="menu_header">
        <div class="menu_header_links">
            <a href="index.php">Home</a>
            <a href="page1.php" id="menu_header_Mapa">Mapa</a>
            <a href="suas_avaliacoes.php" id="a_suas_avaliacoes">Suas Avaliações</a>
            <a href="suas_ocorrencias.php" id="a_suas_ocorrencias">Suas Ocorrências</a>
            <a href="suporte.php">Suporte</a>
            <a href="configuracoes.php" id="a_configuracoes">Configurações</a>
        </div>
        <div class="login_information">
            <p class="text-warning">👤 <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?></p>
            <p class="text-warning">Tipo: <?php echo htmlspecialchars($_SESSION['tipo_usuario'] ?? ''); ?></p>
            <a href="logout.php" class="btn btn-warning" id="button_sair_page1">Sair</a>
        </div>
    </nav>
</header>

<main id="id_main">
    <div class="container mt-4" >
        <h3 class="text-warning mb-3">Requisições de Verificação</h3>

        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar por nome do usuário ou nome completo" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-warning" type="submit">Buscar</button>
            <button class="btn btn-outline-secondary" type="button" onclick="location.href='adminpanel.php'">Limpar</button>
        </form>

        <div class="mb-3 d-flex gap-2">
            <button class="btn btn-success" onclick="aprovarTodos()">Aprovar Todos</button>
            <button class="btn btn-danger" onclick="reprovarTodos()">Reprovar Todos</button>
        </div>

        <?php if (empty($requisicoes)): ?>
            <div class="alert alert-warning">Não há requisições correspondentes.</div>
        <?php endif; ?>

        <?php foreach($requisicoes as $r): ?>
        <div class="card p-3 mb-2 shadow-sm" style="background-color:#161616; border-left:4px solid #ffc107;">
            <p class="text-white"><strong>ID:</strong> <?php echo (int)$r['id']; ?></p>
            <p class="text-white"><strong>Usuário:</strong> <?php echo htmlspecialchars($r['usuario_nome']); ?></p>
            <p class="text-white"><strong>Nome Completo:</strong> <?php echo htmlspecialchars($r['nome_completo']); ?></p>
            <p class="text-white"><strong>CPF:</strong> <?php echo htmlspecialchars($r['cpf']); ?></p>
            <p class="text-white"><strong>Email:</strong> <?php echo htmlspecialchars($r['email']); ?></p>
            <p class="text-white"><strong>Rede Social:</strong> <?php echo htmlspecialchars($r['rede_social']); ?></p>
            <p class="text-white"><strong>Data:</strong> <?php echo htmlspecialchars($r['data_solicitacao']); ?></p>
            <p class="text-white"><strong>Status:</strong> <?php echo htmlspecialchars($r['status']); ?></p>

            <?php if ($r['status'] === 'PENDENTE'): ?>
                <div class="mt-2 d-flex gap-2">
                    <button class="btn btn-success" onclick="aprovar(<?php echo (int)$r['id']; ?>)">Aprovar</button>
                    <button class="btn btn-danger" onclick="reprovar(<?php echo (int)$r['id']; ?>)">Reprovar</button>
                </div>
            <?php else: ?>
                <div class="mt-2">
                    <span class="small text-muted">Ação já tomada.</span>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
function aprovar(id){
    if(!confirm('Aprovar esta solicitação?')) return;
    fetch('aprovar_requisicao.php', {
        method: 'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if(data.sucesso) location.reload();
        else alert(data.mensagem || 'Erro ao aprovar');
    })
    .catch(() => alert('Erro de conexão'));
}

function reprovar(id){
    if(!confirm('Reprovar esta solicitação?')) return;
    fetch('reprovar_requisicao.php', {
        method: 'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if(data.sucesso) location.reload();
        else alert(data.mensagem || 'Erro ao reprovar');
    })
    .catch(() => alert('Erro de conexão'));
}

function aprovarTodos(){
    if(!confirm('Aprovar todas as solicitações pendentes?')) return;
    fetch('aprovar_todos.php', {method:'POST'})
    .then(res=>res.json())
    .then(data => {
        if(data.sucesso) location.reload();
        else alert(data.mensagem || 'Erro ao aprovar todos');
    })
    .catch(()=> alert('Erro de conexão'));
}

function reprovarTodos(){
    if(!confirm('Reprovar todas as solicitações pendentes?')) return;
    fetch('reprovar_todos.php', {method:'POST'})
    .then(res=>res.json())
    .then(data => {
        if(data.sucesso) location.reload();
        else alert(data.mensagem || 'Erro ao reprovar todos');
    })
    .catch(()=> alert('Erro de conexão'));
}
</script>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
