@extends('backend.layouts.master')

@section('title', 'Settings')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>
    <h4>Settings</h4>
    <p class="subtitle">Site-wide configuration for Devotion Technology.</p>
@endsection

@section('admin-content')

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">General</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-social" type="button">Social Media</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button">Contact</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-smtp" type="button">SMTP</button></li>
</ul>

<div class="tab-content">

    <!-- General -->
    <div class="tab-pane fade show active" id="tab-general">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-admin.input name="site_name" label="Website Name" required="true" :value="$settings['site_name'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="site_email" label="Website Email" type="email" :value="$settings['site_email'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="site_phone" label="Website Phone" :value="$settings['site_phone'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="copyright_text" label="Copyright Text" :value="$settings['copyright_text'] ?? ('© '.date('Y').' Devotion Technology. All rights reserved.')" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Website Address</label>
                            <textarea name="site_address" class="form-control" rows="2">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <x-admin.image-upload name="site_logo" label="Logo" :existing="$settings['site_logo'] ?? null" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.image-upload name="site_favicon" label="Favicon" :existing="$settings['site_favicon'] ?? null" />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save me-1"></i> Save General Settings</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Social -->
    <div class="tab-pane fade" id="tab-social">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.settings.social.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-admin.input name="social_facebook" label="Facebook URL" type="url" :value="$settings['social_facebook'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="social_instagram" label="Instagram URL" type="url" :value="$settings['social_instagram'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="social_linkedin" label="LinkedIn URL" type="url" :value="$settings['social_linkedin'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="social_youtube" label="YouTube URL" type="url" :value="$settings['social_youtube'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="social_twitter" label="X / Twitter URL" type="url" :value="$settings['social_twitter'] ?? ''" />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save me-1"></i> Save Social Links</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="tab-pane fade" id="tab-contact">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.settings.contact.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-admin.input name="contact_email" label="Contact Email" type="email" :value="$settings['contact_email'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="contact_phone" label="Contact Phone" :value="$settings['contact_phone'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="contact_whatsapp" label="WhatsApp Number" :value="$settings['contact_whatsapp'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="contact_map_url" label="Google Maps URL" type="url" :value="$settings['contact_map_url'] ?? ''" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Contact Address</label>
                            <textarea name="contact_address" class="form-control" rows="2">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save me-1"></i> Save Contact Settings</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SMTP -->
    <div class="tab-pane fade" id="tab-smtp">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-warning small">
                    <i class="bi bi-shield-lock me-1"></i>
                    For security, the current SMTP password is never shown here. Leave the password field blank to keep it unchanged — only fill it in to set a new one.
                </div>
                <form action="{{ route('admin.settings.smtp.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-admin.input name="smtp_host" label="SMTP Host" :value="$settings['smtp_host'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="smtp_port" label="SMTP Port" type="number" :value="$settings['smtp_port'] ?? 587" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="smtp_username" label="SMTP Username" :value="$settings['smtp_username'] ?? ''" />
                        </div>
                        <div class="col-md-6">
                            <x-admin.input name="smtp_password" label="SMTP Password" type="password" help="Leave blank to keep the current password." />
                        </div>
                        <div class="col-md-4">
                            <x-admin.select name="smtp_encryption" label="Encryption"
                                :options="['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None']"
                                :selected="$settings['smtp_encryption'] ?? 'tls'" />
                        </div>
                        <div class="col-md-4">
                            <x-admin.input name="smtp_from_email" label="From Email" type="email" :value="$settings['smtp_from_email'] ?? ''" />
                        </div>
                        <div class="col-md-4">
                            <x-admin.input name="smtp_from_name" label="From Name" :value="$settings['smtp_from_name'] ?? 'Devotion Technology'" />
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save me-1"></i> Save SMTP Settings</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
