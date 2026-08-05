@php $isEdit = isset($faq); @endphp

<div class="row g-3">
    <div class="col-md-8">
        <x-admin.input name="question" label="Question" required="true" :value="$isEdit ? $faq->question : ''" />
    </div>
    <div class="col-md-4">
        <x-admin.select name="faq_category_id" label="Category" placeholder="Select category"
            :options="$categories->pluck('name', 'id')"
            :selected="$isEdit ? $faq->faq_category_id : old('faq_category_id')" />
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium" for="answer">Answer <span class="text-danger">*</span></label>
        <textarea name="answer" id="answer" class="form-control @error('answer') is-invalid @enderror" rows="5" maxlength="3000">{{ old('answer', $isEdit ? $faq->answer : '') }}</textarea>
        @error('answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <x-admin.input name="display_order" label="Display Order" type="number" :value="$isEdit ? $faq->display_order : 0" />
    </div>
    <div class="col-md-4">
        <x-admin.select name="status" label="Status" required="true"
            :options="[1 => 'Enabled', 0 => 'Disabled']"
            :selected="$isEdit ? (int) $faq->status : 1" />
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>
