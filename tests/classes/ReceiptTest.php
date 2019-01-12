<?php
namespace Ciebit\ReceiptIrpfTests;

use Ciebit\Files\File;
use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Status as FileStatus;
use Ciebit\ReceiptIrpf\Receipt;
use Ciebit\ReceiptIrpf\Status;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    private const ASSOCIATION_ID = '4';
    private const ID = '2';
    private const STATUS = 3;
    private const YEAR = '2018';
    private const YEAR_CALENDAR = '2017';

    public function getFile(): File
    {
        return new Pdf(
            'Name',
            'uri',
            'application/pdf',
            FileStatus::ACTIVE()
        );
    }

    public function testCreateComplete(): void
    {
        $receipt = new Receipt(
            self::YEAR,
            self::YEAR_CALENDAR,
            $this->getFile(),
            new Status(self::STATUS),
            self::ASSOCIATION_ID,
            self::ID
        );

        $this->assertEquals(self::ASSOCIATION_ID, $receipt->getAssociationId());
        $this->assertInstanceOf(File::class, $receipt->getFile());
        $this->assertEquals(self::ID, $receipt->getId());
        $this->assertEquals(self::STATUS, $receipt->getStatus()->getValue());
        $this->assertEquals(self::YEAR, $receipt->getYear());
        $this->assertEquals(self::YEAR_CALENDAR, $receipt->getYearCalendar());
    }

    public function testCreateMinimal(): void
    {
        $receipt = new Receipt(
            self::YEAR,
            self::YEAR_CALENDAR,
            $this->getFile(),
            new Status(self::STATUS)
        );

        $this->assertEquals('', $receipt->getAssociationId());
        $this->assertInstanceOf(File::class, $receipt->getFile());
        $this->assertEquals('', $receipt->getId());
        $this->assertEquals(self::STATUS, $receipt->getStatus()->getValue());
        $this->assertEquals(self::YEAR, $receipt->getYear());
        $this->assertEquals(self::YEAR_CALENDAR, $receipt->getYearCalendar());
    }

    public function testCreateMinimalWithNull(): void
    {
        $receipt = new Receipt(
            self::YEAR,
            self::YEAR_CALENDAR,
            $this->getFile(),
            new Status(self::STATUS),
            null,
            null
        );

        $this->assertEquals('', $receipt->getAssociationId());
        $this->assertEquals('', $receipt->getId());
    }
}
