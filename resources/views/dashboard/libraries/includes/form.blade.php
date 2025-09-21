@csrf
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="name" class="form-label">Título</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                    placeholder="Digite o título" name="title"
                    value="@if(isset($library)){{ $library->title }}@else{{ old('title') }} @endif"
                    required>
                @error('title')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label" for="specialtyDropdown">Especialidades</label>
                <div class="dropdown">
                    <button
                        class="btn btn-light dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center"
                        type="button" id="specialtyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="specialtyButtonText">Selecione as especialidades</span>
                    </button>
                    <div class="dropdown-menu w-100 p-3" aria-labelledby="specialtyDropdown"
                        style="max-height: 280px; overflow-y: auto;">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <a href="#" id="selectAllSpecialties" class="small fw-bold">Selecionar Todos</a>
                            <a href="#" id="deselectAllSpecialties" class="small fw-bold text-danger">Limpar
                                Seleção</a>
                        </div>
                        @forelse ($specialties as $parent)
                            <div class="form-check">
                                <input class="form-check-input parent-checkbox" type="checkbox" name="specialty_ids[]"
                                    value="{{ $parent->id }}" id="specialty-{{ $parent->id }}"
                                    data-parent-id="{{ $parent->id }}"
                                    @if (in_array($parent->id, $selectedSpecialties ?? [])) checked @endif>
                                <label class="form-check-label fw-bold" for="specialty-{{ $parent->id }}">
                                    {{ $loop->iteration }}. {{ $parent->name }}
                                </label>
                            </div>

                            @if ($parent->children->isNotEmpty())
                                <div class="ms-4 mt-1">
                                    @foreach ($parent->children as $child)
                                        <div class="form-check">
                                            <input class="form-check-input child-checkbox" type="checkbox"
                                                name="specialty_ids[]" value="{{ $child->id }}"
                                                id="specialty-{{ $child->id }}"
                                                data-parent-id="{{ $parent->id }}"
                                                @if (in_array($child->id, $selectedSpecialties ?? [])) checked @endif>
                                            <label class="form-check-label" for="specialty-{{ $child->id }}">
                                                {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                                                {{ $child->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if (!$loop->last)
                                <hr class="my-2">
                            @endif
                        @empty
                            <span class="dropdown-item-text">Nenhuma especialidade encontrada.</span>
                        @endforelse
                    </div>
                </div>
                @error('specialty_ids')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="file" class="form-label">Arquivo</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                    name="file">
                <small class="d-block">Formato permitido: PDF. Tamanho máximo: 2MB</small>
                @error('file')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="text-center mt-5">
        <button type="submit" class="btn btn-primary btn-lg">
            <i data-feather="save" class="me-2 icon-xs"></i>
            Salvar
        </button>
    </div>
</section>
<style>
    #specialtyDropdown~.dropdown-menu .form-check {
        margin-bottom: 0.5rem;
    }

    #specialtyDropdown~.dropdown-menu .form-check-label {
        font-size: 1.05rem;
        padding-left: 0.5rem;
    }

    #specialtyDropdown~.dropdown-menu .form-check-input {
        width: 1.2em;
        height: 1.2em;
        margin-top: 0.15em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const specialtyButtonText = document.getElementById('specialtyButtonText');
        const allCheckboxes = document.querySelectorAll('input[name="specialty_ids[]"]');
        const parentCheckboxes = document.querySelectorAll('.parent-checkbox');
        const childCheckboxes = document.querySelectorAll('.child-checkbox');
        const selectAllBtn = document.getElementById('selectAllSpecialties');
        const deselectAllBtn = document.getElementById('deselectAllSpecialties');
        const dropdownMenu = document.querySelector('.dropdown-menu[aria-labelledby="specialtyDropdown"]');

        if (dropdownMenu) {
            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        const updateButtonText = () => {
            const checkedCount = document.querySelectorAll('input[name="specialty_ids[]"]:checked').length;
            if (checkedCount === 0) {
                specialtyButtonText.textContent = 'Selecione a(s) especialidade(s)';
            } else if (checkedCount === 1) {
                specialtyButtonText.textContent = '1 especialidade selecionada';
            } else {
                specialtyButtonText.textContent = `${checkedCount} especialidades selecionadas`;
            }
        };

        const syncParentCheckbox = (parentId) => {
            const parentCheckbox = document.querySelector(`.parent-checkbox[value='${parentId}']`);
            if (!parentCheckbox) return;

            const children = document.querySelectorAll(`.child-checkbox[data-parent-id='${parentId}']`);
            const isAnyChildChecked = Array.from(children).some(child => child.checked);
            parentCheckbox.checked = isAnyChildChecked;
        };

        parentCheckboxes.forEach(parent => {
            parent.addEventListener('change', function() {
                const children = document.querySelectorAll(
                    `.child-checkbox[data-parent-id='${this.value}']`);
                children.forEach(child => {
                    child.checked = this.checked;
                });
            });
        });

        childCheckboxes.forEach(child => {
            child.addEventListener('change', function() {
                syncParentCheckbox(this.dataset.parentId);
            });
        });

        allCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateButtonText);
        });

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                allCheckboxes.forEach(checkbox => checkbox.checked = true);
                updateButtonText();
            });
        }

        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                allCheckboxes.forEach(checkbox => checkbox.checked = false);
                updateButtonText();
            });
        }
        updateButtonText();
    });
</script>
