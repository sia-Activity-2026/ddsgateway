<?php

namespace App\Support;

use Laravel\Lumen\Application as LumenApplication;

class Application extends LumenApplication
{
    /**
     * Get the fallback locale for the application.
     *
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this['config']->get('app.fallback_locale');
    }
}
