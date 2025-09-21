import Swal from 'sweetalert2';

class QuizStudio {
    constructor() {
        this.navContainer = document.getElementById('perguntasNav');
        this.contentContainer = document.getElementById('perguntasContent');
        this.emptyState = document.getElementById('emptyState');
        this.addQuestionBtn = document.getElementById('adicionarPergunta');
        this.saveBtn = document.getElementById('salvar-avaliacao');
        this.jsonInput = document.getElementById('avaliacaoJson');
        this.modalElement = document.getElementById('avaliacaoModal');
    }

    init(initialData = []) {
        if (!this.navContainer || !this.contentContainer) return;
        this._setupEventListeners();
        this.load(initialData);
        this._updateEmptyState();
    }

    _setupEventListeners() {
        this.addQuestionBtn?.addEventListener('click', () => this._addQuestion());
        this.saveBtn?.addEventListener('click', () => this._saveQuiz());

        this.navContainer.addEventListener('click', e => {
            const navLink = e.target.closest('.nav-link');
            if (navLink) {
                e.preventDefault();
                this._setActiveQuestion(navLink.dataset.index);
            }
        });

        this.contentContainer.addEventListener('click', e => this._handleContentClick(e));
    }

    _handleContentClick(e) {
        const button = e.target.closest('button');
        if (!button) return;

        const pane = button.closest('.question-pane');
        const index = parseInt(pane.dataset.index);

        if (button.classList.contains('remover-pergunta')) {
            this._removeQuestion(index);
        }
        if (button.classList.contains('adicionar-resposta')) {
            const respostasContainer = pane.querySelector('.respostas-container');
            const totalRespostas = respostasContainer.children.length;
            if (totalRespostas >= 4) {
                Swal.fire({ icon: 'warning', title: 'Limite atingido!', text: 'No máximo 4 alternativas.' });
                return;
            }
            respostasContainer.insertAdjacentHTML('beforeend', this._createAnswerHtml(index, totalRespostas));
        }
        if (button.classList.contains('remover-resposta')) {
            button.closest('.resposta-item').remove();
            this._reorderAnswers(pane.querySelector('.respostas-container'), index);
        }
    }

    _addQuestion() {
        const newIndex = this.navContainer.children.length;
        if (newIndex >= 5) {
            Swal.fire({ icon: 'warning', title: 'Limite atingido!', text: 'No máximo 5 questões.' });
            return;
        }

        this.navContainer.insertAdjacentHTML('beforeend', this._createSidebarItemHtml(newIndex));
        this.contentContainer.insertAdjacentHTML('beforeend', this._createContentPaneHtml(newIndex));

        this._setActiveQuestion(newIndex);
        this._updateEmptyState();
    }

    _removeQuestion(index) {
        this.navContainer.querySelector(`.nav-link[data-index="${index}"]`)?.parentElement.remove();
        this.contentContainer.querySelector(`.question-pane[data-index="${index}"]`)?.remove();

        this._reorderAndSync();

        const firstNavLink = this.navContainer.querySelector('.nav-link');
        if (firstNavLink) {
            this._setActiveQuestion(firstNavLink.dataset.index);
        } else {
            this._updateEmptyState();
        }
    }

    _setActiveQuestion(index) {
        this.navContainer.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
        this.contentContainer.querySelectorAll('.question-pane').forEach(el => el.classList.remove('active'));

        const navLink = this.navContainer.querySelector(`.nav-link[data-index="${index}"]`);
        const pane = this.contentContainer.querySelector(`.question-pane[data-index="${index}"]`);

        if (navLink && pane) {
            navLink.classList.add('active');
            pane.classList.add('active');
        }
        this._updateEmptyState();
    }

    _updateEmptyState() {
        const hasQuestions = this.navContainer.children.length > 0;
        this.emptyState.classList.toggle('active', !hasQuestions);
    }

    _reorderAndSync() {
        const navLinks = this.navContainer.querySelectorAll('.nav-link');
        const panes = this.contentContainer.querySelectorAll('.question-pane');

        navLinks.forEach((navLink, idx) => {
            navLink.dataset.index = idx;
            navLink.textContent = `Questão ${idx + 1}`;
        });

        panes.forEach((pane, idx) => {
            pane.dataset.index = idx;
            pane.querySelector('[name^="pergunta-"]').name = `pergunta-${idx}`;
            this._reorderAnswers(pane.querySelector('.respostas-container'), idx);
        });
    }

    _reorderAnswers(container, questionIndex) {
        container.querySelectorAll('.resposta-item').forEach((answerEl, idx) => {
            const letter = String.fromCharCode(65 + idx);
            answerEl.querySelector('input[type="radio"]').name = `correta-${questionIndex}`;
            answerEl.querySelector('.input-group-text.fw-bold').textContent = `${letter}.`;
        });
    }

