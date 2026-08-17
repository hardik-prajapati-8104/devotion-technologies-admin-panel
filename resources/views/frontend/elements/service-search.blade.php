{{-- Search bar overlay with live category/service suggestions --}}
<div class="container search-wrapper">
    <form class="search-bar" action="{{ route('services') }}" method="GET" autocomplete="off" id="serviceSearchForm">

        <div class="search-input position-relative">
            <i class="bi bi-search"></i>
            <input type="text"
                   name="q"
                   id="serviceSearchInput"
                   class="form-control"
                   placeholder="Search Your Services"
                   autocomplete="off">

            <div class="search-results-dropdown" id="searchResultsDropdown"></div>
        </div>

        <button type="submit" class="btn btn-orange search-btn">
            <i class="bi bi-search"></i>
        </button>

    </form>
</div>

<style>
    .search-wrapper {
        position: relative;
        z-index: 1000;
    }

    .search-input {
        position: relative;
        flex: 1;
    }

    .search-results-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.14);
        max-height: 340px;
        overflow-y: auto;
        display: none;
        z-index: 1050;
        border: 1px solid #eee;
    }

    .search-results-dropdown.show {
        display: block;
    }

    .search-result-group-title {
        padding: 0.5rem 1rem;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #999;
        font-weight: 600;
        background: #f8f9fa;
        position: sticky;
        top: 0;
    }

    .search-result-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 1rem;
        text-decoration: none;
        color: #212529;
        font-size: 0.92rem;
        transition: background-color 0.15s ease;
    }

    .search-result-item:hover,
    .search-result-item.active {
        background: #fff4ec;
        color: #212529;
    }

    .search-result-item img {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .search-result-item i {
        font-size: 1.1rem;
        color: var(--bs-orange, #f37021);
        flex-shrink: 0;
        width: 20px;
        text-align: center;
    }

    .search-no-results,
    .search-loading {
        padding: 1rem;
        text-align: center;
        color: #888;
        font-size: 0.88rem;
    }

    /* Scrollbar styling */
    .search-results-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .search-results-dropdown::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 3px;
    }

    @media (max-width: 576px) {
        .search-results-dropdown {
            max-height: 260px;
        }
    }
</style>

<script>
    (function () {
        const input = document.getElementById('serviceSearchInput');
        const dropdown = document.getElementById('searchResultsDropdown');
        const suggestionsUrl = '{{ route('search.suggestions') }}';
        const baseUrl = '{{ rtrim(url('/'), '/') }}';

        let debounceTimer;
        let activeController;

        input.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(debounceTimer);

            if (q.length < 2) {
                closeDropdown();
                return;
            }

            debounceTimer = setTimeout(() => fetchSuggestions(q), 300);
        });

        function fetchSuggestions(q) {
            if (activeController) activeController.abort();
            activeController = new AbortController();

            dropdown.innerHTML = '<div class="search-loading">Searching...</div>';
            dropdown.classList.add('show');

            fetch(`${suggestionsUrl}?q=${encodeURIComponent(q)}`, { signal: activeController.signal })
                .then(res => res.json())
                .then(data => renderResults(data, q))
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        dropdown.innerHTML = '<div class="search-no-results">Something went wrong. Try again.</div>';
                    }
                });
        }

        function renderResults(data, q) {
            const categories = data.categories || [];
            const services = data.services || [];

            if (categories.length === 0 && services.length === 0) {
                dropdown.innerHTML = `<div class="search-no-results">No results for "${escapeHtml(q)}"</div>`;
                dropdown.classList.add('show');
                return;
            }

            let html = '';

            if (categories.length) {
                html += '<div class="search-result-group-title">Categories</div>';
                categories.forEach(cat => {
                    html += `
                        <a href="${baseUrl}/services?category=${cat.id}" class="search-result-item">
                            <i class="bi bi-grid"></i>
                            <span>${escapeHtml(cat.name)}</span>
                        </a>`;
                });
            }

            if (services.length) {
                html += '<div class="search-result-group-title">Services</div>';
                services.forEach(svc => {
                    const imgTag = svc.featured_image
                        ? `<img src="${baseUrl}/storage/${svc.featured_image}" alt="${escapeHtml(svc.name)}">`
                        : '<i class="bi bi-tools"></i>';

                    html += `
                        <a href="${baseUrl}/services/${svc.slug}" class="search-result-item">
                            ${imgTag}
                            <span>${escapeHtml(svc.name)}</span>
                        </a>`;
                });
            }

            dropdown.innerHTML = html;
            dropdown.classList.add('show');
        }

        function closeDropdown() {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-wrapper')) {
                closeDropdown();
            }
        });

        // Close on Escape
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeDropdown();
        });

        // Reopen dropdown if user refocuses input with existing text
        input.addEventListener('focus', function () {
            if (this.value.trim().length >= 2 && dropdown.innerHTML.trim() !== '') {
                dropdown.classList.add('show');
            }
        });
    })();
</script>