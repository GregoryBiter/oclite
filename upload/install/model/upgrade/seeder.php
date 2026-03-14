<?php
namespace Opencart\Install\Model\Upgrade;

/**
 * Class Seeder
 *
 * @package Opencart\Install\Model\Upgrade
 */
class Seeder {
    protected $registry;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }
}
