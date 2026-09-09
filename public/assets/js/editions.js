(function () {
    'use strict';

    var gameSelect = document.getElementById('game');

    function gameKey() {
        var opt = gameSelect.options[gameSelect.selectedIndex];
        return opt ? (opt.getAttribute('data-game-key') || '') : '';
    }

    function placeholder(select, text, disabled) {
        select.innerHTML = '';
        var o = document.createElement('option');
        o.value = '';
        o.textContent = text;
        select.appendChild(o);
        select.disabled = disabled;
    }

    var editionSelect = document.getElementById('edition_id');
    var editionName = document.getElementById('edition_name');

    function loadEditions(resetCurrent) {
        if (!editionSelect) return;
        placeholder(editionSelect, 'Carregando…', true);
        var current = resetCurrent ? '' : (editionSelect.getAttribute('data-current') || '');

        fetch('../api/editions.php?game=' + encodeURIComponent(gameKey()))
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (items) {
                editionSelect.innerHTML = '';
                var ph = document.createElement('option');
                ph.value = '';
                ph.textContent = 'Selecione a edição';
                editionSelect.appendChild(ph);

                (items || []).forEach(function (it) {
                    var o = document.createElement('option');
                    o.value = it.id;
                    o.setAttribute('data-name', it.name);
                    o.textContent = it.name;
                    editionSelect.appendChild(o);
                });

                editionSelect.disabled = false;
                if (current) editionSelect.value = current;
                syncEditionName();
            })
            .catch(function () { placeholder(editionSelect, 'Erro ao carregar', true); });
    }

    function syncEditionName() {
        if (!editionName || !editionSelect) return;
        var o = editionSelect.options[editionSelect.selectedIndex];
        editionName.value = (o && o.value) ? (o.getAttribute('data-name') || o.textContent) : '';
    }

    var raritySelect = document.getElementById('rarity');

    function loadRarities(resetCurrent) {
        if (!raritySelect) return;
        placeholder(raritySelect, 'Carregando…', true);
        var current = resetCurrent ? '' : (raritySelect.getAttribute('data-current') || '');

        fetch('../api/rarities.php?game=' + encodeURIComponent(gameKey()))
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (items) {
                raritySelect.innerHTML = '';
                var ph = document.createElement('option');
                ph.value = '';
                ph.textContent = 'Sem raridade';
                raritySelect.appendChild(ph);

                (items || []).forEach(function (it) {
                    var o = document.createElement('option');
                    o.value = it.name;
                    o.setAttribute('data-color', it.color);
                    o.textContent = it.name;
                    raritySelect.appendChild(o);
                });

                raritySelect.disabled = false;
                if (current) raritySelect.value = current;
            })
            .catch(function () { placeholder(raritySelect, 'Erro ao carregar', true); });
    }

    if (gameSelect) {
        gameSelect.addEventListener('change', function () {
            if (editionName) editionName.value = '';
            if (editionSelect) editionSelect.removeAttribute('data-current');
            if (raritySelect) raritySelect.removeAttribute('data-current');
            loadEditions(true);
            loadRarities(true);
        });
    }

    if (editionSelect) {
        editionSelect.addEventListener('change', syncEditionName);
    }

    if (gameSelect && gameKey()) {
        loadEditions(false);
        loadRarities(false);
    }
})();
