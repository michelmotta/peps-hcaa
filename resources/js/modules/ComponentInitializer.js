import TomSelect from 'tom-select';
import "tom-select/dist/css/tom-select.bootstrap5.css";
import IMask from 'imask';
import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

export class ComponentInitializer {
    static init() {
        this.initTagify();
        this.initTomSelect();
        this.initInputMasks();
        this.initImagePreview();
        this.initSubspecialtyManager();
        this.initQuillSync();
    }

    static initTagify() {
        const tagifyInput = document.querySelector('#subspecialties');
        if (tagifyInput) {
            new Tagify(tagifyInput);
        }
    }

    static initTomSelect() {
        const configs = [
            { selector: '#user-select', endpoint: '/dashboard/users/ajax', placeholder: 'Pesquisar pelo nome' },
            { selector: '#professor-select', endpoint: '/dashboard/professors/ajax', placeholder: 'Pesquisar pelo nome' },
            { selector: '#lesson-select', endpoint: '/dashboard/lessons/ajax', placeholder: 'Pesquisar pelo nome da aula' }
        ];

        const tomSelectPtBr = {
            render: {
                loading: () => '<div class="p-2 text-muted">Carregando...</div>',
                no_results: () => '<div class="p-2 text-muted">Nenhum resultado encontrado.</div>'
            }
        };

        configs.forEach(config => {
            const element = document.querySelector(config.selector);
            if (element) {
                new TomSelect(config.selector, {
                    ...tomSelectPtBr,
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    placeholder: config.placeholder,
                    preload: false,
                    load: (query, callback) => {
                        if (!query.length) return callback();
                        axios.get(`${config.endpoint}?q=${encodeURIComponent(query)}`)
                            .then(response => callback(response.data))
                            .catch(() => callback());
                    }
                });
            }
        });
    }

    static initInputMasks() {
        // Date mask
        const maskOptions = {
            mask: Date,
            pattern: 'd{/}`m{/}`Y',
            lazy: true,
            blocks: {
                d: { mask: IMask.MaskedRange, from: 1, to: 31, maxLength: 2 },
                m: { mask: IMask.MaskedRange, from: 1, to: 12, maxLength: 2 },
                Y: { mask: IMask.MaskedRange, from: 1900, to: 2099 }
            },
            format: (date) => {
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();
                return [day, month, year].join('/');
            },
            parse: (str) => {
                const [day, month, year] = str.split('/');
                return new Date(year, month - 1, day);
            }
        };

        document.querySelectorAll('.date').forEach(el => IMask(el, maskOptions));

        // CPF mask
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            IMask(cpfInput, { mask: '000.000.000-00' });
        }
    }

    static initImagePreview() {
        const fileInput = document.getElementById('file');
        const imagePreview = document.getElementById('image-preview');

        if (fileInput && imagePreview) {
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.setAttribute('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    static initSubspecialtyManager() {
        const addButton = document.getElementById('add-subspecialty');
        const wrapper = document.getElementById('subspecialties-wrapper');

        if (addButton && wrapper) {
            addButton.addEventListener('click', () => {
                const div = document.createElement('div');
                div.classList.add('input-group', 'mb-2', 'subspecialty-item');
                div.innerHTML = `
                    <input type="text" name="subspecialties[]" class="form-control" placeholder="Subespecialidade" required>
                    <button class="btn btn-outline-danger remove-subspecialty" type="button">×</button>
                `;
                wrapper.appendChild(div);
            });

            wrapper.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-subspecialty')) {
                    e.target.closest('.subspecialty-item').remove();
                }
            });
        }
    }

    static initQuillSync() {
        const biography = document.getElementById('biography');
        const description = document.getElementById('description');

        if (typeof quill !== 'undefined' && quill) {
            quill.on('text-change', () => {
                if (biography) biography.value = quill.root.innerHTML;
                if (description) description.value = quill.root.innerHTML;
            });
        }
    }
}