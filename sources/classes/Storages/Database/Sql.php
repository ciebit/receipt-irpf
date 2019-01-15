<?php
namespace Ciebit\ReceiptIrpf\Storages\Database;

use Ciebit\Files\Storages\Database\Database as FileDatabase;
use Ciebit\ReceiptIrpf\Collection;
use Ciebit\ReceiptIrpf\Receipt;
use Ciebit\ReceiptIrpf\Status;
use Ciebit\ReceiptIrpf\Storages\Storage;
use Ciebit\ReceiptIrpf\Storages\Database\Database;
use Ciebit\ReceiptIrpf\Storages\Database\SqlHelper;
use Exception;
use PDO;

use function array_column;
use function array_map;
use function implode;
use function intval;

class Sql implements Database
{
    public const FIELD_ASSOCIATION_ID = 'association_id';
    public const FIELD_FILE_ID = 'file_id';
    public const FIELD_ID = 'id';
    public const FIELD_STATUS = 'status';
    public const FIELD_YEAR = 'year';
    public const FIELD_YEAR_CALENDAR = 'year_calendar';

    /** @var FileDatabase */
    private $fileDatabase;

    /** @var PDO */
    private $pdo;

    /** @var SqlHelper */
    private $sqlHelper;

    /** @var string */
    private $table;

    /** @var int */
    private $totalItemsLastQuery;

    public function __construct(PDO $pdo, FileDatabase $fileDatabase)
    {
        $this->fileDatabase = $fileDatabase;
        $this->pdo = $pdo;
        $this->sqlHelper = new SqlHelper;
        $this->table = 'cb_receipt_irpf';
        $this->totalItemsLastQuery = 0;
    }

    private function addFilter(string $fieldName, int $type, string $operator, ...$value): self
    {
        $field = "`{$this->table}`.`{$fieldName}`";
        $this->sqlHelper->addFilterBy($field, $type, $operator, ...$value);
        return $this;
    }

    public function addFilterByAssociationId(string $operation = '=', string ...$id): Storage
    {
        $this->addFilter(self::FIELD_ASSOCIATION_ID, PDO::PARAM_STR, $operation, ...$id);
        return $this;
    }

    public function addFilterByFileId(string $operation = '=', string ...$id): Storage
    {
        $this->addFilter(self::FIELD_FILE_ID, PDO::PARAM_STR, $operation, ...$id);
        return $this;
    }

    public function addFilterById(string $operation = '=', string ...$id): Storage
    {
        $this->addFilter(self::FIELD_ID, PDO::PARAM_STR, $operation, ...$id);
        return $this;
    }

    public function addFilterByYear(string $operation = '=', int ...$year): Storage
    {
        $this->addFilter(self::FIELD_YEAR, PDO::PARAM_INT, $operation, ...$year);
        return $this;
    }

    public function addFilterByYearCalendar(string $operation = '=', int ...$year): Storage
    {
        $this->addFilter(self::FIELD_YEAR_CALENDAR, PDO::PARAM_INT, $operation, ...$year);
        return $this;
    }

    public function addOrderBy(string $field, string $direction): Storage
    {
        $this->sqlHelper->addOrderBy("`{$this->table}`.`{$field}`", $direction);
        return $this;
    }

    /**
    * @throws Exception
    */
    public function create(array $data): Receipt
    {
        if ($data['file'] == null) {
            throw new Exception('ciebit.receipt_ifpf.storages.database.file_not_found', 3);
        }

        return new Receipt(
            (int) $data['year'],
            (int) $data['year_calendar'],
            $data['file'],
            new Status((int) $data['status']),
            (string) $data['association_id'],
            (string) $data['id']
        );
    }

    /**
    * @throws Exception
    */
    public function findAll(): Collection
    {
        $statement = $this->pdo->prepare(
            $sql = "SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table}
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            {$this->sqlHelper->generateSqlLimit()}"
        );

        $this->sqlHelper->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.receipt-irpf.storages.database.get_error', 2);
        }

        $this->totalItemsLastQuery = (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $collection = new Collection;

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($data)) {
            return $collection;
        }

        $fileIds = array_map('intval', array_column($data, self::FIELD_FILE_ID));
        $fileCollection = (clone $this->fileDatabase)->addFilterByIds('=', ...$fileIds)->getAll();

        foreach ($data as $receiptData) {
            $receiptData['file'] = $fileCollection->getById($receiptData[self::FIELD_FILE_ID]);
            $receipt = $this->create($receiptData);
            $collection->add($receipt);
        }

        return $collection;
    }

    /**
    * @throws Exception
    */
    public function findOne(): ?Receipt
    {
        $statement = $this->pdo->prepare(
            "SELECT
            {$this->getFields()}
            FROM {$this->table}
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            LIMIT 1"
        );

        $this->sqlHelper->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.receipt_ifpf.storages.database.get_error', 2);
        }

        $receiptData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($receiptData == false) {
            return null;
        }

        $this->totalItemsLastQuery = (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();

        $receiptData['file'] = (clone $this->fileDatabase)
        ->addFilterById($receiptData[self::FIELD_FILE_ID])
        ->get();

        return $this->create($receiptData);
    }

    private function getFields(): string
    {
        $table = $this->table;
        $fields = [
            self::FIELD_ASSOCIATION_ID,
            self::FIELD_FILE_ID,
            self::FIELD_ID,
            self::FIELD_STATUS,
            self::FIELD_YEAR,
            self::FIELD_YEAR_CALENDAR
        ];
        $fields = array_map(
            function($field) use ($table){
                return "`{$table}`.`{$field}`";
            },
            $fields
        );
        return implode(',', $fields);
    }

    public function getTotalOfLastQueryItemsWithoutFilters(): int
    {
        return $this->totalItemsLastQuery;
    }

    public function setLimit(int $limit): Storage
    {
        $this->sqlHelper->setLimit($limit);
        return $this;
    }

    public function setOffset(int $offset): Storage
    {
        $this->sqlHelper->setOffset($offset);
        return $this;
    }

    public function setTable(string $name): self
    {
        $this->table = $name;
        return $this;
    }
}
