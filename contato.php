<?php ini_set('opcache.enable', 0); ?>
<?php
session_start();
include "conn.php";
// contato acessível a usuários logados ou não; se quiser bloquear, descomente abaixo
// if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreetReputation - Contato</title>
    <link rel="stylesheet" href="/STREETREP/css/contato.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<?php include "template/head.php"; ?>
<?php include "template/header.php"; ?>

<main>
    <div class="info_content1" style="justify-content:center; padding:40px 0;">
        <div class="img_area" style="max-width:900px; width:100%;">
            <h2 style="color:#fdd835;">Entre em contato com a nossa equipe</h2>
            <p style="color:#fff;">Use o formulário abaixo para enviar dúvidas, sugestões ou relatar problemas. Sua mensagem será encaminhada para nossa equipe administrativa.</p>

            <div class="card p-4 mt-3" style="background-color:#161616; border-left:4px solid #ffc107;">
                <form id="formContato">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-warning">Nome</label>
                            <input type="text" name="nome" class="form-control" placeholder="Seu nome" value="<?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-warning">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="seu@exemplo.com" value="<?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-warning">Assunto</label>
                        <input type="text" name="assunto" class="form-control" placeholder="Assunto da mensagem" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-warning">Mensagem</label>
                        <textarea name="mensagem" rows="6" class="form-control" placeholder="Descreva sua mensagem..." required></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="btnEnviar" class="btn btn-warning">Enviar para o Suporte</button>
                    </div>
                    <p class="small text-muted mt-2">Limite: 1 envio por e-mail a cada 24 horas. Limite por IP: máximo 3 envios por hora.</p>
                </form>
            </div>

            <div class="card p-3 mt-4" style="background-color:#161616; border-left:4px solid #ffc107;">
                <h5 class="text-warning">Informações</h5>
                <p class="text-white">Quando você enviar, todos os administradores do sistema receberão uma cópia por e-mail. Se não receber resposta, verifique a caixa de spam ou contate-nos novamente após o período de espera.</p>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formContato');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnEnviar');
        btn.disabled = true;
        btn.innerText = 'Enviando...';

        const dados = new FormData(form);

        fetch('processar_contato.php', {
            method: 'POST',
            body: dados
        })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                alert(data.mensagem || 'Mensagem enviada com sucesso. Obrigado!');
                form.reset();
            } else {
                alert(data.mensagem || 'Erro ao enviar mensagem.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erro de conexão ao enviar mensagem.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'Enviar para o Suporte';
        });
    });
});
</script>

<?php include "template/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
