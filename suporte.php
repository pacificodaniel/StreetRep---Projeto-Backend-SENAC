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
    <title>StreetReputation - Suporte</title>
    <link rel="stylesheet" href="/STREETREP/css/suporte.css">
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
            <a href="suporte.php" id="a_suporte">Suporte</a>
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
    <div class="suporte_content">
        <!-- Seção de Contato -->
        <div class="card p-4 mb-3 shadow-sm" style="background-color:#161616; border-left: 4px solid #ffc107;">
            <h4 class="text-warning"><i class="fas fa-headset"></i> Central de Suporte</h4>
            <p class="text-white">Encontrou um problema? Tem uma sugestão? Entre em contato conosco através do formulário abaixo.</p>
        </div>

        <!-- Formulário de Suporte -->
        <div class="card p-4 shadow-sm" style="background-color:#161616; border-left: 4px solid #ffc107;">
            <form id="formSuporte" method="POST" action="processar_suporte.php">
                <div class="mb-3">
                    <label for="assunto" class="form-label text-warning"><strong>Assunto *</strong></label>
                    <select class="form-control" id="assunto" name="assunto" required style="background-color:#222; color:#fff; border-color:#ffc107;">
                        <option value="" selected disabled>Selecione um assunto...</option>
                        <option value="relato_bug">Relatar um Bug</option>
                        <option value="sugestao">Sugestão de Melhoria</option>
                        <option value="duvida">Dúvida Geral</option>
                        <option value="problema_conta">Problema na Conta</option>
                        <option value="problema_verificacao">Problema de Verificação</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="titulo" class="form-label text-warning"><strong>Título *</strong></label>
                    <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Resumo do seu problema ou sugestão" required style="background-color:#222; color:#fff; border-color:#ffc107;">
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label text-warning"><strong>Descrição Detalhada *</strong></label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="6" placeholder="Descreva seu problema, sugestão ou dúvida em detalhes..." required style="background-color:#222; color:#fff; border-color:#ffc107;"></textarea>
                </div>

                <div class="mb-3">
                    <label for="navegador" class="form-label text-warning"><strong>Navegador/Sistema (opcional)</strong></label>
                    <input type="text" class="form-control" id="navegador" name="navegador" placeholder="Ex: Chrome no Windows 10" style="background-color:#222; color:#fff; border-color:#ffc107;">
                </div>

                <div class="mb-3">
                    <label for="prioridade" class="form-label text-warning"><strong>Prioridade *</strong></label>
                    <select class="form-control" id="prioridade" name="prioridade" required style="background-color:#222; color:#fff; border-color:#ffc107;">
                        <option value="" selected disabled>Selecione a prioridade...</option>
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="receberResposta" name="receberResposta" checked style="accent-color:#ffc107;">
                    <label class="form-check-label text-white" for="receberResposta">
                        Desejo receber uma resposta sobre minha solicitação
                    </label>
                </div>

                <button type="submit" class="btn btn-warning w-100" id="btnEnviarSuporte">
                    <i class="fas fa-paper-plane"></i> Enviar Mensagem
                </button>
            </form>
        </div>

        <!-- Informações de Suporte Adicionais -->
        <div class="card p-4 mt-4 shadow-sm" style="background-color:#161616; border-left: 4px solid #ffc107;">
            <h5 class="text-warning mb-3"><i class="fas fa-info-circle"></i> Perguntas Frequentes</h5>
            <div class="accordion" id="accordionFAQ">
                <div class="accordion-item" style="background-color:#222; border-color:#ffc107;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" style="background-color:#222; color:#ffc107;">
                            Como faço para verificar minha conta?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="background-color:#1a1a1a; color:#fff;">
                            Acesse a página de Configurações e clique em "Enviar solicitação de verificação". Você precisará fornecer alguns dados pessoais para passar na verificação.
                        </div>
                    </div>
                </div>
                <div class="accordion-item" style="background-color:#222; border-color:#ffc107;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" style="background-color:#222; color:#ffc107;">
                            Como registro uma ocorrência?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="background-color:#1a1a1a; color:#fff;">
                            Você precisa estar verificado primeiro. Após a verificação, acesse "Suas Ocorrências" e clique em "Nova Ocorrência" para relatar um problema no mapa.
                        </div>
                    </div>
                </div>
                <div class="accordion-item" style="background-color:#222; border-color:#ffc107;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" style="background-color:#222; color:#ffc107;">
                            Qual é a diferença entre Ocorrências e Avaliações?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="background-color:#1a1a1a; color:#fff;">
                            Ocorrências são relatos de problemas específicos em locais (crimes, perigos, etc.). Avaliações são comentários e notas sobre locais, negócios ou áreas em geral.
                        </div>
                    </div>
                </div>
                <div class="accordion-item" style="background-color:#222; border-color:#ffc107;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" style="background-color:#222; color:#ffc107;">
                            Quanto tempo leva para receber uma resposta?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="background-color:#1a1a1a; color:#fff;">
                            Geralmente respondemos em até 48 horas. Para assuntos mais urgentes, podem receber atendimento mais rápido.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contato Direto -->
        <div class="card p-4 mt-4 shadow-sm" style="background-color:#161616; border-left: 4px solid #ffc107;">
            <h5 class="text-warning mb-3"><i class="fas fa-envelope"></i> Outras Formas de Contato</h5>
            <div class="row text-white">
                <div class="col-md-6 mb-3">
                    <h6 class="text-warning">Email</h6>
                    <p><a href="mailto:suporte@streetrep.com.br" style="color:#ffc107; text-decoration:none;">suporte@streetrep.com.br</a></p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-warning">Redes Sociais</h6>
                    <div>
                        <a href="#" style="color:#ffc107; margin-right:10px;"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" style="color:#ffc107; margin-right:10px;"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" style="color:#ffc107;"><i class="fab fa-discord fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const formSuporte = document.getElementById('formSuporte');

    if(formSuporte) {
        formSuporte.addEventListener('submit', (e) => {
            e.preventDefault();

            const dados = new FormData(formSuporte);

            fetch('processar_suporte.php', {
                method: 'POST',
                body: dados,
            })
            .then(res => res.json())
            .then(data => {
                if(data.sucesso){
                    alert('Mensagem enviada com sucesso! Entraremos em contato em breve.');
                    formSuporte.reset();
                } else {
                    alert(data.mensagem || 'Erro ao enviar mensagem.');
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
