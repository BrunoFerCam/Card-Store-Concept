document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('.alert[data-dismissible]').forEach(function (alert) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'alert-close';
        btn.setAttribute('aria-label', 'Fechar mensagem');
        btn.textContent = '\u00d7';
        btn.addEventListener('click', function () {
            alert.remove();
        });
        alert.appendChild(btn);
    });

    var imageInput = document.getElementById('image');
    var preview = document.getElementById('image-preview');
    if (imageInput && preview) {
        var updatePreview = function () {
            var url = imageInput.value.trim();
            preview.innerHTML = '';
            if (!url) {
                preview.textContent = 'Sem pré-visualização';
                return;
            }
            var img = document.createElement('img');
            img.alt = 'Pré-visualização da imagem';
            img.onerror = function () {
                preview.textContent = 'Imagem inválida';
            };
            img.src = url;
            preview.appendChild(img);
        };
        imageInput.addEventListener('input', updatePreview);
        updatePreview();
    }

    document.querySelectorAll('form.card-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (btn && !form.classList.contains('submitting')) {
                form.classList.add('submitting');
                btn.disabled = true;
                btn.dataset.original = btn.textContent;
                btn.textContent = 'Salvando…';
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    function closeAll() {
        document.querySelectorAll('.modal.open').forEach(function (m) {
            m.classList.remove('open');
            m.setAttribute('aria-hidden', 'true');
        });
        document.body.classList.remove('modal-open');
    }

    document.querySelectorAll('[data-close-modal]').forEach(function (el) {
        el.addEventListener('click', function () {
            var m = el.closest('.modal');
            if (m) {
                m.classList.remove('open');
                m.setAttribute('aria-hidden', 'true');
            }
            if (!document.querySelector('.modal.open')) {
                document.body.classList.remove('modal-open');
            }
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAll();
        }
    });
});
