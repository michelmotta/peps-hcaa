<section>
    <form type="GET" action="{{ $action }}" class="search-form position-relative mx-auto">
        <input type="text" class="form-control search-input" name="q" value="{{ request('q') }}"
            placeholder="{{ $title }}">
        <button type="submit" class="search-btn position-absolute top-50 translate-middle-y end-0 me-2">
            <i class="bi bi-search"></i>
        </button>
    </form>
</section>
