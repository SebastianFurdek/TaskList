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

    // Handle checkbox-based completes (list view)
    // Use event delegation so dynamically added items are handled as well
    document.addEventListener('change', function (e) {
        const cb = e.target;
        if (!cb || !cb.matches('.complete-checkbox')) return;

        // only act when checkbox is being checked (not unchecked)
        if (!cb.checked) return;

        const form = cb.closest('form.complete-form');
        if (!form) return;

        // No confirmation on checkbox change (user requested immediate completion)
        // proceed directly

        const action = form.getAttribute('action');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        console.log('Completing task via', action);
        // disable checkbox while request is in-flight
        cb.disabled = true;

        fetch(action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        }).then(function (resp) {
            console.log('Response status', resp.status, 'ok', resp.ok);
            if (!resp.ok) {
                return resp.text().then(function (text) {
                    throw new Error('Server error ' + resp.status + ': ' + text);
                });
            }
            // try parse JSON, but if not JSON, proceed
            return resp.text().then(function (text) {
                let json = null;
                try { json = JSON.parse(text); } catch (err) { /* not json */ }
                return { raw: text, json: json };
            });
        }).then(function (result) {
            console.log('Complete result', result);
            // If server returned JSON, require success === true
            if (result.json) {
                if (result.json.success === true) {
                    // ok
                } else if (result.json.success === false) {
                    throw new Error(result.json.message || 'Server rejected completion');
                }
            } else {
                // No JSON — inspect raw text to detect HTML redirects or login pages
                const raw = (result.raw || '').toLowerCase();
                if (raw.includes('<!doctype') || raw.includes('<html') || raw.includes('login') || raw.includes('csrf')) {
                    throw new Error('Unexpected HTML response from server (maybe redirected to login).');
                }
                // otherwise treat as success
            }
            // remove the list item
            const item = form.closest('.list-group-item');
            if (item) item.remove();

            // update counter
            const completedCounter = document.querySelector('#completed-counter');
            if (completedCounter) {
                const current = parseInt(completedCounter.textContent || '0', 10);
                completedCounter.textContent = (current + 1).toString();
            }

            // no user-facing notification on completion (silent)
         }).catch(function (err) {
            console.error(err);
            alert('Pri označovaní úlohy nastala chyba: ' + err.message);
            cb.checked = false;
            cb.disabled = false;
        });
    });

    // Intercept complete forms (fallback for other layouts)
    document.querySelectorAll('form[action][method="POST"]').forEach(function (form) {
        const action = form.getAttribute('action');
        if (action && action.includes('/tasks/') && action.includes('/complete')) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                // No confirmation for form-based complete actions; proceed directly

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                console.log('Completing task via (form submit) ', action);
                fetch(action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                }).then(function (resp) {
                    console.log('Response status', resp.status, 'ok', resp.ok);
                    if (!resp.ok) {
                        return resp.text().then(function (text) {
                            throw new Error('Server error ' + resp.status + ': ' + text);
                        });
                    }
                    return resp.text().then(function (text) {
                        let json = null;
                        try { json = JSON.parse(text); } catch (err) { /* not json */ }
                        return { raw: text, json: json };
                    });
                }).then(function (result) {
                    console.log('Complete (form) result', result);
                    if (result.json) {
                        if (result.json.success === true) {
                            // ok
                        } else if (result.json.success === false) {
                            throw new Error(result.json.message || 'Server rejected completion');
                        }
                    } else {
                        const raw = (result.raw || '').toLowerCase();
                        if (raw.includes('<!doctype') || raw.includes('<html') || raw.includes('login') || raw.includes('csrf')) {
                            throw new Error('Unexpected HTML response from server (maybe redirected to login).');
                        }
                        // otherwise treat as success
                    }
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

                    // no user-facing notification on completion (silent)
                 }).catch(function (err) {
                    console.error(err);
                    alert('Pri označovaní úlohy nastala chyba: ' + err.message);
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
