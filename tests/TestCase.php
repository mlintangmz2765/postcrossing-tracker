<?php

namespace Tests;

use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Throwaway key for this test run, generated on first use. */
    private static ?string $testAppKey = null;

    /**
     * Contact details are encrypted and sessions need a key, but a fresh
     * clone (and CI, which copies .env.example) has an empty APP_KEY. Rather
     * than commit a key, generate a random one for the run when none is set.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        if (blank($app['config']->get('app.key'))) {
            self::$testAppKey ??= 'base64:'.base64_encode(Encrypter::generateKey($app['config']->get('app.cipher')));
            $app['config']->set('app.key', self::$testAppKey);
        }

        return $app;
    }
}
