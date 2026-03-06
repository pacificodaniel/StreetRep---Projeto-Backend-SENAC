<?php ini_set('opcache.enable', 0); ?>
<?php
session_start();
include "conn.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreetReputation - Sobre</title>
    <link rel="stylesheet" href="/STREETREP/css/sobre.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<?php include "template/head.php"; ?>
<?php include "template/header.php"; ?>

<main>
    <!-- SEÇÃO INTRODUTÓRIA -->
    <div class="info_content1" style="justify-content:center; padding:40px 0;">
        <div class="img_area" style="max-width:900px; width:100%; height:auto;">
            <h2 style="color:#fdd835;">Sobre o StreetRep</h2>
            <p style="color:#fff; font-size:16px; line-height:1.8;">
                O <strong>StreetRep</strong> é uma plataforma colaborativa e inovadora que transforma a forma como as comunidades compartilham informações sobre segurança e situações nas ruas. 
                Funcionando como uma mini rede social baseada em <strong>reputação</strong>, nosso sistema permite que usuários registrem ocorrências em um mapa interativo, 
                criando um banco de dados coletivo de eventos urbanos. Cada contribuição é avaliada pela comunidade, 
                construindo um sistema de confiança que beneficia todos os usuários e torna o ambiente mais seguro e transparente.
            </p>
        </div>
    </div>

    <!-- SEÇÃO DE 3 COLUNAS: IMAGEM + TEXTO -->
    <div class="secao_features">
        <div class="feature-item">
            <div class="feature-image-container">
                <img src="./img/strert rep image 2.png" alt="Observe e Relate" class="feature-image">
            </div>
            <div class="feature-text-container">
                <h4 class="text-warning">Observe e Relate</h4>
                <p class="text-white">
                    Qualquer situação nas ruas pode ser importante para a comunidade. 
                    Viu algo suspeito, um incidente ou uma situação que mereça atenção? 
                    Tire uma foto, preencha os detalhes e compartilhe no mapa em poucos segundos. 
                    Sua observação vigilante é o primeiro passo para construir um ambiente mais seguro e informado para todos.
                </p>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-image-container">
                <img src="./img/ocorrencias_image.PNG" alt="Mapa Colaborativo" class="feature-image">
            </div>
            <div class="feature-text-container">
                <h4 class="text-warning">Mapa Colaborativo</h4>
                <p class="text-white">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                    Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </p>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-image-container">
                <img src="./img/street rep image 4.png" alt="Sistema de Reputação" class="feature-image">
            </div>
            <div class="feature-text-container">
                <h4 class="text-warning">Sistema de Reputação</h4>
                <p class="text-white">
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
                    Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. 
                    Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.
                </p>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-image-container">
                <img src="./img/STREET REP IMAGE 3.png" alt="Comunidade Segura" class="feature-image">
            </div>
            <div class="feature-text-container">
                <h4 class="text-warning">Comunidade Segura</h4>
                <p class="text-white">
                    Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores 
                    eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, 
                    consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam.
                </p>
            </div>
        </div>
    </div>

    <!-- SEÇÃO ADICIONAL -->
    <div class="info_content1" style="justify-content:center; padding:40px 0; margin-top:40px;">
        <div class="img_area" style="max-width:900px; width:100%; height:auto;">
            <h3 style="color:#fdd835; margin-bottom:20px;">Por que escolher o StreetRep?</h3>
            <div class="row text-white" style="text-align:left;">
                <div class="col-md-6 mb-3">
                    <h5 class="text-warning"><i class="fas fa-check-circle"></i> Transparência Total</h5>
                    <p>Todas as ocorrências são visíveis e verificáveis pela comunidade, criando um ambiente de confiança.</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5 class="text-warning"><i class="fas fa-check-circle"></i> Dados em Tempo Real</h5>
                    <p>Informações atualizadas constantemente para que você sempre tenha os dados mais recentes.</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5 class="text-warning"><i class="fas fa-check-circle"></i> Comunidade Forte</h5>
                    <p>Faça parte de uma rede de usuários comprometidos com a segurança e o bem-estar coletivo.</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h5 class="text-warning"><i class="fas fa-check-circle"></i> Fácil de Usar</h5>
                    <p>Interface intuitiva que permite registrar ocorrências em poucos cliques diretamente no mapa.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include "template/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>