<?php

return [
    App\Providers\AppServiceProvider::class,
    // SOLID DIP: bind repository interfaces once, at the composition root.
    Infrastructure\Providers\RepositoryServiceProvider::class,
    Infrastructure\Providers\DomainBridgeServiceProvider::class,
];
