<?php
namespace Ciebit\ReceiptIrpfTests;

use Ciebit\Files\File;
use Ciebit\Files\Pdfs\Pdf;
use Ciebit\Files\Status as FileStatus;
use Ciebit\ReceiptIrpf\Collection;
use Ciebit\ReceiptIrpf\Receipt;
use Ciebit\ReceiptIrpf\Status;
use PHPUnit\Framework\TestCase;

class CollectionTests extends TestCase
{
    public function getFile(): File
    {
        return new Pdf(
            'Name',
            'uri',
            'application/pdf',
            FileStatus::ACTIVE()
        );
    }

    public function testCreateManual(): void
    {
        $receipt1 = new Receipt(2018, 2017, $this->getFile(), Status::ACTIVE(), null, '1');
        $receipt2 = new Receipt(2017, 2016, $this->getFile(), Status::ACTIVE(), null, '2');

        $collection = new Collection;

        $this->assertCount(0, $collection);

        $collection->add($receipt1, $receipt2);

        $this->assertCount(2, $collection);

        $this->assertEquals($receipt2, $collection->getById(2));
    }
}
