document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('appWrapper');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const backdrop = document.getElementById('sidebarBackdrop');

    const isMobile = () => window.innerWidth < 992;

    toggleBtn?.addEventListener('click', function () {
        if (isMobile()) {
            wrapper.classList.toggle('sidebar-mobile-open');
        } else {
            wrapper.classList.toggle('sidebar-collapsed');
        }
    });

    backdrop?.addEventListener('click', function () {
        wrapper.classList.remove('sidebar-mobile-open');
    });

    // Submenu accordion
    document.querySelectorAll('[data-submenu-toggle]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const li = link.closest('li.has-submenu');

            if (!li) {
                return;
            }

            const isCollapsed = wrapper?.classList.contains('sidebar-collapsed');

            if (isCollapsed) {
                wrapper.classList.remove('sidebar-collapsed');
                document.querySelectorAll('li.has-submenu.open').forEach(function (openItem) {
                    openItem.classList.remove('open');
                });
                li.classList.add('open');
                return;
            }

            li.classList.toggle('open');
        });
    });
});

/**
 * Reusable SweetAlert2 delete-confirmation helper.
 * Usage: <form id="delete-form-5" ...>
 *        <a onclick="confirmDelete('delete-form-5')">Delete</a>
 */
function confirmDelete(formId, itemLabel = 'this item') {
    Swal.fire({
        title: 'Are you sure?',
        text: `This will permanently remove ${itemLabel}. This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#aa8038',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
