<?php


$pageTitle = 'Cartas';
$base      = '..';

$breadcrumbs = [
    ['Dashboard', 'index.php'],
    ['Cartas', null],
];

require __DIR__ . '/../layout/header.php';
?>
<div class="page-head">
    <h1>Cartas <span class="count"><?= count($cards) ?></span></h1>
    <a class="btn" href="cards-create.php">Nova carta</a>
</div>

<?php if ($flash !== null): ?>
    <div class="alert alert-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>" data-dismissible>
        <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if (count($cards) === 0): ?>
    <p class="empty">Nenhuma carta cadastrada ainda.</p>
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
                <tr>
                    <td class="thumb-cell">
                        <span class="card-thumb">
                            <?php if (!empty($card['image'])): ?>
                                <img src="<?= htmlspecialchars((string) $card['image'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars((string) $card['name_en'], ENT_QUOTES, 'UTF-8') ?>"
                                     loading="lazy" onerror="this.remove()">
                            <?php endif; ?>
                        </span>
                    </td>
                    <td><?= (int) $card['id'] ?></td>
                    <td><?= htmlspecialchars((string) $card['name_en'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($card['name_pt']) ? htmlspecialchars((string) $card['name_pt'], ENT_QUOTES, 'UTF-8') : '<span class="muted">—</span>' ?></td>
                    <td><?= htmlspecialchars((string) $card['game'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($card['edition_name']) ? htmlspecialchars((string) $card['edition_name'], ENT_QUOTES, 'UTF-8') : '<span class="muted">—</span>' ?></td>
                    <td><?= !empty($card['rarity']) ? '<span class="badge">' . htmlspecialchars((string) $card['rarity'], ENT_QUOTES, 'UTF-8') . '</span>' : '<span class="muted">—</span>' ?></td>
                    <td class="actions-col">
                        <a class="btn btn-sm" href="cards-edit.php?id=<?= (int) $card['id'] ?>">Editar</a>
                        <form class="inline-form" method="post" action="cards-delete.php"
                              data-confirm="<?= htmlspecialchars('Excluir a carta "' . $card['name_en'] . '"?', ENT_QUOTES, 'UTF-8') ?>">
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

<?php require __DIR__ . '/../layout/footer.php'; ?>
