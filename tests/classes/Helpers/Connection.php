<?php
namespace Ciebit\ReceiptIrpfTests\Helpers;

use PDO;

class Connection
{
    /** @var PDO */
    private static $pdo;

    private static function getSettings(): array
    {
        return (include __DIR__.'/../../settings.php')['database'];
    }

    public static function getPdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $settings = self::getSettings();

        return self::$pdo = new PDO(
            "mysql:dbname={$settings['name']};"
            ."host={$settings['host']};"
            ."port={$settings['port']};"
            ."charset={$settings['charset']}",
            $settings['user'],
            $settings['password']
        );
    }
}
