@extends('backend.layouts.master')

@section('title', 'IP Whitelist / Blacklist')

@section('admin-content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<p class="text-muted">Your current IP address: <code>{{ $currentIp }}</code></p>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-medium">Whitelist</div>
            <ul class="list-group list-group-flush">
                @forelse ($whitelist as $rule)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div>{{ $rule->ip }}</div>
                            @if ($rule->note)<div class="small text-muted">{{ $rule->note }}</div>@endif
                        </div>
                        <form method="POST" action="{{ route('admin.security.ip-rules.destroy', $rule) }}" onsubmit="return confirm('Remove this rule?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted small">No whitelist entries — all IPs currently allowed (whitelist mode only activates once you add an entry).</li>
                @endforelse
            </ul>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.security.ip-rules.store') }}" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="type" value="whitelist">
                    <input type="text" name="ip" class="form-control form-control-sm" placeholder="203.0.113.4 or 203.0.113.0/24" required>
                    <button class="btn btn-sm btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-medium">Blacklist</div>
            <ul class="list-group list-group-flush">
                @forelse ($blacklist as $rule)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div>{{ $rule->ip }}</div>
                            @if ($rule->note)<div class="small text-muted">{{ $rule->note }}</div>@endif
                        </div>
                        <form method="POST" action="{{ route('admin.security.ip-rules.destroy', $rule) }}" onsubmit="return confirm('Remove this rule?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                        </form>
                    </li>
                @empty
                    <li class="list-group-item text-muted small">No blacklist entries.</li>
                @endforelse
            </ul>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.security.ip-rules.store') }}" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="type" value="blacklist">
                    <input type="text" name="ip" class="form-control form-control-sm" placeholder="203.0.113.4 or 203.0.113.0/24" required>
                    <button class="btn btn-sm btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
