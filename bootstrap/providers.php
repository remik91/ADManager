<?php

use Illuminate\Pagination\Paginator;

return [
    App\Providers\AppServiceProvider::class,
    Paginator::useBootstrapFive()
];