    load(data) {
        this.navContainer.innerHTML = '';
        this.contentContainer.innerHTML = '';
        if (!data || data.length === 0) return;

        data.forEach((item, index) => {
            this.navContainer.insertAdjacentHTML('beforeend', this._createSidebarItemHtml(index));
            this.contentContainer.insertAdjacentHTML('beforeend', this._createContentPaneHtml(index));

            const pane = this.contentContainer.querySelector(`.question-pane[data-index="${index}"]`);
            pane.querySelector(`[name="pergunta-${index}"]`).value = item.question;

            const answersContainer = pane.querySelector('.respostas-container');
            answersContainer.innerHTML = '';

            item.options.forEach((answer, answerIndex) => {
                answersContainer.insertAdjacentHTML('beforeend', this._createAnswerHtml(index, answerIndex));
                const answerItem = answersContainer.querySelector(`.resposta-item:last-child`);
                const letter = Object.keys(answer)[0];
                answerItem.querySelector('input[type="text"]').value = answer[letter];
                if (letter === item.correct) {
                    answerItem.querySelector('input[type="radio"]').checked = true;
                }
            });
        });

        this._setActiveQuestion(0);
    }

    _saveQuiz() {
        const questionsData = [];
        let hasEmptyField = false;

        this.contentContainer.querySelectorAll('.question-pane').forEach((pane, index) => {
            const questionInput = pane.querySelector(`[name="pergunta-${index}"]`);
            const questionText = questionInput?.value.trim() || '';

            if (!questionText) { hasEmptyField = true; questionInput?.classList.add('is-invalid'); } else { questionInput?.classList.remove('is-invalid'); }

            const options = [];
            let correct = null;
            pane.querySelectorAll('.resposta-item').forEach(answerEl => {
                const textInput = answerEl.querySelector('input[type="text"]');
                const radioInput = answerEl.querySelector('input[type="radio"]');
                const text = textInput?.value.trim() || '';
                const option = radioInput?.value;
                if (!text) { hasEmptyField = true; textInput?.classList.add('is-invalid'); } else { textInput?.classList.remove('is-invalid'); }
                if (radioInput?.checked) correct = option;
                options.push({ [option]: text });
            });
            if (!correct) { hasEmptyField = true; }
            questionsData.push({ question: questionText, options, correct });
        });

        if (hasEmptyField) {
            Swal.fire({ icon: 'error', title: 'Campos Incompletos!', text: 'Preencha todos os campos e marque uma resposta correta para cada questão.' });
            return;
        }

        this.jsonInput.value = JSON.stringify(questionsData);
        bootstrap.Modal.getInstance(this.modalElement)?.hide();
    }

    _createAnswerHtml(questionIndex, answerIndex) {
        const letter = String.fromCharCode(65 + answerIndex);
        return `
            <div class="input-group mb-3 resposta-item">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="radio" name="correta-${questionIndex}" value="${letter}" title="Marcar como correta" required>
                    <span class="ms-1">Correta?</span>
                </div>
                <span class="input-group-text fw-bold bg-light">${letter}.</span>
                <input type="text" class="form-control" placeholder="Texto da alternativa">
                <button type="button" class="btn btn-danger remover-resposta" title="Remover alternativa">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        `;
    }

    _createSidebarItemHtml(index) {
        return `
            <li class="nav-item">
                <a class="nav-link" href="#" data-index="${index}">Questão ${index + 1}</a>
            </li>
        `;
    }

    _createContentPaneHtml(index) {
        return `
            <div class="question-pane" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                     <h2 class="mb-0">Questão ${index + 1}</h2>
                     <button type="button" class="btn btn-danger remover-pergunta" title="Remover pergunta">
                        <i class="bi bi-trash me-1"></i> Excluir Questão
                    </button>
                </div>
                <div class="mb-3">
                    <h3>Enunciado:</h3>
                    <textarea class="form-control" name="pergunta-${index}" rows="4" placeholder="Digite o enunciado da questão aqui..."></textarea>
                </div>
                <hr class="my-4">
                <h4 class="mb-3">Alternativas:</h4>
                <div class="respostas-container">
                    ${this._createAnswerHtml(index, 0)}
                    ${this._createAnswerHtml(index, 1)}
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm adicionar-resposta">
                    <i class="bi bi-plus-circle me-1"></i>Adicionar Alternativa
                </button>
            </div>
        `;
    }
}

export function initQuizEditor(data = []) {
    const studio = new QuizStudio();
    studio.init(data);
}