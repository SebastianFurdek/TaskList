import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Custom JS: confirmation for destructive actions and AJAX complete
document.addEventListener('DOMContentLoaded', function () {
    // Confirm before delete (for forms with method DELETE)
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
        if (methodInput) {
            const ok = confirm('Naozaj chcete túto položku zmazať?');
            if (!ok) e.preventDefault();
        }
    }, true);

    // Intercept complete forms (POST to tasks.complete) and do AJAX to avoid full reload
    document.querySelectorAll('form[action][method="POST"]').forEach(function (form) {
        // mark forms intended for complete by checking action URL contains '/tasks/' and 'complete'
        const action = form.getAttribute('action');
        if (action && action.includes('/tasks/') && action.includes('/complete')) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!confirm('Označiť úlohu ako dokončenú?')) return;

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({})
                }).then(function (resp) {
                    if (resp.ok) return resp.json().catch(() => ({}));
                    throw new Error('Network response was not ok');
                }).then(function () {
                    // Try removing row (table layout)
                    let removed = false;
                    const row = form.closest('tr');
                    if (row) {
                        row.remove();
                        removed = true;
                    }

                    // Try removing card column (card/grid layout)
                    if (!removed) {
                        const col = form.closest('.col-12, .col-md-6, .col-lg-4');
                        if (col) {
                            col.remove();
                            removed = true;
                        }
                    }

                    // Try removing the card itself
                    if (!removed) {
                        const card = form.closest('.card');
                        if (card) {
                            card.remove();
                            removed = true;
                        }
                    }

                    // If nothing removed (we are likely on a show page), redirect to /tasks
                    if (!removed) {
                        window.location.href = '/tasks';
                        return;
                    }

                    // Optionally update a counter in the dashboard if present
                    const completedCounter = document.querySelector('#completed-counter');
                    if (completedCounter) {
                        const current = parseInt(completedCounter.textContent || '0', 10);
                        completedCounter.textContent = (current + 1).toString();
                    }

                    // Show a simple alert
                    alert('Úloha označená ako dokončená.');
                }).catch(function (err) {
                    console.error(err);
                    alert('Pri označovaní úlohy nastala chyba.');
                });
            });
        }
    });

    // Client-side enhancement: prevent submit if form invalid and show first invalid field
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.classList.add('needs-validation');
                const invalid = form.querySelector(':invalid');
                if (invalid) invalid.focus();
            }
        });
    });
});
