<?php

namespace FireflyIII\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Preferences
 *
 * @codeCoverageIgnore
 * @package FireflyIII\Support\Facades
 */
class Preferences extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'preferences';
    }

}
