<?php


$base      = '.';
$pageTitle = 'Cartas';
$user      = $user ?? null;
$isLogged  = $user !== null;
$rarityColors = $rarityColors ?? [];

if (!function_exists('c_badge_style')) {
    function c_badge_style($hex): string {
        if (!$hex || !preg_match('/^#([0-9a-fA-F]{6})$/', $hex, $m)) {
            return '';
        }
        $n  = hexdec($m[1]);
        $r  = ($n >> 16) & 255;
        $g  = ($n >> 8) & 255;
        $b  = $n & 255;
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return 'style="background:' . $hex . ';color:' . ($lum > 0.6 ? '#111827' : '#ffffff') . ';"';
    }
}

require __DIR__ . '/../layout/header.php';
?>
<div class="page-head">
    <h1>Cartas registradas <span class="count"><?= count($cards) ?></span></h1>
    <?php if ($isLogged): ?>
        <a class="btn" href="./admin/cards.php">Gerenciar cartas</a>
    <?php endif; ?>
</div>
<p class="intro">
    <?php if ($isLogged): ?>
        Você está logado. Use "Gerenciar" para incluir, editar ou excluir cartas.
    <?php else: ?>
        Esta é uma visualização pública das cartas registradas.
        <a href="./login.php" data-login-open>Entrar</a> para incluir, editar ou excluir.
    <?php endif; ?>
</p>

<?php
$fGame    = $filter['game']    ?? '';
$fRarity  = $filter['rarity']  ?? '';
$fEdition = $filter['edition'] ?? '';
$fSort    = $filter['sort']    ?? 'name_asc';
$opts     = $filter['options'] ?? ['games' => [], 'rarities' => [], 'editions' => []];
$hasFilter = $fGame !== '' || $fRarity !== '' || $fEdition !== '';

$sortLabels = [
    'name_asc'  => 'Nome (A–Z)',
    'name_desc' => 'Nome (Z–A)',
    'game'      => 'Card Game',
    'rarity'    => 'Raridade',
    'edition'   => 'Edição',
];

function f_sel($current, $value): string {
    return (string) $current === (string) $value ? ' selected' : '';
}
?>
<form class="filters" id="catalog-filters" method="get" action="">
    <div class="f-group">
        <label for="f-game">Card Game</label>
        <select id="f-game" name="game" data-auto-filter>
            <option value="">Todos</option>
            <?php foreach (($opts['games'] ?? []) as $g): ?>
                <option value="<?= htmlspecialchars((string) $g, ENT_QUOTES, 'UTF-8') ?>"<?= f_sel($fGame, $g) ?>><?= htmlspecialchars((string) $g, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="f-group">
        <label for="f-rarity">Raridade <?php if ($fGame !== ''): ?><span class="f-hint">(<?= htmlspecialchars((string) $fGame, ENT_QUOTES, 'UTF-8') ?>)</span><?php endif; ?></label>
        <select id="f-rarity" name="rarity" data-auto-filter>
            <option value="">Todas</option>
            <?php foreach (($opts['rarities'] ?? []) as $r): ?>
                <option value="<?= htmlspecialchars((string) $r, ENT_QUOTES, 'UTF-8') ?>"<?= f_sel($fRarity, $r) ?>><?= htmlspecialchars((string) $r, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="f-group">
        <label for="f-edition">Edição <?php if ($fGame !== ''): ?><span class="f-hint">(<?= htmlspecialchars((string) $fGame, ENT_QUOTES, 'UTF-8') ?>)</span><?php endif; ?></label>
        <select id="f-edition" name="edition" data-auto-filter>
            <option value="">Todas</option>
            <?php foreach (($opts['editions'] ?? []) as $e): ?>
                <option value="<?= htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') ?>"<?= f_sel($fEdition, $e) ?>><?= htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="f-group">
        <label for="f-sort">Ordenar por</label>
        <select id="f-sort" name="sort" data-auto-filter>
            <?php foreach ($sortLabels as $k => $label): ?>
                <option value="<?= htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8') ?>"<?= f_sel($fSort, $k) ?>><?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="f-actions">
        <?php if ($hasFilter): ?>
            <a class="btn btn-link" href="./index.php">Limpar</a>
        <?php endif; ?>
    </div>
</form>
<script>
(function () {
    var form = document.getElementById('catalog-filters');
    if (!form) { return; }
    form.addEventListener('change', function (e) {
        if (!e.target || !e.target.matches || !e.target.matches('[data-auto-filter]')) {
            return;
        }
        if (e.target.id === 'f-game') {
            var rar = document.getElementById('f-rarity');
            var ed  = document.getElementById('f-edition');
            if (rar) { rar.value = ''; }
            if (ed)  { ed.value = ''; }
        }
        form.submit();
    });
})();
</script>

<?php if (count($cards) === 0): ?>
    <p class="empty"><?= $hasFilter ? 'Nenhuma carta corresponde aos filtros selecionados.' : 'Nenhuma carta registrada ainda.' ?></p>
<?php else: ?>
    <div class="catalog-grid">
        <?php foreach ($cards as $card):
            $initial = function_exists('mb_substr')
                ? mb_strtoupper(mb_substr((string) $card['name_en'], 0, 1))
                : strtoupper(substr((string) $card['name_en'], 0, 1));
        ?>
            <article class="card-tile">
                <div class="tile-media">
                    <span class="tile-empty"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if (!empty($card['image'])): ?>
                        <img src="<?= htmlspecialchars((string) $card['image'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars((string) $card['name_en'], ENT_QUOTES, 'UTF-8') ?>"
                             loading="lazy" onerror="this.remove()">
                    <?php endif; ?>
                </div>
                <div class="tile-body">
                    <h2 class="tile-name"><?= htmlspecialchars((string) $card['name_en'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php if (!empty($card['name_pt'])): ?>
                        <p class="tile-name-pt"><?= htmlspecialchars((string) $card['name_pt'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <p class="tile-edition"><?= !empty($card['edition_name']) ? htmlspecialchars((string) $card['edition_name'], ENT_QUOTES, 'UTF-8') : '—' ?></p>
                    <div class="tile-meta">
                        <span class="tile-game"><?= htmlspecialchars((string) $card['game'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if (!empty($card['rarity'])):
                            $tileColor = $rarityColors[$card['game']][$card['rarity']] ?? null; ?>
                            <span class="badge" <?= c_badge_style($tileColor) ?>><?= htmlspecialchars((string) $card['rarity'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<div class="modal" id="login-modal" aria-hidden="true" aria-labelledby="login-title">
    <div class="modal-backdrop" data-login-close></div>
    <div class="modal-panel" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" data-login-close aria-label="Fechar">&times;</button>
        <h2 id="login-title">Entrar</h2>
        <p class="modal-subtitle">Acesse para incluir, editar ou excluir cartas.</p>

        <div class="alert alert-error" id="login-error" role="alert" hidden></div>

        <form id="login-form" novalidate>
            <div class="field">
                <label for="login-username">Usuário</label>
                <input type="text" id="login-username" name="username" autocomplete="username" required>
            </div>
            <div class="field">
                <label for="login-password">Senha</label>
                <input type="password" id="login-password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn" id="login-submit">Entrar</button>
        </form>
    </div>
</div>

<script src="./assets/js/login.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
