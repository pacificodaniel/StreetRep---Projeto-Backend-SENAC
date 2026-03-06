/**
 * page1.js
 * Todo o JavaScript do page1 separado do HTML/PHP.
 * Depende de Leaflet (L) e turf (se necessário) — ambos devem estar carregados antes deste arquivo.
 *
 * Estrutura:
 *  - Ler dados passados pelo PHP (window.AppData)
 *  - Inicializar mapa
 *  - Ícones e utilitários (escapeHtml)
 *  - Funções de comentários/avaliações
 *  - Busca de endereço
 *  - Carregamento de ocorrências + marcadores + linhas de alerta
 *  - Filtros de gravidade
 *  - Formulário de registro de ocorrência (click no mapa)
 */

// ==================================================
// Ler variáveis passadas pelo PHP
// ==================================================
const usuarioVerificado = window.AppData && window.AppData.usuarioVerificado === true;

// ==================================================
// IIFE para isolar o escopo
// ==================================================
(function() {
    // Garantir que tudo execute após o DOM e após bibliotecas estarem prontas
document.addEventListener('DOMContentLoaded', () => {




        
        // ----------------------
        // Inicialização do mapa
        // ----------------------
        const map = L.map('map').setView([-23.5505, -46.6333], 13); // São Paulo

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

                    // -------------------------------
            // FOCO AUTOMÁTICO VIA URL
            // -------------------------------
            (function () {
            const params = new URLSearchParams(window.location.search);
            const lat = parseFloat(params.get('lat'));
            const lng = parseFloat(params.get('lng'));
            const zoom = parseInt(params.get('zoom')) || 16;

            if (isNaN(lat) || isNaN(lng)) return;

            // Espera o mapa existir
            if (typeof map === 'undefined') {
                console.warn('Mapa não encontrado');
                return;
            }

            map.setView([lat, lng], zoom);

            L.marker([lat, lng])
                .addTo(map)
                .bindPopup('Local da ocorrência')
                .openPopup();
            })();




        // Tornar map global neste arquivo (algumas funções referenciam plain `map`)
        window._page1_map = map;

        // Tentar centralizar o mapa na localização do usuário fornecida pelo navegador
        // Somente fazer isso se não houver parâmetros lat/lng na URL (ou seja, não sobrescrever um foco explícito)
        (function tryBrowserGeolocation() {
            try {
                const params = new URLSearchParams(window.location.search);
                const pLat = parseFloat(params.get('lat'));
                const pLng = parseFloat(params.get('lng'));

                // Se já existe lat/lng na URL, não tentar geolocalização
                if (!isNaN(pLat) && !isNaN(pLng)) return;

                if (!('geolocation' in navigator)) {
                    console.info('Geolocation não disponível no navegador');
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Ajusta o mapa para a posição do usuário e adiciona um marcador indicativo *29/01/2026
                    try {
                        map.setView([lat, lng], 15);
                        L.marker([lat, lng]).addTo(map).bindPopup('Você está aqui').openPopup();
                    } catch (err) {
                        console.warn('Erro ao aplicar localização no mapa:', err);
                    }
                }, function(err) {
                    console.warn('Geolocation falhou ou foi negada pelo usuário:', err && err.message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 5 * 60 * 1000
                });
            } catch (e) {
                console.error('Erro ao tentar geolocalizar:', e);
            }
        })();

        // ----------------------
        // Ícone customizado (redIcon)
        // ----------------------
        const redIcon = L.icon({
            iconUrl: './img/map-marker2.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // ----------------------
        // Utilitários
        // ----------------------
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        // ----------------------
        // Comentários / Avaliações
        // ----------------------
        function adicionarComentario(id_ocorrencia, tipo) {
            const comentarioEl = document.getElementById('novoComentario');
            const comentario = comentarioEl ? comentarioEl.value.trim() : '';
            if (!comentario) return alert('Digite um comentário.');

            fetch('registrar_avaliacao.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_ocorrencia: id_ocorrencia,
                        tipo: tipo,
                        comentario: comentario
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        alert(data.mensagem);

                        // Atualiza os contadores no popup:
                        const posEl = document.getElementById(`pos-count-${id_ocorrencia}`);
                        const negEl = document.getElementById(`neg-count-${id_ocorrencia}`);

                        if (posEl && negEl) {
                            posEl.textContent = data.aval_positivo ?? 0;
                            negEl.textContent = data.aval_negativo ?? 0;
                        }

                        // limpar comentário
                        if (document.getElementById('novoComentario')) {
                            document.getElementById('novoComentario').value = '';
                        }
                    } else {
                        alert(data.mensagem || 'Erro ao registrar avaliação.');
                    }
                })
                .catch(err => {
                    console.error('Erro ao enviar avaliação:', err);
                    alert('Erro inesperado. Veja o console.');
                });
        }

        function abrirComentarios(id_ocorrencia) {
            fetch(`listar_comentarios.php?id_ocorrencia=${id_ocorrencia}`, { credentials: 'include' })
                .then(res => {
                    if (!res.ok) return res.text().then(t => { throw new Error(t || res.statusText); });
                    return res.json();
                })
                .then(data => {
                    const titulo = data.titulo || `Ocorrência ${id_ocorrencia}`;
                    const comentarios = data.comentarios || [];

                    let html = `<h4>💬 ${escapeHtml(titulo)}</h4>`;

                    html += `<div class="comentarios-container">`;
                    if (comentarios.length === 0) {
                        html += '<p class="sem-comentarios">Nenhum comentário ainda. Seja o primeiro!</p>';
                    } else {
                        comentarios.forEach(c => {
                            const nome = c.usuario || 'Anônimo';
                            let emoji = '💭';
                            let badgeClass = '';
                            if (c.tipo === 'positivo') {
                                emoji = '✅';
                                badgeClass = 'badge-success';
                            } else if (c.tipo === 'negativo') {
                                emoji = '❌';
                                badgeClass = 'badge-danger';
                            }
                            html += `
                <p>
                    <strong>${escapeHtml(nome)}</strong> 
                    <em>${emoji}</em><br>
                    <small style="color: #aaa;">
                        ${escapeHtml(c.comentario)}
                    </small>
                </p>
                            `;
                        });
                    }
                    html += `</div>`;

                    html += `
            <label class="text-warning fw-bold" style="font-size: 13px; text-transform: uppercase;">Seu comentário:</label>
            <select id="tipoComentario" class="form-select mb-2">
              <option value="positivo">✅ Concordo / Verdadeiro</option>
              <option value="negativo">❌ Discordo / Falso</option>
            </select>
            <textarea id="novoComentario" class="form-control mb-2" placeholder="Compartilhe sua avaliação..." maxlength="500"></textarea>
            <button id="btnEnviarComentario" class="btn btn-warning">
                <i class="fas fa-paper-plane"></i> Enviar Avaliação
            </button>
            <button id="btnFecharComentario" class="btn btn-secondary">
                <i class="fas fa-times"></i> Fechar
            </button>
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
                })
                .catch(err => {
                    console.error('Erro abrirComentarios:', err);
                    alert('Erro ao carregar os comentários. Veja o console.');
                });
        }

        function fecharComentarios() {
            const div = document.getElementById('comentariosDiv');
            if (div) div.style.display = 'none';
        }

        // Expor abrirComentarios para ser chamada de outros lugares
        window.abrirComentarios = abrirComentarios;

        // ----------------------
        // Filtro por gravidade
        // ----------------------
        const markers = []; // array com todos os marcadores (adicionados abaixo)
        function filtrarPorTipo(tipoSelecionado) {
            markers.forEach(marker => {
                if (tipoSelecionado === 'todos' || marker.gravidade === tipoSelecionado) {
                    marker.addTo(map);
                } else {
                    if (map.hasLayer(marker)) map.removeLayer(marker);
                }
            });
        }

        const filtroTipoEl = document.getElementById('filtroTipo');
        if (filtroTipoEl) {
            filtroTipoEl.addEventListener('change', (e) => {
                filtrarPorTipo(e.target.value);
            });
        }

        // ----------------------
        // Busca por endereço (Nominatim)
        // ----------------------
        const buscarEnderecoBtn = document.getElementById('buscarEndereco');
        if (buscarEnderecoBtn) {
            buscarEnderecoBtn.addEventListener('click', () => {
                const endereco = document.getElementById('endereco').value;
                if (!endereco) return alert('Digite um endereço');

                fetch(`buscar_endereco.php?q=${encodeURIComponent(endereco)}`)

                    .then(res => res.json())
                    .then(data => {
                        if (data.length === 0) return alert('Endereço não encontrado');
                        const local = data[0];
                        const lat = parseFloat(local.lat);
                        const lng = parseFloat(local.lon);

                        // Atualiza o form
                        const latEl = document.getElementById('lat');
                        const lngEl = document.getElementById('lng');
                        if (latEl) latEl.value = lat;
                        if (lngEl) lngEl.value = lng;

                        // Move o mapa e o form
                        map.setView([lat, lng], 15);
                        const point = map.latLngToContainerPoint([lat, lng]);
                        const formBox = document.getElementById('formOcorrencia');
                        if (formBox) {
                            formBox.style.left = point.x + 'px';
                            formBox.style.top = point.y + 'px';
                        }
                    });
            });
        }

                                        document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-denunciar');
    if (!btn) return;

    e.preventDefault();

    const confirmou = confirm('Tem certeza que deseja denunciar esta ocorrência?');

    if (confirmou) {
        const id = btn.dataset.id;
        console.log("Denúncia confirmada para ID:", id);
    }
});
















        // ----------------------
        // Carregar ocorrências do servidor e criar marcadores
        // ----------------------
        const markersRedLines = []; // linhas vermelhas desenhadas entre ocorrências

        LoadingSpinner.show('Carregando ocorrências...');

        fetch('listar_ocorrencias.php')
            .then(res => res.json())
            .then(ocorrencias => {
                // adiciona marcadores e popula markers[]
                ocorrencias.forEach(o => {
                    const marker = L.marker([o.latitude, o.longitude], { icon: redIcon });

                    // guardar dados no marker para facilitar atualização
                    marker.ocorrenciaId = o.id;
                    marker.titulo = o.titulo;
                    marker.gravidade = o.gravidade;
                    marker.descricao = o.descricao;
                    marker.aval_positivo = o.aval_positivo ?? 0;
                    marker.aval_negativo = o.aval_negativo ?? 0;
                    marker.imagem = o.imagem ?? null;


                    function gerarPopupHTML(m) {
                         const imagemHTML = m.imagem
        ? `<img src="${escapeHtml(m.imagem)}"
                alt="Imagem da ocorrência"
                style="width:100%; max-height:180px; object-fit:cover; border-radius:6px; margin:6px 0;">`
        : '';
                        return `
              <strong> ${escapeHtml(m.titulo)}</strong>
              ${imagemHTML}
              <em>Gravidade: ${escapeHtml(m.gravidade)}</em>
              <p>${escapeHtml(m.descricao)}</p>
              <div class="stats">
                <div class="stat-item">
                  <span class="cont_pos_ocor" id="pos-count-${m.ocorrenciaId}">👍 ${m.aval_positivo}</span>
                </div>
                <div class="stat-item">
                  <span class="cont_neg_ocor" id="neg-count-${m.ocorrenciaId}">👎 ${m.aval_negativo}</span>
                </div>
              </div>
              <button class="btn-avaliacoes btn btn-warning" data-id="${m.ocorrenciaId}">
                <i class="fas fa-comments"></i> Comentários / Avaliar
                
              </button>
              <button class="btn btn-sm btn-warning btn-denunciar" id="btn-denunciar-popup" id="b"data-id="${m.ocorrenciaId}">
               <i class="fa-solid fa-triangle-exclamation"></i> Denunciar
              </button>

            `;
    

                    }
                    

                    marker.bindPopup(gerarPopupHTML(marker));
                    marker.addTo(map);
                    markers.push(marker);
                });

                // --- Conexões entre ocorrências de gravidade "alto" próximas ---
                const o_gravidade_alta = ocorrencias.filter(o => o.gravidade === "alto");
                const DISTANCIA_PROXIMA = 50; // pixels
                let clusters = [];

                // Agrupar ocorrências próximas
                o_gravidade_alta.forEach(o => {
                    let added = false;
                    clusters.forEach(cluster => {
                        if (cluster.some(c => map.latLngToLayerPoint([c.latitude, c.longitude])
                                .distanceTo(map.latLngToLayerPoint([o.latitude, o.longitude])) <= DISTANCIA_PROXIMA)) {
                            cluster.push(o);
                            added = true;
                        }
                    });
                    if (!added) clusters.push([o]);
                });

                // Filtrar apenas clusters com 4 ou mais ocorrências
                clusters = clusters.filter(c => c.length >= 4);

                // Conectar cada ocorrência à ocorrência mais próxima dentro da distância, sem duplicar
                clusters.forEach(cluster => {
                    const drawnPairs = new Set();
                    cluster.forEach(p1 => {
                        let minDist = Infinity;
                        let closest = null;
                        cluster.forEach(p2 => {
                            if (p1 === p2) return;
                            const d = map.latLngToLayerPoint([p1.latitude, p1.longitude])
                                .distanceTo(map.latLngToLayerPoint([p2.latitude, p2.longitude]));
                            if (d < minDist && d <= DISTANCIA_PROXIMA) {
                                minDist = d;
                                closest = p2;
                            }
                        });

                        if (closest) {
                            const key = [p1.id, closest.id].sort().join('-');
                            if (!drawnPairs.has(key)) {
                                drawnPairs.add(key);
                                const line = L.polyline(
                                    [
                                        [p1.latitude, p1.longitude],
                                        [closest.latitude, closest.longitude]
                                    ], { color: 'red', weight: 100, opacity: 0.3, smoothFactor: 1 }
                                ).addTo(map);
                                markersRedLines.push(line);
                            }
                        }
                    });
                });

                // Ajuste das linhas de acordo com o zoom
                map.on('zoomend', () => {
                    const zoom = map.getZoom();
                    markersRedLines.forEach(line => {
                        if (zoom < 13) {
                            line.setStyle({ opacity: 0 });
                        } else if (zoom < 15) {
                            line.setStyle({ weight: 3, opacity: 0.3 });
                        } else {
                            line.setStyle({ weight: 90, opacity: 0.3 });
                        }
                    });
                });

                // Delegação de evento para os botões do popup
                map.on('popupopen', function(e) {
                    const popupNode = e.popup._contentNode;
                    const btn = popupNode.querySelector('.btn-avaliacoes');
                    if (btn) {
                        btn.addEventListener('click', function() {
                            const id_ocorrencia = parseInt(this.dataset.id, 10);
                            abrirComentarios(id_ocorrencia);
                        });
                    }
                });
            })
            .catch(err => {
                console.error('Erro ao carregar ocorrências:', err);
            })
             .finally(() => {
        LoadingSpinner.hide();
    });

        // ----------------------
        // Função para atualizar contadores (expor publicamente)
        // ----------------------
        function atualizarContadores(id_ocorrencia, avalPos, avalNeg) {
            const marker = markers.find(m => m.ocorrenciaId === id_ocorrencia);
            if (!marker) return;

            marker.aval_positivo = avalPos;
            marker.aval_negativo = avalNeg;

            // Atualiza o conteúdo do popup se estiver aberto
            const popup = marker.getPopup && marker.getPopup();
            if (popup && popup.isOpen && popup.isOpen()) {
                marker.setPopupContent(`
          <strong>📍 ${escapeHtml(marker.titulo)}</strong>
          <em>Gravidade: ${escapeHtml(marker.gravidade)}</em>
          <p>${escapeHtml(marker.descricao)}</p>
          <div class="stats">
            <div class="stat-item">
              <span id="pos-count-${marker.ocorrenciaId}">✅ ${marker.aval_positivo}</span>
            </div>
            <div class="stat-item">
              <span id="neg-count-${marker.ocorrenciaId}">❌ ${marker.aval_negativo}</span>
            </div>
          </div>
          <button class="btn-avaliacoes btn btn-warning" data-id="${marker.ocorrenciaId}">
            <i class="fas fa-comments"></i> Comentários / Avaliar
          </button>
        `);
            }
        }

        // tornar disponível globalmente caso outro script precise chamar
        window.atualizarContadores = atualizarContadores;

        // ----------------------
        // Interação: clicar no mapa para abrir formulário
        // ----------------------
        (function setupMapClickAndForm() {
            const formBox = document.getElementById('formOcorrencia');
            const form = document.getElementById('ocorrenciaForm');
            const btnCancelar = document.getElementById('cancelar');

            if (!formBox || !form || !btnCancelar) return;

            let tempMarker = null;

            map.on('click', function(e) {
                if (!usuarioVerificado) {
                    alert('Apenas usuários verificados podem criar ocorrências.');
                    return;
                }

                if (tempMarker) map.removeLayer(tempMarker);

                tempMarker = L.marker([e.latlng.lat, e.latlng.lng], { opacity: 0.6 }).addTo(map);
                tempMarker.bindPopup('<i style="margin-top:20px;">Foi aqui.</i>').openPopup();

                const latEl = document.getElementById('lat');
                const lngEl = document.getElementById('lng');
                if (latEl) latEl.value = e.latlng.lat;
                if (lngEl) lngEl.value = e.latlng.lng;

                const point = map.latLngToContainerPoint(e.latlng);
                formBox.style.left = (point.x + 10) + 'px';
                formBox.style.top = (point.y + 10) + 'px';
                formBox.style.display = 'block';
            });

            btnCancelar.addEventListener('click', function() {
                formBox.style.display = 'none';
                form.reset();
            });

            form.addEventListener('submit', function(e) {
    e.preventDefault();

    if (!confirm('Tem certeza que deseja registrar essa ocorrência?')) return;

    const formData = new FormData(form);

    fetch('registrar_ocorrencia.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(resp => {
        alert(resp.mensagem);
        formBox.style.display = 'none';
        form.reset();
        

        const lat = document.getElementById('lat').value;
const lng = document.getElementById('lng').value;

const marker = L.marker([lat, lng]).addTo(map);
marker.bindPopup(
    `<strong>${document.getElementById('titulo').value}</strong><br>` +
    `${document.getElementById('descricao').value}`
);

    })
    .catch(err => {
        console.error(err);
        alert('Erro ao registrar ocorrência.');
    });
});

        })();















  //Preview da imagem na criação da ocorrência. 

 const inputImagem = document.getElementById("imagem");
  const preview = document.getElementById("preview_imagem");


//Evita que a imagem reapareça quando o formulário for limpo
       const form = document.getElementById("ocorrenciaForm");

if (form && preview) {
    form.addEventListener("reset", function () {
        preview.src = "";
        preview.style.display = "none";
    });
}

//------------------------------------------------------
        

    if (inputImagem && preview) {
        inputImagem.addEventListener("change", function () {

            const arquivo = this.files[0];

            if (!arquivo) {
                preview.style.display = "none";
                preview.src = "";
               
                return;
            }

            if (!arquivo.type.startsWith("image/")) {
                alert("Selecione uma imagem válida.");
                this.value = "";
                preview.style.display = "none";
                return;
            }

            const url = URL.createObjectURL(arquivo);

            preview.src = url;
            preview.style.display = "block";
        });


    }
        






        
        // ----------------------
        // Fim do DOMContentLoaded
        // ----------------------
    }); // end DOMContentLoaded
})(); // end IIFE

  
