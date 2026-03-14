<?php

declare(strict_types=1);

namespace Oclite\System;

/**
 * Minimal bootstrapper for the packaged "system" core.
 *
 * Usage example:
 *   \Oclite\System\Bootstrap::init([
 *       'APPLICATION'   => 'Catalog',
 *       'DIR_OPENCART'  => '/var/www/html/',
 *       'DIR_SYSTEM'    => '/var/www/html/system/',
 *       'DIR_APPLICATION' => '/var/www/html/catalog/',
 *       'DIR_CONFIG'    => '/var/www/html/system/config/',
 *   ]);
 *
 * The method will define provided constants (unless already defined),
 * include the packaged `framework.php` and return the created registry.
 */
class Bootstrap
{
    /**
     * Initialize the packaged system core.
     *
     * @param array<string,string> $constants Map of constant name => value to define before boot
     * @return \Opencart\System\Engine\Registry|null Returns registry when boot completed, or null on failure
     */
    public static function init(array $constants = []): ?\Opencart\System\Engine\Registry
    {
        foreach ($constants as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }

        // include the packaged framework bootstrap (it will populate $registry)
        $framework = __DIR__ . DIRECTORY_SEPARATOR . 'framework.php';

        if (!is_file($framework)) {
            throw new \RuntimeException("Packaged framework.php not found at: {$framework}");
        }

        require $framework;

        return $registry ?? null;
    }
}
