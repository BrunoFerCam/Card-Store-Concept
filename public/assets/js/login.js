(function () {
    'use strict';

    var modal = document.getElementById('login-modal');
    if (!modal) {
        return;
    }

    var form = document.getElementById('login-form');
    var error = document.getElementById('login-error');
    var submit = document.getElementById('login-submit');

    function openModal() {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        var user = document.getElementById('login-username');
        if (user) {
            user.focus();
        }
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    document.querySelectorAll('[data-login-open]').forEach(function (el) {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            openModal();
        });
    });

    document.querySelectorAll('[data-login-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });

    if (window.location.search.indexOf('login=1') !== -1) {
        openModal();
    }

    if (!form) {
        return;
    }

    function resetSubmit() {
        submit.disabled = false;
        submit.textContent = 'Entrar';
    }

    function showError(message) {
        error.textContent = message;
        error.hidden = false;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        error.hidden = true;
        submit.disabled = true;
        submit.textContent = 'Entrando…';

        fetch('api/login.php', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data && data.ok) {
                    window.location.href = 'admin/index.php';
                    return;
                }
                showError((data && data.message) ? data.message : 'Não foi possível entrar.');
                resetSubmit();
            })
            .catch(function () {
                showError('Erro de conexão. Tente novamente.');
                resetSubmit();
            });
    });
})();
