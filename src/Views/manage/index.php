<?php


if (!function_exists('m_e')) {
    function m_e($s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('m_badge_style')) {
    function m_badge_style($hex): string {
        if (!$hex || !preg_match('/^#([0-9a-fA-F]{6})$/', $hex, $m)) {
            return '';
        }
        $n  = hexdec($m[1]);
        $r  = ($n >> 16) & 255;
        $g  = ($n >> 8) & 255;
        $b  = $n & 255;
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        $fg = $lum > 0.6 ? '#111827' : '#ffffff';
        return 'style="background:' . $hex . ';color:' . $fg . ';"';
    }
}

$pageTitle = 'Gerenciar';
$base      = '..';

require __DIR__ . '/../layout/header.php';
?>

<nav class="subnav" aria-label="Seções de gerenciamento">
    <a href="gerenciar.php?tab=cartas" class="<?= $activeTab === 'cartas' ? 'active' : '' ?>">Cartas</a>
    <a href="gerenciar.php?tab=edicoes" class="<?= $activeTab === 'edicoes' ? 'active' : '' ?>">Edições</a>
    <a href="gerenciar.php?tab=raridades" class="<?= $activeTab === 'raridades' ? 'active' : '' ?>">Raridades</a>
</nav>

<?php if ($flash !== null): ?>
    <div class="alert alert-<?= m_e($flash['type']) ?>" data-dismissible>
        <?= m_e($flash['message']) ?>
    </div>
<?php endif; ?>


<?php if ($activeTab === 'cartas'): ?>
    <div class="page-head">
        <h1>Cartas <span class="count"><?= count($cards) ?></span></h1>
        <a class="btn" href="gerenciar.php?tab=cartas&nova=carta" data-open-modal="card-modal">Nova carta</a>
    </div>

    <?php if (count($cards) === 0): ?>
        <p class="empty">Nenhuma carta cadastrada.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>ID</th>
                        <th>Nome (EN)</th>
                        <th>Nome (PT)</th>
                        <th>Game</th>
                        <th>Edição</th>
                        <th>Raridade</th>
                        <th class="actions-col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cards as $card): ?>
                    <?php $cardColor = $rarityColors[$card['game']][$card['rarity']] ?? null; ?>
                    <tr>
                        <td class="thumb-cell">
                            <span class="card-thumb">
                                <?php if (!empty($card['image'])): ?>
                                    <img src="<?= m_e($card['image']) ?>" alt="<?= m_e($card['name_en']) ?>" loading="lazy" onerror="this.remove()">
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><?= (int) $card['id'] ?></td>
                        <td><?= m_e($card['name_en']) ?></td>
                        <td><?= !empty($card['name_pt']) ? m_e($card['name_pt']) : '<span class="muted">—</span>' ?></td>
                        <td><?= m_e($card['game']) ?></td>
                        <td><?= !empty($card['edition_name']) ? m_e($card['edition_name']) : '<span class="muted">—</span>' ?></td>
                        <td>
                            <?php if (!empty($card['rarity'])): ?>
                                <span class="badge" <?= m_badge_style($cardColor) ?>><?= m_e($card['rarity']) ?></span>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-col">
                            <a class="btn btn-sm" href="gerenciar.php?tab=cartas&edit=<?= (int) $card['id'] ?>">Editar</a>
                            <form class="inline-form" method="post" action="gerenciar.php"
                                  data-confirm="<?= m_e('Excluir a carta "' . $card['name_en'] . '"?') ?>">
                                <input type="hidden" name="form" value="delete">
                                <input type="hidden" name="kind" value="card">
                                <input type="hidden" name="id" value="<?= (int) $card['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>


<?php if ($activeTab === 'edicoes'): ?>
    <div class="page-head">
        <h1>Edições <span class="count"><?= count($editions) ?></span></h1>
        <a class="btn" href="gerenciar.php?tab=edicoes&nova=edicao">Nova edição</a>
    </div>

    <?php if (count($editions) === 0): ?>
        <p class="empty">Nenhuma edição cadastrada.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Game</th><th>Código</th><th>Nome</th><th class="actions-col">Ações</th></tr>
                </thead>
                <tbody>
                <?php foreach ($editions as $edition): ?>
                    <tr>
                        <td><?= m_e($edition['game']) ?></td>
                        <td><code><?= m_e($edition['code']) ?></code></td>
                        <td><?= m_e($edition['name']) ?></td>
                        <td class="actions-col">
                            <form class="inline-form" method="post" action="gerenciar.php"
                                  data-confirm="<?= m_e('Excluir a edição "' . $edition['name'] . '"?') ?>">
                                <input type="hidden" name="form" value="delete">
                                <input type="hidden" name="kind" value="edition">
                                <input type="hidden" name="id" value="<?= (int) $edition['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>


<?php if ($activeTab === 'raridades'): ?>
    <div class="page-head">
        <h1>Raridades <span class="count"><?= count($rarities) ?></span></h1>
        <a class="btn" href="gerenciar.php?tab=raridades&nova=raridade">Nova raridade</a>
    </div>

    <?php if (count($rarities) === 0): ?>
        <p class="empty">Nenhuma raridade cadastrada.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Game</th><th>Raridade</th><th class="actions-col">Ações</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rarities as $rarity): ?>
                    <tr>
                        <td><?= m_e($rarity['game']) ?></td>
                        <td>
                            <span class="rarity-pick">
                                <span class="color-swatch" style="background:<?= m_e($rarity['color']) ?>;"></span>
                                <?= m_e($rarity['name']) ?>
                            </span>
                        </td>
                        <td class="actions-col">
                            <form class="inline-form" method="post" action="gerenciar.php"
                                  data-confirm="<?= m_e('Excluir a raridade "' . $rarity['name'] . '"?') ?>">
                                <input type="hidden" name="form" value="delete">
                                <input type="hidden" name="kind" value="rarity">
                                <input type="hidden" name="id" value="<?= (int) $rarity['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>


