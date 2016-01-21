<?php

namespace Fiorello\AffinityIntegration\Facades;

use Illuminate\Support\Facades\Facade;

class Affinity extends Facade {

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'affinity';
    }

}