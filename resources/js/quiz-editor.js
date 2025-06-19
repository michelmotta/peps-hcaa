import Swal from 'sweetalert2';

let avaliacaoPreCarregada = [];
let perguntaCount = 0;

function criarRespostaHtml(perguntaIndex, respostaIndex) {
    const letra = String.fromCharCode(65 + respostaIndex);
    return `
        <div class="input-group mb-2 resposta-item" data-resposta-index="${respostaIndex}">
            <span class="input-group-text">${letra}.</span>
            <input type="text" class="form-control" name="resposta-${letra}-${perguntaIndex}" required>
            <div class="input-group-text">
                <input class="form-check-input mt-0" type="radio" name="correta-${perguntaIndex}" value="${letra}" required>
                <label class="ms-1 small">Correta?</label>
            </div>
            <button type="button" class="btn btn-danger btn-sm remover-resposta" title="Remover resposta">×</button>
        </div>
    `;
}

function criarPerguntaHtml(index) {
    return `
        <div class="accordion-item pergunta-item" data-pergunta-index="${index}">
            <h2 class="accordion-header" id="heading-${index}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-${index}" aria-expanded="true" aria-controls="collapse-${index}">
                    <strong>Questão Nº ${index + 1}</strong>
                </button>
            </h2>
            <div id="collapse-${index}" class="accordion-collapse collapse show" aria-labelledby="heading-${index}">
                <div class="accordion-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-outline-danger btn-sm remover-pergunta" title="Remover pergunta">
                            <i class="bi bi-trash me-2 icon-xs"></i>Excluir Questão
                        </button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-3">Questão:</label>
                        <input type="text" class="form-control" name="pergunta-${index}" required>
                    </div>
                    <div class="mb-3 respostas-container">
                        ${criarRespostaHtml(index, 0)}
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm adicionar-resposta" data-pergunta-index="${index}">
                        <i class="bi bi-plus-circle me-2 icon-xs"></i>Adicionar Alternativa
                    </button>
                </div>
            </div>
        </div>
    `;
}

function carregarAvaliacoesSalvas(dados) {
    const container = document.getElementById('perguntasContainer');
    if (!Array.isArray(dados) || !container) return;

    dados.forEach((item) => {
        const indexAtual = perguntaCount++;
        const html = criarPerguntaHtml(indexAtual);
        container.insertAdjacentHTML('beforeend', html);

        const perguntaItem = container.querySelector(`.pergunta-item[data-pergunta-index="${indexAtual}"]`);
        perguntaItem.querySelector(`input[name="pergunta-${indexAtual}"]`).value = item.question;

        const respostasContainer = perguntaItem.querySelector('.respostas-container');
        respostasContainer.innerHTML = '';

        item.options.forEach((resposta, respostaIndex) => {
            // Novo código para extrair letra e texto da opção no novo formato
            const letra = Object.keys(resposta)[0];      // ex: "A"
            const texto = resposta[letra];               // ex: "Antibióticos"

            const respostaHtml = criarRespostaHtml(indexAtual, respostaIndex);
            respostasContainer.insertAdjacentHTML('beforeend', respostaHtml);

            const respostaItem = respostasContainer.querySelector(`.resposta-item[data-resposta-index="${respostaIndex}"]`);
            respostaItem.querySelector('input[type="text"]').value = texto;

            if (letra === item.correct) {
                respostaItem.querySelector('input[type="radio"]').checked = true;
            }
        });
    });
}

function reordenarRespostas(container, perguntaIndex) {
    const respostaItems = container.querySelectorAll('.resposta-item');
    respostaItems.forEach((respostaEl, idx) => {
        const novaLetra = String.fromCharCode(65 + idx);
        respostaEl.setAttribute('data-resposta-index', idx);
        respostaEl.querySelector('.input-group-text').textContent = `${novaLetra}.`;
        respostaEl.querySelector('input[type="text"]').setAttribute('name', `resposta-${novaLetra}-${perguntaIndex}`);

        const radio = respostaEl.querySelector('input[type="radio"]');
        radio.setAttribute('value', novaLetra);
        radio.setAttribute('name', `correta-${perguntaIndex}`);
    });
}

function reordenarPerguntas(container) {
    const perguntas = container.querySelectorAll('.pergunta-item');
    perguntas.forEach((perguntaEl, novoIndex) => {
        perguntaEl.setAttribute('data-pergunta-index', novoIndex);
        const btn = perguntaEl.querySelector('.accordion-button');
        btn.textContent = `Pergunta #${novoIndex + 1}`;

        const heading = perguntaEl.querySelector('.accordion-header');
        const collapse = perguntaEl.querySelector('.accordion-collapse');
        heading.id = `heading-${novoIndex}`;
        btn.setAttribute('data-bs-target', `#collapse-${novoIndex}`);
        btn.setAttribute('aria-controls', `collapse-${novoIndex}`);
        collapse.id = `collapse-${novoIndex}`;
        collapse.setAttribute('aria-labelledby', `heading-${novoIndex}`);

        const inputPergunta = perguntaEl.querySelector('input[name^="pergunta-"]');
        inputPergunta.setAttribute('name', `pergunta-${novoIndex}`);

        const btnAdicionarResposta = perguntaEl.querySelector('.adicionar-resposta');
        btnAdicionarResposta.setAttribute('data-pergunta-index', novoIndex);

        const respostasContainer = perguntaEl.querySelector('.respostas-container');
        reordenarRespostas(respostasContainer, novoIndex);
    });
}

