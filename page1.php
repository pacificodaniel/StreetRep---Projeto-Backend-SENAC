<?php ini_set('opcache.enable', 0);?>
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
  <!-- turf.js -->
  <script src="https://cdn.jsdelivr.net/npm/@turf/turf/turf.min.js"></script>

  <!-- Leaflet (CSS + JS) -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StreetRep - MAPA </title>
  <link rel="stylesheet" href="/STREETREP/css/page1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
        <a href="sobre.php" id="menu_header_Mapa">Mapa</a>
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

  <main>
    <div class="content1">
      <!-- input para endereço -->
      <div class="mb-2" id="input_map_location">
        <label class="text-white">Endereço ou local:</label>
        <input type="text" id="endereco" class="form-control" placeholder="Ex: São Paulo, SP">
        <button type="button" id="buscarEndereco" class="btn btn-warning mt-1">Buscar</button>
        <!-- botão adicionado: ir para minha localização -->
        <button type="button" id="btnMinhaLocalizacao" class="btn btn-warning mt-1 ms-2">
            <i class="fa-solid fa-location-crosshairs"></i> Minha localização
        </button>

        <label class="text-white">Filtrar por gravidade:</label>
        <select id="filtroTipo" class="form-select w-100 align-self-center">
          <option value="todos">Todos</option>
          <option value="alto">Alto</option>
          <option value="medio">Médio</option>
          <option value="baixo">Baixo</option>
          <option value="inofensivo">Inofensivo</option>
        </select>
      </div>
    </div>

    <!-- mapa -->
    <div id="map" style="height: 800px; width: 100%;"></div>
  </main>

  <div id="comentariosDiv" class="comentarios-lateral">
    <!-- Conteúdo será carregado via JS -->
  </div>

  <!-- FORM OCORRÊNCIA (permanece no PHP/HTML) -->
  <div id="formOcorrencia" class="form-container" >
    <h5>Nova Ocorrência</h5>
    <form id="ocorrenciaForm" enctype="multipart/form-data">
      <input type="hidden" id="lat" name="lat">
      <input type="hidden" id="lng" name="lng">

      <div class="mb-2">
        <label>Título:</label>
        <input type="text" id="titulo" class="form-control" required name="titulo">
      </div>

      <div class="mb-2">
        <label>Descrição:</label>
        <textarea id="descricao" class="form-control" name="descricao" required></textarea>
      </div>

      <div class="mb-2">
        <label>Gravidade:</label>
        <select id="gravidade" class="form-select" name="gravidade" required>
          <option value="inofensivo">Inofensivo</option>
          <option value="baixo">Baixo</option>
          <option value="medio">Médio</option>
          <option value="alto">Alto</option>
        </select>
        <label for="imagem">Imagem (opcional)</label>
    <input    type="file" id="imagem"  name="imagem"  accept="image/jpeg,image/png" class="form-control">
    <img  id="preview_imagem"    style="display:none; max-width: 300px; margin-top:10px; border-radius: 10px; justify-self: center;" >
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" id="cancelar">Cancelar</button>
        <button type="submit" class="btn btn-warning">Registrar</button>
      </div>
    </form>
  </div>

  <?php include "template/footer.php"; ?>

  <!-- === VARIÁVEIS PHP -> JS (mínimo indispensável) === -->
  <script>
    window.AppData = {
      usuarioVerificado: <?php echo ($_SESSION['verificado'] ?? 0) ? 'true' : 'false'; ?>
    };
  </script>

 <!-- loading spinner  -->
<script src="/STREETREP/js/loading.js"></script>
  <!-- === JS === -->
  <script src="/STREETREP/js/page1.js"></script>
</body>
</html>

