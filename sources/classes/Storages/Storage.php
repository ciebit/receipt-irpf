<?php
namespace Ciebit\ReceiptIrpf\Storages;

use Ciebit\ReceiptIrpf\Receipt;
use Ciebit\ReceiptIrpf\Collection;

interface Storage
{
    public function addFilterByAssociationId(string $operation = '=', string ...$id): self;

    public function addFilterByFileId(string $operation = '=', string ...$id): self;

    public function addFilterById(string $operation = '=', string ...$id): self;

    public function addFilterByYear(string $operation = '=', int ...$year): self;

    public function addFilterByYearCalendar(string $operation = '=', int ...$year): self;

    public function addOrderBy(string $field, string $direction): self;

    public function findAll(): Collection;

    public function findOne(): ?Receipt;

    public function getTotalOfLastQueryItemsWithoutFilters(): int;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;
}
