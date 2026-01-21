import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
        if (methodInput) {
            const ok = confirm('Naozaj chcete túto položku zmazať?');
            if (!ok) e.preventDefault();
        }
    }, true);

    document.addEventListener('change', function (e) {
        const cb = e.target;
        if (!cb || !cb.matches('.complete-checkbox')) return;

        if (!cb.checked) return;

        const form = cb.closest('form.complete-form');
        if (!form) return;


        const action = form.getAttribute('action');
        const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

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
            }

            // odstrániť položku zo zoznamu
            const item = form.closest('.task-item') || form.closest('.list-group-item');
            if (item) item.remove();

            // aktualizovať počítadlo v dashboarde, ak je prítomné
            const completedCounter = document.querySelector('#completed-counter');
            if (completedCounter) {
                const current = parseInt(completedCounter.textContent || '0', 10);
                completedCounter.textContent = (current + 1).toString();
            }


        }).catch(function (err) {
            console.error(err);
            alert('Pri označovaní úlohy nastala chyba: ' + err.message);
            cb.checked = false;
            cb.disabled = false;
        });
    });


    document.querySelectorAll('form[action][method="POST"]').forEach(function (form) {
        const action = form.getAttribute('action');
        if (action && action.includes('/tasks/') && action.includes('/complete')) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

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
                    }


                    let removed = false;
                    const row = form.closest('tr');
                    if (row) {
                        row.remove();
                        removed = true;
                    }


                    if (!removed) {
                        const col = form.closest('.col-12, .col-md-6, .col-lg-4');
                        if (col) {
                            col.remove();
                            removed = true;
                        }
                    }


                    if (!removed) {
                        const titem = form.closest('.task-item') || form.closest('.list-group-item');
                        if (titem) {
                            titem.remove();
                            removed = true;
                        } else {
                            const card = form.closest('.card');
                            if (card) {
                                card.remove();
                                removed = true;
                            }
                        }
                    }


                    if (!removed) {
                        window.location.href = '/tasks';
                        return;
                    }


                    const completedCounter = document.querySelector('#completed-counter');
                    if (completedCounter) {
                        const current = parseInt(completedCounter.textContent || '0', 10);
                        completedCounter.textContent = (current + 1).toString();
                    }
                }).catch(function (err) {
                    console.error(err);
                    alert('Pri označovaní úlohy nastala chyba: ' + err.message);
                });
            });
        }
    });


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


    document.addEventListener('change', function (e) {
        const el = e.target;
        if (!el || el.id !== 'project-filter') return;

        const pid = el.value;
        const container = document.getElementById('tasks-list-container');
        if (!container) return;


        const url = new URL(window.location.href);
        if (pid) url.searchParams.set('project_id', pid); else url.searchParams.delete('project_id');

        container.style.opacity = '0.6';

        fetch(url.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        }).then(function (resp) {
            if (!resp.ok) throw resp;
            return resp.json();
        }).then(function (json) {
            if (json && json.html !== undefined) {
                container.innerHTML = json.html;
            }
            container.style.opacity = '';
        }).catch(function (err) {
            console.error('Filter load error', err);
            container.style.opacity = '';
            alert('Chyba pri načítaní úloh pre tento projekt.');
        });
    });
});

