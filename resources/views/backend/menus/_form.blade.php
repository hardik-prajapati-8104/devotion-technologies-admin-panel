@csrf

<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Type</label>
        <select id="menuType" name="type" class="form-select" required>
            <option value="item" {{ old('type', optional($menu)->type ?? 'item') === 'item' ? 'selected' : '' }}>Item (link)</option>
            <option value="section" {{ old('type', optional($menu)->type ?? '') === 'section' ? 'selected' : '' }}>Section (heading only)</option>
        </select>
    </div>

    <div class="col-md-6" id="parentSelectWrapper">
        <label class="form-label">Parent (for submenu items)</label>
        <select id="parentSelect" name="parent_id" class="form-select" {{ old('type', optional($menu)->type ?? 'item') === 'section' ? 'disabled' : '' }}>
            <option value="">— Top level —</option>
            @foreach ($parents as $parent)
                <option value="{{ $parent->id }}" {{ (string) old('parent_id', optional($menu)->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>
                    {{ $parent->title }}
                </option>
            @endforeach
        </select>
        <div class="form-text text-muted">Parent is only used for link items that should become submenu entries.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', optional($menu)->title ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Icon (Bootstrap Icons class)</label>
        <input type="text" name="icon" class="form-control" placeholder="bi bi-briefcase-fill" value="{{ old('icon', optional($menu)->icon ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Route name</label>
        <input type="text" name="route_name" class="form-control" list="routeNamesList" value="{{ old('route_name', optional($menu)->route_name ?? '') }}">
        <datalist id="routeNamesList">
            @foreach ($routeNames as $name)
                <option value="{{ $name }}"></option>
            @endforeach
        </datalist>
    </div>

    <div class="col-md-6">
        <label class="form-label">Route pattern(s) for active/open state</label>
        <input type="text" name="route_pattern" class="form-control" placeholder="admin.services*,admin.service-categories*" value="{{ old('route_pattern', optional($menu)->route_pattern ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Permission required</label>
        <input type="text" name="permission" class="form-control" placeholder="services.view" value="{{ old('permission', optional($menu)->permission ?? '') }}">
        <div class="form-text">Leave blank to show to every logged-in admin.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Badge key</label>
        <select name="badge_key" class="form-select">
            <option value="">— No badge —</option>
            @foreach ($badgeKeys as $key)
                <option value="{{ $key }}" {{ old('badge_key', optional($menu)->badge_key ?? '') === $key ? 'selected' : '' }}>{{ $key }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Sort order</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', optional($menu)->sort_order ?? 0) }}">
    </div>

    <div class="col-md-4 d-flex align-items-center pt-4">
        <div class="form-check">
            <input type="checkbox" name="open_in_new_tab" value="1" class="form-check-input" id="newTab" {{ old('open_in_new_tab', optional($menu)->open_in_new_tab ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="newTab">Open in new tab</label>
        </div>
    </div>

    <div class="col-md-4 d-flex align-items-center pt-4">
        <div class="form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ old('is_active', optional($menu)->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="isActive">Active</label>
        </div>
    </div>

</div>

<div class="mt-4">
    <button type="submit" class="btn text-white" style="background:#aa8038;">Save</button>
    <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

@push('scripts')
<script>
    $(function () {
        const typeSelect = $('#menuType');
        const parentSelect = $('#parentSelect');
        const parentWrapper = $('#parentSelectWrapper');

        function toggleParentField() {
            const isSection = typeSelect.val() === 'section';
            parentSelect.prop('disabled', isSection);
            parentWrapper.toggleClass('d-none', isSection);

            if (isSection) {
                parentSelect.val('');
            }
        }

        typeSelect.on('change', toggleParentField);
        toggleParentField();
    });
</script>
@endpush
