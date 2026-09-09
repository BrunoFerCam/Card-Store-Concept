<?php


$pageTitle = 'Edições';
$base      = '..';

$breadcrumbs = [
    ['Dashboard', 'index.php'],
    ['Edições', null],
];

require __DIR__ . '/../layout/header.php';
?>
<div class="page-head">
    <h1>Edições <span class="count"><?= count($editions) ?></span></h1>
</div>

<?php if ($flash !== null): ?>
    <div class="alert alert-<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>" data-dismissible>
        <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<form class="card-form" method="post" action="editions.php" style="margin-bottom:1.5rem;">
    <h2 style="margin-top:0;font-size:1.1rem;">Nova edição</h2>
    <div class="grid">
        <div class="field">
            <label for="game">Card Game <span class="req">*</span></label>
            <select id="game" name="game" required>
                <option value="">Selecione…</option>
                <?php foreach (\EditionController::GAMES as $game): ?>
                    <option value="<?= htmlspecialchars($game, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($game, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="code">Código <span class="req">*</span></label>
            <input type="text" id="code" name="code" maxlength="20" required placeholder="ex.: mh3">
        </div>
        <div class="field">
            <label for="name">Nome <span class="req">*</span></label>
            <input type="text" id="name" name="name" maxlength="120" required placeholder="ex.: Modern Horizons 3">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn">Adicionar</button>
    </div>
</form>

<?php if (count($editions) === 0): ?>
    <p class="empty">Nenhuma edição cadastrada.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Game</th>
                    <th>Código</th>
                    <th>Nome</th>
                    <th class="actions-col">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($editions as $edition): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $edition['game'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><code><?= htmlspecialchars((string) $edition['code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td><?= htmlspecialchars((string) $edition['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions-col">
                        <form class="inline-form" method="post" action="editions.php"
                              data-confirm="<?= htmlspecialchars('Excluir a edição "' . $edition['name'] . '"?', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete">
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

<?php require __DIR__ . '/../layout/footer.php'; ?>