<?php if ($openModal === 'card'): $edVal = $cardData['edition_id'] ?? ''; $rarVal = $cardData['rarity'] ?? ''; ?>
<div class="modal open" id="card-modal" aria-hidden="false" aria-labelledby="card-modal-title">
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-panel modal-panel-lg" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" data-close-modal aria-label="Fechar">&times;</button>
        <h2 id="card-modal-title"><?= $cardMode === 'edit' ? 'Editar carta' : 'Nova carta' ?></h2>
        <p class="modal-subtitle">Preencha os dados da carta.</p>

        <?php if ($cardErrors !== []): ?>
            <div class="alert alert-error" data-dismissible>Verifique os campos destacados.</div>
        <?php endif; ?>

        <form class="card-form" method="post" action="gerenciar.php">
            <input type="hidden" name="form" value="<?= $cardMode === 'edit' ? 'card_update' : 'card_create' ?>">
            <?php if ($cardMode === 'edit'): ?><input type="hidden" name="card_id" value="<?= (int) $cardId ?>"><?php endif; ?>

            <div class="grid">
                <div class="field">
                    <label for="name_en">Nome em inglês <span class="req">*</span></label>
                    <input type="text" id="name_en" name="name_en" value="<?= m_e($cardData['name_en'] ?? '') ?>" maxlength="120" required<?= isset($cardErrors['name_en']) ? ' class="input-error"' : '' ?>>
                    <?php if (isset($cardErrors['name_en'])): ?><p class="field-error"><?= m_e($cardErrors['name_en']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                    <label for="name_pt">Nome em português</label>
                    <input type="text" id="name_pt" name="name_pt" value="<?= m_e($cardData['name_pt'] ?? '') ?>" maxlength="120">
                    <?php if (isset($cardErrors['name_pt'])): ?><p class="field-error"><?= m_e($cardErrors['name_pt']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                    <label for="game">Card Game <span class="req">*</span></label>
                    <select id="game" name="game" required<?= isset($cardErrors['game']) ? ' class="input-error"' : '' ?>>
                        <option value="">Selecione…</option>
                        <?php foreach ($games as $g): ?>
                            <option value="<?= m_e($g) ?>" data-game-key="<?= m_e($gameKeys[$g] ?? '') ?>" <?= ($cardData['game'] ?? '') === $g ? 'selected' : '' ?>><?= m_e($g) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($cardErrors['game'])): ?><p class="field-error"><?= m_e($cardErrors['game']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                    <label for="edition_id">Edição <span class="req">*</span></label>
                    <select id="edition_id" name="edition_id" data-current="<?= m_e($edVal) ?>" required disabled<?= isset($cardErrors['edition_name']) ? ' class="input-error"' : '' ?>>
                        <option value="">Selecione o Card Game primeiro</option>
                    </select>
                    <input type="hidden" id="edition_name" name="edition_name" value="<?= m_e($cardData['edition_name'] ?? '') ?>">
                    <?php if (isset($cardErrors['edition_name'])): ?><p class="field-error"><?= m_e($cardErrors['edition_name']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                    <label for="rarity">Raridade</label>
                    <select id="rarity" name="rarity" data-current="<?= m_e($rarVal) ?>" disabled>
                        <option value="">Selecione o Card Game primeiro</option>
                    </select>
                    <?php if (isset($cardErrors['rarity'])): ?><p class="field-error"><?= m_e($cardErrors['rarity']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                    <label for="image">Imagem da carta</label>
                    <input type="text" id="image" name="image" value="<?= m_e($cardData['image'] ?? '') ?>" maxlength="255" placeholder="URL ou caminho da imagem">
                    <div class="image-preview" id="image-preview">Sem pré-visualização</div>
                    <?php if (isset($cardErrors['image'])): ?><p class="field-error"><?= m_e($cardErrors['image']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Salvar</button>
                <button type="button" class="btn btn-secondary" data-close-modal>Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if ($openModal === 'edition'): ?>
<div class="modal open" id="edition-modal" aria-hidden="false" aria-labelledby="edition-modal-title">
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-panel" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" data-close-modal aria-label="Fechar">&times;</button>
        <h2 id="edition-modal-title">Nova edição</h2>
        <p class="modal-subtitle">Adicione uma edição ao Card Game.</p>

        <?php if ($editionErrors !== []): ?>
            <div class="alert alert-error" data-dismissible>Verifique os campos destacados.</div>
        <?php endif; ?>

        <form method="post" action="gerenciar.php">
            <input type="hidden" name="form" value="edition">
            <div class="field">
                <label for="ed_game">Card Game <span class="req">*</span></label>
                <select id="ed_game" name="game" required<?= isset($editionErrors['game']) ? ' class="input-error"' : '' ?>>
                    <option value="">Selecione…</option>
                    <?php foreach ($games as $g): ?>
                        <option value="<?= m_e($g) ?>" <?= ($editionData['game'] ?? '') === $g ? 'selected' : '' ?>><?= m_e($g) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($editionErrors['game'])): ?><p class="field-error"><?= m_e($editionErrors['game']) ?></p><?php endif; ?>
            </div>
            <div class="field">
                <label for="ed_code">Código <span class="req">*</span></label>
                <input type="text" id="ed_code" name="code" value="<?= m_e($editionData['code'] ?? '') ?>" maxlength="20" required placeholder="ex.: mh3"<?= isset($editionErrors['code']) ? ' class="input-error"' : '' ?>>
                <?php if (isset($editionErrors['code'])): ?><p class="field-error"><?= m_e($editionErrors['code']) ?></p><?php endif; ?>
            </div>
            <div class="field">
                <label for="ed_name">Nome <span class="req">*</span></label>
                <input type="text" id="ed_name" name="name" value="<?= m_e($editionData['name'] ?? '') ?>" maxlength="120" required placeholder="ex.: Modern Horizons 3"<?= isset($editionErrors['name']) ? ' class="input-error"' : '' ?>>
                <?php if (isset($editionErrors['name'])): ?><p class="field-error"><?= m_e($editionErrors['name']) ?></p><?php endif; ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Adicionar</button>
                <button type="button" class="btn btn-secondary" data-close-modal>Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?php if ($openModal === 'rarity'): ?>
<div class="modal open" id="rarity-modal" aria-hidden="false" aria-labelledby="rarity-modal-title">
    <div class="modal-backdrop" data-close-modal></div>
    <div class="modal-panel" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" data-close-modal aria-label="Fechar">&times;</button>
        <h2 id="rarity-modal-title">Nova raridade</h2>
        <p class="modal-subtitle">A raridade ficará ligada a um Card Game e terá uma cor.</p>

        <?php if ($rarityErrors !== []): ?>
            <div class="alert alert-error" data-dismissible>Verifique os campos destacados.</div>
        <?php endif; ?>

        <form method="post" action="gerenciar.php">
            <input type="hidden" name="form" value="rarity">
            <div class="field">
                <label for="ra_game">Card Game <span class="req">*</span></label>
                <select id="ra_game" name="game" required<?= isset($rarityErrors['game']) ? ' class="input-error"' : '' ?>>
                    <option value="">Selecione…</option>
                    <?php foreach ($games as $g): ?>
                        <option value="<?= m_e($g) ?>" <?= ($rarityData['game'] ?? '') === $g ? 'selected' : '' ?>><?= m_e($g) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($rarityErrors['game'])): ?><p class="field-error"><?= m_e($rarityErrors['game']) ?></p><?php endif; ?>
            </div>
            <div class="field">
                <label for="ra_name">Nome <span class="req">*</span></label>
                <input type="text" id="ra_name" name="name" value="<?= m_e($rarityData['name'] ?? '') ?>" maxlength="40" required placeholder="ex.: Mythic Rare"<?= isset($rarityErrors['name']) ? ' class="input-error"' : '' ?>>
                <?php if (isset($rarityErrors['name'])): ?><p class="field-error"><?= m_e($rarityErrors['name']) ?></p><?php endif; ?>
            </div>
            <div class="field">
                <label for="ra_color">Cor <span class="req">*</span></label>
                <input type="color" id="ra_color" name="color" value="<?= preg_match('/^#[0-9a-fA-F]{6}$/', $rarityData['color'] ?? '') ? m_e($rarityData['color']) : '#6b7280' ?>" required>
                <?php if (isset($rarityErrors['color'])): ?><p class="field-error"><?= m_e($rarityErrors['color']) ?></p><?php endif; ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Adicionar</button>
                <button type="button" class="btn btn-secondary" data-close-modal>Cancelar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="../assets/js/editions.js"></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
