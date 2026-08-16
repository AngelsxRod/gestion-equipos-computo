<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use App\Auth\Auth;

Auth::logout();
redirect('/login.php');
