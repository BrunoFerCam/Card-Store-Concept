<?php


$pageTitle = 'Raridades';
$base      = '..';

$breadcrumbs = [
    ['Dashboard', 'index.php'],
    ['Raridades', null],
];

require __DIR__ . '/../layout/header.php';
?>
<div class="page-head">
    <h1>Raridades <span class="count"><?= count($rarities) ?></span></h1>
</div>

<?php if ($flash !== null): ?>
    <div class="alert alert-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>" data-dismissible>
        <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<form class="card-form" method="post" action="rarities.php" style="margin-bottom:1.5rem;">
    <h2 style="margin-top:0;font-size:1.1rem;">Nova raridade</h2>
    <div class="field" style="max-width:360px;">
        <label for="name">Nome <span class="req">*</span></label>
        <input type="text" id="name" name="name" maxlength="40" required placeholder="ex.: Mythic Rare">
    </div>
    <div class="form-actions">
        <button type="submit" class="btn">Adicionar</button>
    </div>
</form>

<?php if (count($rarities) === 0): ?>
    <p class="empty">Nenhuma raridade cadastrada.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="actions-col">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rarities as $rarity): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $rarity['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions-col">
                        <form class="inline-form" method="post" action="rarities.php"
                              data-confirm="<?= htmlspecialchars('Excluir a raridade "' . $rarity['name'] . '"?', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete">
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

<?php require __DIR__ . '/../layout/footer.php'; ?>