const btnAdicionarPergunta = document.getElementById('adicionarPergunta');
if (btnAdicionarPergunta) {
    btnAdicionarPergunta.addEventListener('click', () => {
        if (perguntaCount >= 5) {
            Swal.fire({
                icon: 'warning',
                title: 'Limite atingido!',
                text: 'No máximo 5 questões por tópico.',
                confirmButtonText: 'OK',
            });
            return;
        }

        const container = document.getElementById('perguntasContainer');
        if (!container) return;

        const indexAtual = perguntaCount++;
        const html = criarPerguntaHtml(indexAtual);
        container.insertAdjacentHTML('beforeend', html);
    });
}

const containerPerguntas = document.getElementById('perguntasContainer');
if (containerPerguntas) {
    containerPerguntas.addEventListener('click', (event) => {
        const container = document.getElementById('perguntasContainer');
        const target = event.target.closest('button');
        if (!target) return;

        if (target.classList.contains('adicionar-resposta')) {
            const perguntaItem = target.closest('.pergunta-item');
            const perguntaIndex = parseInt(perguntaItem.getAttribute('data-pergunta-index'));
            const respostasContainer = perguntaItem.querySelector('.respostas-container');
            const totalRespostas = respostasContainer.querySelectorAll('.resposta-item').length;

            if (totalRespostas >= 4) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limite atingido!',
                    text: 'No máximo 4 alternativas possíveis.',
                    confirmButtonText: 'OK',
                });
                return;
            }

            const respostaHtml = criarRespostaHtml(perguntaIndex, totalRespostas);
            respostasContainer.insertAdjacentHTML('beforeend', respostaHtml);
        }

        if (target.classList.contains('remover-resposta')) {
            const perguntaItem = target.closest('.pergunta-item');
            const respostasContainer = perguntaItem.querySelector('.respostas-container');
            target.closest('.resposta-item').remove();
            const perguntaIndex = parseInt(perguntaItem.getAttribute('data-pergunta-index'));
            reordenarRespostas(respostasContainer, perguntaIndex);
        }

        if (target.classList.contains('remover-pergunta')) {
            target.closest('.pergunta-item').remove();
            perguntaCount = Math.max(0, perguntaCount - 1);
            reordenarPerguntas(container);
        }
    });
}

const btnSalvar = document.getElementById('salvar-avaliacao');

if (btnSalvar) {
    btnSalvar.addEventListener('click', () => {
        const perguntasData = [];
        let hasEmptyField = false;

        document.querySelectorAll('.pergunta-item').forEach((pergunta, i) => {
            const perguntaInput = pergunta.querySelector(`input[name="pergunta-${i}"]`);
            const perguntaTexto = perguntaInput?.value.trim() || '';
            const options = [];
            let correct = null;

            // Check question input
            if (!perguntaTexto) {
                hasEmptyField = true;
                if (perguntaInput) perguntaInput.style.borderColor = 'red';
            } else {
                if (perguntaInput) perguntaInput.style.borderColor = '';
            }

            pergunta.querySelectorAll('.resposta-item').forEach((respostaEl) => {
                const input = respostaEl.querySelector('input[type="text"]');
                const radio = respostaEl.querySelector('input[type="radio"]');
                const texto = input?.value.trim() || '';
                const option = radio?.value;

                // Check answer input
                if (!texto) {
                    hasEmptyField = true;
                    if (input) input.style.borderColor = 'red';
                } else {
                    if (input) input.style.borderColor = '';
                }

                if (radio?.checked) {
                    correct = option;
                }

                options.push({ [option]: texto });
            });

            perguntasData.push({
                question: perguntaTexto,
                options,
                correct
            });
        });

        if (hasEmptyField) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos não preenchidos!',
                text: 'Atenção! Um ou mais campos não foram preenchidos.',
                confirmButtonText: 'OK',
            });
            return;
        }

        const avaliacaoJson = document.getElementById('avaliacaoJson');
        if (avaliacaoJson) {
            avaliacaoJson.value = JSON.stringify(perguntasData);
        }

        console.log(perguntasData);

        const modal = bootstrap.Modal.getInstance(document.getElementById('avaliacaoModal'));
        if (modal) {
            modal.hide();
        }
    });
}

export function initQuizEditor(data = []) {
    avaliacaoPreCarregada = data;
    if (avaliacaoPreCarregada.length > 0) {
        carregarAvaliacoesSalvas(avaliacaoPreCarregada);
    }
}
