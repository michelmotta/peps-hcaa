@csrf
<section>
    <div class="row">
        {{-- Title --}}
        <div class="col-md-5">
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                    placeholder="Digite o título" name="title" value="{{ old('title', $guidebook?->title) }}" required>
                @error('title')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- ✅ Category Selection Added --}}
        <div class="col-md-4">
            <div class="mb-3">
                <label for="guidebook_category_id" class="form-label">Categoria</label>
                <select class="form-select @error('guidebook_category_id') is-invalid @enderror"
                    id="guidebook_category_id" name="guidebook_category_id" required>
                    <option value="">Selecione uma categoria...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('guidebook_category_id', $guidebook?->guidebook_category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('guidebook_category_id')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- ✅ Type Selection Added --}}
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label">Tipo de Acesso</label>
                <div class="d-flex gap-4 mt-2">
                    @foreach (App\Enums\GuidebookEnum::cases() as $type)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_{{ $type->value }}"
                                value="{{ $type->value }}" @checked(old('type', $guidebook?->type?->value ?? 'intern') == $type->value)>
                            <label class="form-check-label" for="type_{{ $type->value }}">
                                {{ $type->label() }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('type')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Editor -->
        <div class="col-md-12">
            <div class="mt-2 mb-4">
                <label class="form-label">Descrição</label>
                {{-- Ensure your rich text editor populates this hidden input --}}
                <div id="editor">
                    {!! old('description', $guidebook?->description) !!}
                </div>
                <input type="hidden" name="description" id="description"
                    value="{{ old('description', $guidebook?->description) }}">
                @error('description')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i data-feather="save" class="me-2 icon-xs"></i>
            Salvar
        </button>
    </div>
</section>
