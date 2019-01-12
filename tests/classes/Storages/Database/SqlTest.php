<?php
namespace Ciebit\ReceiptIrpfTests\Storages\Database;

use Ciebit\Files\File;
use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Status as FileStatus;
use Ciebit\Files\Storages\Database\Sql as FileSql;
use Ciebit\ReceiptIrpf\Receipt;
use Ciebit\ReceiptIrpf\Status;
use Ciebit\ReceiptIrpf\Storages\Database\Sql;
use Ciebit\ReceiptIrpf\Storages\Storage;
use Ciebit\ReceiptIrpfTests\Helpers\Connection;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase
{
    /** @var Storage */
    private $storage;

    public function getStorage(): Storage
    {
        if ($this->storage instanceof Storage) {
            return $this->storage;
        }

        $pdo = Connection::getPdo();
        $fileDatabase = new FileSql($pdo);
        return $this->storage = new Sql($pdo, $fileDatabase);
    }

    public function testFindAll(): void
    {
        $storage = $this->getStorage();
        $collection = $storage->findAll();

        $this->assertCount(3, $collection);
        $this->assertEquals(2017, $collection->getById('2')->getYear());
    }

    public function testFindOne(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->findOne();

        $this->assertEquals(1, $receipt->getId());
        $this->assertEquals('title-file-1.pdf', $receipt->getFile()->getUri());
        $this->assertEquals(2016, $receipt->getYear());
        $this->assertEquals(2015, $receipt->getYearCalendar());
        $this->assertEquals(1, $receipt->getAssociationId());
        $this->assertEquals(Status::ACTIVE(), $receipt->getStatus());
    }

    public function testFilterByAssociationId(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->addFilterByAssociationId('=', '3')->findOne();

        $this->assertEquals(3, $receipt->getId());
    }

    public function testFilterByFileId(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->addFilterByFileId('=', '2')->findOne();

        $this->assertEquals(2, $receipt->getId());
    }

    public function testFilterById(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->addFilterById('=', '1')->findOne();

        $this->assertEquals(1, $receipt->getId());
    }

    public function testFilterByYear(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->addFilterByYear('=', 2018)->findOne();

        $this->assertEquals(3, $receipt->getId());
    }

    public function testFilterByYearCalendar(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->addFilterByYearCalendar('=', 2016)->findOne();

        $this->assertEquals(2, $receipt->getId());
    }

    public function testOrderBy(): void
    {
        $storage = $this->getStorage();
        $receipt = $storage->addOrderBy('year', 'DESC')->findOne();

        $this->assertEquals(3, $receipt->getId());
    }

    public function testLimit(): void
    {
        $storage = $this->getStorage();
        $collection = $storage->setLimit(2)->findAll();

        $this->assertCount(2, $collection);
        $this->assertEquals(3, $storage->getTotalOfLastQueryItemsWithoutFilters());
    }

    public function testOffset(): void
    {
        $storage = $this->getStorage();
        $collection = $storage->setOffset(1)->setLimit(10)->findAll();
        $this->assertEquals(2, $collection->getArrayObject()->offsetGet(0)->getId(0));
    }
}
