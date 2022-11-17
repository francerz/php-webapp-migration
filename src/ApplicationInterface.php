<?php

namespace Francerz\WebappMigration;

use Psr\Http\Message\ServerRequestInterface;

interface ApplicationInterface
{
    public function run(?ServerRequestInterface $request = null);
}
