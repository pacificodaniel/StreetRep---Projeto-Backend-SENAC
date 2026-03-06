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
    <title>StreetReputation - Suas Ocorrências </title>
    <link rel="stylesheet" href="/STREETREP/css/suas_ocorrencias.css">
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
            <a href="page1.php" id="menu_header_Mapa">Mapa</a>
            <a href="suas_avaliacoes.php" id="a_suas_avaliacoes"> Suas Avaliações</a>
            <a href="suas_ocorrencias.php" id="a_suas_ocorrencias"> Suas Ocorrências</a>
            <a href="suporte.php">Suporte</a>
            <a href="configuracoes.php">Configurações</a>
           
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
    <main id="id_main">
        <div class="ocorrencia_content">

        </div>





    </main>
    <script>
        
document.addEventListener("DOMContentLoaded", () => {
  fetch('listar_ocorrencias_usuario.php')
    .then(res => res.json())
    .then(ocorrencias => {
      const container = document.querySelector('.ocorrencia_content');
      container.innerHTML = '';

      if (!ocorrencias.length) {
        container.innerHTML = `
          <div class="alert alert-warning text-center" role="alert">
            Você ainda não cadastrou nenhuma ocorrência.
          </div>
        `;
        return;
      }

      ocorrencias.forEach(o => {
        
        const div = document.createElement('div');
        div.className = 'ocorrencia_item';
        div.innerHTML = `
  <h4 class="text-warning">${o.titulo}</h4>
  
         ${o.imagem ? `
            <img
                src="/STREETREP/${o.imagem}"
                alt="Imagem da ocorrência"
                class="ocorrencia_imagem mb-2"
            />
        ` : ''}

  
  <p class="text-white"><strong class="text-warning ">Gravidade:</strong> ${o.gravidade}</p>
   <p class="text-white"><strong class="text-warning">Data da Ocorrência:</strong> ${o.data}</p>
  <div class="mt-2">
  <span class="badge bg-success">
    👍 ${o.aval_positivo} Positivas
  </span>
  <span class="badge bg-danger">
    👎 ${o.aval_negativo} Negativas 
  </span>
</div>

  <p class="text-white descricao_texto" data-id="${o.id}">${o.descricao}</p>
  <div class="botoes-container">
    <button class="btn btn-sm btn-warning" onclick="editarOcorrencia(${o.id}, this)">Editar Descrição</button>
    <button class="btn btn-sm btn-success salvar-btn" style="display:none;" onclick="salvarDescricao(${o.id}, this)">Salvar</button>
    <button class="btn btn-danger" onclick="excluirOcorrencia(${o.id})">Excluir</button>
    <button class="btn btn-sm btn-warning" onclick="verComentarios(${o.id})">Ver comentários</button>
    <button class="btn btn-sm btn-warning" onclick="verNoMapa(${o.latitude}, ${o.longitude})">Ver no mapa</button>

  </div>
`;

        container.appendChild(div);
      });
    })
    .catch(err => console.error("Erro ao carregar ocorrências:", err));
});



function excluirOcorrencia(id) {
    const confirmar = confirm("Tem certeza que deseja excluir esta ocorrência?");
    if (!confirmar) return;

    fetch('excluir_ocorrencia.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_ocorrencia=${encodeURIComponent(id)}`,
        credentials: 'include' // 👈 envia o cookie de sessão PHP
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            alert("Ocorrência excluída com sucesso!");
            location.reload();
        } else {
            alert(data.mensagem || "Erro ao excluir a ocorrência.");
        }
    })
    .catch(err => {
        console.error("Erro ao excluir:", err);
        alert("Ocorreu um erro ao tentar excluir a ocorrência.");
    });
}
function verComentarios(id) {
  window.location.href = `suas_ocorrencias_comentarios.php?id_ocorrencia=${id}`;
}

//EDITAR
// INLINE PARA EDITAR DESCRIÇÃO DA OCORRÊNCIA
function editarOcorrencia(id, btnEditar) {
  const card = btnEditar.closest('.ocorrencia_item');
  const descricaoEl = card.querySelector('.descricao_texto');
  
  // Criar textarea com o texto atual
  const textarea = document.createElement('textarea');
  textarea.className = 'form-control mb-2';
  textarea.value = descricaoEl.textContent;
  textarea.style.background = '#111';
  textarea.style.color = '#fff';
  
  descricaoEl.replaceWith(textarea);
  
  // Mostrar botão salvar e esconder editar
  btnEditar.style.display = 'none';
  card.querySelector('.salvar-btn').style.display = 'inline-block';
}

//JS PARA SALVAR A DESCRIÇÃO EDITADA
function salvarDescricao(id, btnSalvar) {
  const card = btnSalvar.closest('.ocorrencia_item');
  const textarea = card.querySelector('textarea');
  const novaDescricao = textarea.value;

  fetch('editar_ocorrencia.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id_ocorrencia=${encodeURIComponent(id)}&descricao=${encodeURIComponent(novaDescricao)}`,
    credentials: 'include'
  })
  .then(res => res.json())
  .then(data => {
  if (data.success || data.sucesso) {
    const p = document.createElement('p');
    p.className = 'text-white descricao_texto';
    p.dataset.id = id;
    p.textContent = novaDescricao;
    textarea.replaceWith(p);

    btnSalvar.style.display = 'none';
    card.querySelector('.btn-warning').style.display = 'inline-block';
  } else {
    alert(data.message || data.mensagem || "Erro ao salvar a descrição.");
  }
})

  .catch(err => {
    console.error("Erro ao salvar descrição:", err);
    alert("Erro de conexão ao salvar descrição.");
  });
}

function verNoMapa(lat, lng) {
  window.location.href = `page1.php?lat=${lat}&lng=${lng}&zoom=16`;
}


</script>


</div>
<!-- // ------------------------------------------------------------------------------------ -->
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q " crossorigin="anonymous "></script>
<!-- <script src="./js/script.js "></script> -->
</body>

</html>

