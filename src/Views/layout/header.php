<?php


require_once __DIR__ . '/../../Auth.php';

$base      = $base ?? '..';
$pageTitle = $pageTitle ?? 'Card Store';
$user      = $user ?? Auth::user();
$isLogged  = $user !== null;
$username  = htmlspecialchars((string) ($user['username'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (t !== 'light' && t !== 'dark') {
                    t = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {}
        })();
    </script>
    <title><?= htmlspecialchars((string) $pageTitle, ENT_QUOTES, 'UTF-8') ?> — Card Store</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= $base ?>/index.php">Card Store</a>
    <nav>
        <a class="btn btn-link" href="<?= $base ?>/index.php">Cartas</a>
        <?php if ($isLogged): ?>
            <a class="btn btn-link" href="<?= $base ?>/admin/gerenciar.php">Gerenciar</a>
            <a class="btn btn-link" href="<?= $base ?>/logout.php">Sair</a>
        <?php else: ?>
            <a class="btn btn-nav" href="<?= $base ?>/login.php" data-login-open>Entrar</a>
        <?php endif; ?>
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Alternar tema claro/escuro" title="Alternar tema">
            <svg class="icon icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="icon icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </nav>
</header>
<main class="dashboard">
<?php if (!empty($breadcrumbs)): ?>
    <nav class="breadcrumbs" aria-label="Caminho">
        <?php $last = count($breadcrumbs) - 1; foreach ($breadcrumbs as $i => $bc): ?>
            <?php $crumbLabel = $bc[0] ?? ''; $crumbHref = $bc[1] ?? null; ?>
            <?php if ($i === $last): ?>
                <span class="crumb"><?= htmlspecialchars((string) $crumbLabel, ENT_QUOTES, 'UTF-8') ?></span>
            <?php else: ?>
                <a class="crumb" href="<?= htmlspecialchars((string) $crumbHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $crumbLabel, ENT_QUOTES, 'UTF-8') ?></a>
                <span class="crumb-sep" aria-hidden="true">›</span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
