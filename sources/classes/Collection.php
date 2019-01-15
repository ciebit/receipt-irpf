<?php
namespace Ciebit\ReceiptIrpf;

use ArrayIterator;
use ArrayObject;
use Ciebit\ReceiptIrpf\Receipt;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    /** @var ArrayObject */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayObject;
    }

    public function add(Receipt ...$receipts): self
    {
        foreach ($receipts as $receipt) {
            $this->items->append($receipt);
        }

        return $this;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->items;
    }

    public function getById(string $id): ?Receipt
    {
        foreach ($this->getIterator() as $receipt) {
            if ($receipt->getId() == $id) {
                return $receipt;
            }
        }
        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->items->getIterator();
    }

    public function count(): int
    {
        return $this->items->count();
    }
}
