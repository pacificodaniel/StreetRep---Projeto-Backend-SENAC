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
    <title>StreetReputation - Configurações</title>
    <link rel="stylesheet" href="/STREETREP/css/configuracoes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
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
            <p class="text-warning">Verificado: <?php echo ($_SESSION['verificado'] ?? false) ? 'Sim ✅' : 'Não ❌'; ?></p>
            <a href="logout.php" class="btn btn-warning" id="button_sair_page1">Sair</a>
        </div>
    </nav>
</header>

<main id="id_main">
    <div class="ocorrencia_content" >
        <div class="card p-4 mb-3 shadow-sm" style="background-color:#161616; ">
            <h4 class="text-warning">Status de Verificação</h4>
            <?php if(($_SESSION['tipo_usuario'] ?? '') === 'ADMIN'): ?>
<div class="card p-4 mb-3 shadow-sm" style="background-color:#161616; border-left: 4px solid #ffc107;">
    <h4 class="text-warning">Painel Admin</h4>
    <p class="text-white">Acesse e gerencie requisições de verificação:</p>
    <a href="adminpanel.php" class="btn btn-warning w-25 align-self-center" >Página de aprovações (ADMIN) </a>
    
   
</div>
<div class="card p-4 mb-3 shadow-sm" style="background-color:#161616; border-left: 4px solid #ffc107;">
              
                <h4 class="text-warning">Página de Denúncias (ADM)</h4>
            <p class="text-white">Gerencie denúncias dos usuários:</p>

               <a href="denunciaspanel.php" class="btn btn-warning w-25 align-self-center" >Página de denúncias dos usuários (ADMIN) </a>
            </div>


<?php endif; ?>
            <p class="text-white">VERIFICADO: <?php echo ($_SESSION['verificado'] ?? false) ? 'Sim ✅' : 'Não ❌'; ?></p>
            <?php if (!($_SESSION['verificado'] ?? false)): ?>
            <button class="btn btn-warning mb-3" id="btnSolicitarVerificacao">Enviar solicitação de verificação</button>
            <p class="text-white">Envie mais informações sobre você para se tornar verificado no StreetRep e assim conseguir enviar ocorrências para alertar outros usuários!</p>

            <form id="formVerificacao" style="display:none; margin-top:15px; text-align:left;">
                <div class="mb-3">
                    <label for="nomeCompleto" class="form-label text-white">Nome completo</label>
                    <input type="text" class="form-control" id="nomeCompleto" name="nomeCompleto" required>
                </div>
                <div class="mb-3">
                    <label for="cpf" class="form-label text-white">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" required>
                </div>
                <div class="mb-3">
                    <label for="emailConfirm" class="form-label text-white">Confirmação de Email</label>
                    <input type="email" class="form-control" id="emailConfirm" name="emailConfirm" required>
                </div>
                <div class="mb-3">
                    <label for="redeSocial" class="form-label text-white">Link ou nome da sua rede social (opcional)</label>
                    <input type="text" class="form-control" id="redeSocial" name="redeSocial">
                </div>
                <button type="submit" class="btn btn-warning">Enviar solicitação</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
   
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById('btnSolicitarVerificacao');
    const form = document.getElementById('formVerificacao');

    if(btn) {
        btn.addEventListener('click', () => {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
    }

    if(form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const dados = new FormData(form);
            

            fetch('requisitar_verificacao.php', {
                method: 'POST',
                body: dados,
            })
            .then(res => res.json())
            .then(data => {
                if(data.sucesso){
                    alert('Solicitação enviada com sucesso!');
                    form.style.display = 'none';
                } else {
                    alert(data.mensagem || 'Erro ao enviar solicitação.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erro de conexão.');
            });
        });
    }
});
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
