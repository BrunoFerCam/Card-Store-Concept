<?php

declare(strict_types=1);



$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

header('Location: gerenciar.php?tab=cartas&edit=' . $id, true, 302);
exit;
