<?php ini_set('opcache.enable', 0);?>
<?php


session_start();
include "conn.php";

if (!isset($_SESSION['usuario_id'])) {
  // die("Usuário não está logado");
   header("Location: login.php");
}

 


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreetRep </title>
    <link rel="stylesheet" href="/STREETREP/css/page1.css">
    <!-- link do awesome fonts (biblioteca) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!--link do bootstrap css (biblioteca) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
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
            <a href="sobre.php" id="menu_header_Mapa">Mapa</a>
            <a href="avaliacoes.php"> Avaliações</a>
            <a href="avaliacoes.php"> Ocorrências</a>
            <a href="suporte.php">Suporte</a>
            <a href="config.php">Configurações</a>
           
            </div>
            <div class="login_information">
            <p class="text-warning">👤 
                <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?>
                
            </p>

            <p class="text-warning">Verificado: 
                <?php echo ($_SESSION['verificado'] ?? false) ? 'Sim ✅' : 'Não ❌'; ?>
            </p>
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
                    </div>
            </div>
  <script>
    //JS DO INPUT DE ENDEREÇO PARA LOCALIZAR NO MAPA
    document.getElementById('buscarEndereco').addEventListener('click', () => {
  const endereco = document.getElementById('endereco').value;
  if (!endereco) return alert('Digite um endereço');

  fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`)
    .then(res => res.json())
    .then(data => {
      if (data.length === 0) return alert('Endereço não encontrado');
      const local = data[0];
      const lat = parseFloat(local.lat);
      const lng = parseFloat(local.lon);

      // Atualiza o form
      document.getElementById('lat').value = lat;
      document.getElementById('lng').value = lng;

      // Move o mapa e o form
      map.setView([lat, lng], 15);
      const point = map.latLngToContainerPoint([lat, lng]);
      const formBox = document.getElementById('formOcorrencia');
      formBox.style.left = point.x + 'px';
      formBox.style.top = point.y + 'px';
    });
});

  </script>

     <!-- div do mapa  -->
    <div id="map" style="height: 500px; width: 100%;"></div>











    </main>
    <div id="comentariosDiv" class="comentarios-lateral">
  <!-- Conteúdo será carregado via JS -->
</div>
        <!-- js do comentários e avaliações -->
        <script>
  // função global para enviar comentário
  function adicionarComentario(id_ocorrencia, tipo) {
      const comentario = document.getElementById('novoComentario').value.trim();
      if (!comentario) return alert('Digite um comentário.');

      fetch('registrar_avaliacao.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          credentials: 'include',
          body: JSON.stringify({ id_ocorrencia, tipo, comentario, titulo })
      })
      .then(res => res.json())
      .then(resp => {
          if (resp.sucesso) {
              alert(resp.mensagem);
              abrirComentarios(id_ocorrencia); // recarrega a div
          } else {
              alert('Erro: ' + resp.mensagem);
          }
      })
      .catch(err => {
          console.error(err);
          alert('Erro na requisição. Veja o console.');
      });
  }

  // função para abrir os comentários
  function abrirComentarios(id_ocorrencia) {
      fetch(`listar_comentarios.php?id_ocorrencia=${id_ocorrencia}`)
      .then(res => res.json())
      .then(comentarios => {
          let html = `<h4 class="text-warning">Comentários da ocorrência ${titulo}</h4>`;
          comentarios.forEach(c => {
              html += `<p><strong>${c.usuario}</strong> (${c.tipo}): ${c.comentario}</p>`;
          });

          html += `
              <select id="tipoComentario" class="form-select mb-2">
                  <option value="real">✅ Positiva</option>
                  <option value="falso">❌ Negativa</option>
              </select>
              <textarea id="novoComentario" class="form-control mb-2" placeholder="Digite seu comentário"></textarea>
              <button id="btnEnviarComentario" class="btn btn-warning w-100 mb-2">Enviar</button>
              <button id="btnFecharComentario" class="btn btn-secondary w-100">Fechar</button>
          `;

          const div = document.getElementById('comentariosDiv');
          div.innerHTML = html;
          div.style.display = 'block';

          // listener do botão de enviar
          document.getElementById('btnEnviarComentario').addEventListener('click', function() {
              const tipo = document.getElementById('tipoComentario').value;
              adicionarComentario(id_ocorrencia, tipo);
          });

          // listener do botão de fechar
          document.getElementById('btnFecharComentario').addEventListener('click', function() {
              fecharComentarios();
          });
      });
  }

  // função de fechar a div
  function fecharComentarios() {
      document.getElementById('comentariosDiv').style.display = 'none';
  }
</script>

      <!-- fim js do comentários e avaliações -->

    <script>
        // ------------------------------------------------------------------------------------ PARTE DO MAPA 
  // Cria o mapa centralizado
  var map = L.map('map').setView([-23.5505, -46.6333], 13); // São Paulo

  // Adiciona o mapa do OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

//mostra todas as ocorrencias do banco: 
// Depois de criar o mapa  
 //ALTERAÇÕES: 1 - FOI ADICIONADO AVALIAÇÕES + BOTÃO PARA A SEÇÃO DOS COMENTÁRIOS NO POPUP
fetch('listar_ocorrencias.php')
  .then(res => res.json())
  .then(ocorrencias => {
    ocorrencias.forEach(o => {
      const marker = L.marker([o.latitude, o.longitude]).addTo(map);

      // HTML do popup
      const popupHTML = `
        <strong class="fw bolder fs-5">${o.titulo}</strong><br>
        <em class="fw-bolder">Gravidade: ${o.gravidade}</em><br>
        <p>${o.descricao}</p>
        ✔️ ${o.aval_positivo ?? 0} ❌ ${o.aval_negativo ?? 0}<br>
        <button class="btn-avaliacoes btn btn-warning" data-id="${o.id}"> Comentários / Avaliar</button>
      `;

      marker.bindPopup(popupHTML);
    });

    // Delegação de evento para todos os botões do popup
    map.on('popupopen', function(e) {
      const popupNode = e.popup._contentNode;
      const btn = popupNode.querySelector('.btn-avaliacoes');
      if (btn) {
        btn.addEventListener('click', function() {
          const id_ocorrencia = this.dataset.id;
          abrirComentarios(id_ocorrencia); // função que cria a div lateral / form
        });
      }
    });
  });





//   // Adiciona um marcador de teste
//   L.marker([-23.5505, -46.6333]).addTo(map)
//     .bindPopup('<b>Ocorrência Exemplo</b><br>Algo aconteceu aqui.')
//     .openPopup();
// ------------------------------------------------------------------------------------
// ---------------------------









// ABRIR FORM AO CLICAR NO MAPA
// ---------------------------
window.addEventListener('DOMContentLoaded', () => {
  const formBox = document.getElementById('formOcorrencia');
  const form = document.getElementById('ocorrenciaForm');
  const btnCancelar = document.getElementById('cancelar');
 
 //MARCADOR TEMPORÁRIO
  // Variável global para o marcador temporário
let tempMarker = null;

map.on('click', function(e) {
    // Remove marcador temporário anterior, se houver
    if (tempMarker) {
        map.removeLayer(tempMarker);
    }

    // Adiciona marcador temporário translúcido
    tempMarker = L.marker([e.latlng.lat, e.latlng.lng], { opacity: 0.6 }).addTo(map);
    tempMarker.bindPopup('<i style="margin-top:20px;">Foi aqui.</i>').openPopup();

    // Preenche lat/lng no form
    document.getElementById('lat').value = e.latlng.lat;
    document.getElementById('lng').value = e.latlng.lng;

    // ----------------- Mostrar o form -----------------
    const point = map.latLngToContainerPoint(e.latlng); // converte lat/lng para pixels
    formBox.style.left = (point.x + 10) + 'px'; // desloca um pouco pra não sobrepor o marcador
    formBox.style.top = (point.y + 10) + 'px';
    formBox.style.display = 'block';
});
// ----------------- Fim do marcador temporario -----------------

  // Exibir form no ponto clicado
  map.on('click', function(e) {
    const point = map.latLngToContainerPoint(e.latlng); // converte lat/lng para pixels
    formBox.style.left = point.x + 'px';
    formBox.style.top = point.y + 'px';
    formBox.style.display = 'block';

    // preenche lat/lng no form
    document.getElementById('lat').value = e.latlng.lat;
    document.getElementById('lng').value = e.latlng.lng;
  });

  // Fechar form com o botão "Cancelar"
  btnCancelar.addEventListener('click', function() {
    formBox.style.display = 'none';
    form.reset(); // limpa os campos
  });

  // Submeter form via fetch
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    if (!confirm('Tem certeza que deseja registrar essa ocorrência?')) return;

    const dados = {
      titulo: document.getElementById('titulo').value,
      descricao: document.getElementById('descricao').value,
      gravidade: document.getElementById('gravidade').value,
      latitude: document.getElementById('lat').value,
      longitude: document.getElementById('lng').value
    };

    fetch('registrar_ocorrencia.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(resp => {
      alert(resp.mensagem);
      formBox.style.display = 'none';
      form.reset();

      // adiciona marcador no mapa em tempo real
      const marker = L.marker([dados.latitude, dados.longitude]).addTo(map);
      marker.bindPopup(`<strong>${dados.titulo}</strong><br>${dados.descricao}`);
    });
  });
});


// ------------------------------------------------------------------------------------
  
</script>
<!-- //FORM OCORRÊNCIA -->
<div id="formOcorrencia" class="form-container">
  <h5>Nova Ocorrência</h5>
  <form id="ocorrenciaForm">
    <input type="hidden" id="lat">
    <input type="hidden" id="lng">

    <div class="mb-2">
      <label>Título:</label>
      <input type="text" id="titulo" class="form-control" required>
    </div>

    <div class="mb-2">
      <label>Descrição:</label>
      <textarea id="descricao" class="form-control" required></textarea>
    </div>

    <div class="mb-2">
      <label>Gravidade:</label>
      <select id="gravidade" class="form-select">
        <option value="inofensivo">Inofensivo</option>
        <option value="baixo">Baixo</option>
        <option value="medio">Médio</option>
        <option value="alto">Alto</option>
      </select>
    </div>

    <div class="d-flex justify-content-end gap-2">
      <button type="button" class="btn btn-secondary" id="cancelar">Cancelar</button>
      <button type="submit" class="btn btn-warning">Registrar</button>
    </div>
  </form>
</div>
<!-- // ------------------------------------------------------------------------------------ -->

 <?php include "template/footer.php" ;?>
