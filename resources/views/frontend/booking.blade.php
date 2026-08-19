@extends('frontend.layouts.app')
 
@section('content')
  <!-- header section -->
  <section class="page-header">
      <div class="overlay"></div>

      <div class="container position-relative"> 
          <h1 data-aos="fade-up">Booking</h1>

          <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
              <li class="breadcrumb-item" data-aos="fade-up">
                  <a href="{{ route('home') }}">
                      <i class="bi bi-house-door-fill me-1"></i> Home
                  </a>
              </li>

              <li class="breadcrumb-item active" data-aos="fade-up">
                  Booking
              </li>
          </ol>
      </div>
  </section>

  <section>
    <div class="container">
      <div class="row g-4">

        <div class="col-lg-8">

          <div class="booking-card">
            <h3 class="fw-bold mb-1" data-aos="fade-up">Get an Instant Quote</h3>
            <p class="text-muted" data-aos="fade-up">Fill in your details — we will confirm within 15 minutes.</p>

            <div id="bookingSuccess" class="alert alert-success d-none">
              <i class="bi bi-check-circle me-2"></i>
              Booking received! Our team will reach out shortly.
            </div>

            {{-- <form id="bookingForm" class="row g-3 mt-1" data-aos="fade-up"> 
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input class="form-control" placeholder="Enter Your Full Name" id="full_name" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input class="form-control" type="tel" placeholder="Enter  Your Phone Number" id="phone" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input class="form-control" type="email" placeholder="Enter Your Email Address" id="email" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Service Type</label>
                    <select class="form-select" name="service_category_id" required>
                        <option value="" disabled selected>Select a service</option>
                        @forelse ($serviceCategories as $category)
                            <option value="{{ $category->id }}" {{ old('service_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @empty
                            <option value="" disabled>No services available</option>
                        @endforelse
                    </select>
                </div>
   
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <input class="form-control" placeholder="Enter Your Address" id="address" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" rows="3" id="description" placeholder="Pets, parking, special requests..."></textarea>
                </div>

                <div class="col-12">
                  <button class="btn btn-orange w-100 py-3 fw-bold">
                    <i class="bi bi-calendar2-check me-2"></i>Request Booking
                  </button>
                </div>
            </form> --}}

            <form id="bookingForm" class="row g-3 mt-1" data-aos="fade-up" novalidate>
                @csrf

                <div id="bookingFormAlert" class="col-12 d-none"></div>

                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input class="form-control" placeholder="Enter Your Full Name" id="full_name" name="full_name" required>
                    <div class="invalid-feedback" data-error-for="full_name"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="tel" placeholder="Enter Your Phone Number" id="phone" name="phone" required>
                    <div class="invalid-feedback" data-error-for="phone"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" placeholder="Enter Your Email Address" id="email" name="email" required>
                    <div class="invalid-feedback" data-error-for="email"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Service Type</label>
                    <select class="form-select" id="service_category_id" name="service_category_id" required>
                        <option value="" disabled selected>Select a service</option>
                        @forelse ($serviceCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @empty
                            <option value="" disabled>No services available</option>
                        @endforelse
                    </select>
                    <div class="invalid-feedback" data-error-for="service_category_id"></div>
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input class="form-control" placeholder="Enter Your Address" id="address" name="address" required>
                    <div class="invalid-feedback" data-error-for="address"></div>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="3" id="description" name="description" placeholder="Pets, parking, special requests..."></textarea>
                    <div class="invalid-feedback" data-error-for="description"></div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-orange w-100 py-3 fw-bold" id="bookingFormSubmit">
                        <i class="bi bi-calendar2-check me-2"></i>
                        <span id="bookingFormSubmitText">Request Booking</span>
                    </button>
                </div>
            </form>

          </div>
        </div>

        <div class="col-lg-4">
          <div class="booking-card">
            <h5 class="fw-bold" data-aos="fade-up">Why Book With Us</h5>
            <ul class="list-unstyled mt-3">

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Vetted & insured cleaners
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Flat, transparent pricing
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Eco-friendly supplies included
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Easy reschedule & cancel
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>100% happiness guarantee
              </li>
            </ul>
            <hr>
            <p class="mb-1 small text-muted" data-aos="fade-up">Need help?</p>
            <a href="tel:+91 9173307640" class="fw-bold text-orange" data-aos="fade-up"><i class="bi bi-telephone me-1"></i>+91 9173307640</a>
          </div>
        </div>
      </div>
    </div>
  </section>
<script>
(function () {
    const form = document.getElementById('bookingForm');
    if (!form) return;

    const alertBox = document.getElementById('bookingFormAlert');
    const submitBtn = document.getElementById('bookingFormSubmit');
    const submitText = document.getElementById('bookingFormSubmitText');

    function showAlert(type, message) {
        alertBox.className = `col-12 alert alert-${type}`;
        alertBox.textContent = message;
    }

    function clearFieldErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-error-for]').forEach(el => el.textContent = '');
    }

    function showFieldErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            const feedback = form.querySelector(`[data-error-for="${field}"]`);
            if (input) input.classList.add('is-invalid');
            if (feedback) feedback.textContent = errors[field][0];
        });
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearFieldErrors();
        alertBox.className = 'col-12 d-none';

        submitBtn.disabled = true;
        submitText.textContent = 'Sending...';

        const formData = new FormData(form);

        try {
            const response = await fetch('{{ route('admin.booking-enquiries.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        || form.querySelector('input[name="_token"]').value,
                },
                body: formData,
            });

            const result = await response.json();

            if (response.status === 422) {
                showFieldErrors(result.errors || {});
                showAlert('danger', 'Please fix the highlighted fields and try again.');
                return;
            }

            if (!response.ok) {
                showAlert('danger', 'Something went wrong. Please try again in a moment.');
                return;
            }

            showAlert('success', result.message || 'Thanks! Your booking request has been received.');
            form.reset();

        } catch (err) {
            showAlert('danger', 'Network error — please check your connection and try again.');
        } finally {
            submitBtn.disabled = false;
            submitText.textContent = 'Request Booking';
        }
    });
})();
</script>
@endsection