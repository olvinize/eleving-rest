<?php

namespace App\Utils;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class DbHelper
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function isExists(): bool
    {
        try {
            $this->managerRegistry->getConnection()->createSchemaManager()->listTables();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function create()
    {
        chdir('..');
        shell_exec('php bin/console doctrine:database:create --no-interaction');
        shell_exec('php bin/console doctrine:mig:mig --no-interaction');
        shell_exec('php bin/console doctrine:fix:load --no-interaction');
    }

}