<?php
namespace Ciebit\ReceiptIrpf;

use Ciebit\Files\File;
use Ciebit\ReceiptIrpf\Status;

class Receipt
{
    /** @var string */
    private const TYPE = 'file';

    /** @var string */
    private $associationId;

    /** @var File */
    private $file;

    /** @var string */
    private $id;

    /** @var Status */
    private $status;

    /** @var int */
    private $year;

    /** @var int */
    private $yearCalendar;

    public function __construct(
        int $year,
        int $yearCalendar,
        File $file,
        Status $status,
        string $associationId = '',
        string $id = ''
    ) {
        $this->associationId = $associationId;
        $this->file = $file;
        $this->id = $id;
        $this->status = $status;
        $this->year = $year;
        $this->yearCalendar = $yearCalendar;
    }

    public function getAssociationId(): string
    {
        return $this->associationId;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public static function getType(): string
    {
        return self::TYPE;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getYearCalendar(): int
    {
        return $this->yearCalendar;
    }
}
