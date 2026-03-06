<?php ini_set('opcache.enable', 0); ?>
<?php
session_start();
include "conn.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suas Avaliações - StreetRep</title>
    <link rel="stylesheet" href="/STREETREP/css/suas_avaliacoes.css">
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
    <div class="ocorrencia_content">
        <!-- Avaliações serão carregadas aqui pelo JS -->
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    fetch('listar_avaliacoes_usuario.php')
        .then(res => res.json())
        .then(avaliacoes => {
            const container = document.querySelector('.ocorrencia_content');
            container.innerHTML = '';

            if (!avaliacoes.length) {
                container.innerHTML = `
                    <div class="alert alert-warning text-center" role="alert">
                        Você ainda não fez nenhuma avaliação.
                    </div>
                `;
                return;
            }

            avaliacoes.forEach(a => {
    const title = a.titulo || a.nome_ocorrencia || a.titulo_ocorrencia || 'Sem título';
    const comentario = a.comentario || '(Sem comentário)';
    const tipoTexto = a.tipo === 'positivo' ? '✅ Positiva' : '❌ Negativa';
    const criado = a.criado_em ? new Date(a.criado_em).toLocaleString('pt-BR') : 'Não informada';

    const div = document.createElement('div');
    div.className = 'ocorrencia_item card p-3 mb-3 shadow-sm';
    div.innerHTML = `
        <h5 class="text-warning">Avaliação feita em: <p class="fw-bold fw-bolder" style="font-size: 30px"> ${escapeHtml(title)}  </p> </h5>
        <p class="text-white"><strong>Comentário:</strong> "${escapeHtml(comentario)}"</p>
        <p class="text-white"><strong>Tipo:</strong> ${tipoTexto}</p>
        <p class="text-white"><strong>Data:</strong> ${escapeHtml(criado)}</p>
        <button class="btn btn-danger w-25 align-self-center" onclick="excluirAvaliacao(${a.id}, this)">Excluir Avaliação</button>

    `;
    container.appendChild(div);
});

        })
        .catch(err => console.error("Erro ao carregar avaliações:", err));
});

//escapehtml
function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');
}


// Função para excluir avaliação ( implementada )
function excluirAvaliacao(id, botao) {
    if (!confirm("Tem certeza que deseja excluir esta avaliação?")) return;

    fetch('excluir_avaliacao.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            // Faz a div sumir suavemente
            const div = botao.closest('.ocorrencia_item');
            if (div) {
                div.style.transition = 'opacity 0.5s ease';
                div.style.opacity = '0';
                setTimeout(() => div.remove(), 500);
            }
        } else {
            alert('Erro ao excluir: ' + (data.mensagem || 'desconhecido'));
        }
    })
    .catch(err => {
        console.error('Erro no fetch:', err);
        alert('Ocorreu um erro ao tentar excluir.');
    });
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
                <a href="#"><i class="fa-brands fa-instagram" style="color: #FFD43B; font-size: 20px;"></i></a>
                <a href="#"><i class="fa-brands fa-x-twitter" style="color: #FFD43B; font-size: 20px;"></i></a>
                <a href="#"><i class="fa-brands fa-discord" style="color: #FFD43B; font-size: 20px;"></i></a>
            </ul>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
