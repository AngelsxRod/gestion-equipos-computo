<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

redirect(usuario_actual() ? '/dashboard.php' : '/login.php');
