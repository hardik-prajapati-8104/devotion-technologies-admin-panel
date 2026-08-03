@props(['action', 'formId', 'label' => 'this item'])

<a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); confirmDelete('{{ $formId }}', '{{ $label }}');">
    <i class="bi bi-trash me-2"></i>Delete
</a>
<form id="{{ $formId }}" action="{{ $action }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
