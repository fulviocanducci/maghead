<?php
use Maghead\Testing\ModelTestCase;
use Maghead\TableStatus\MySQLTableStatus;
use AuthorBooks\Model\AuthorSchema;

/**
 * @group table-status
 * @group mysql
 */
class MySQLTableStatusTest extends ModelTestCase
{
    protected $onlyDriver = 'mysql';

    public function models()
    {
        return [new AuthorSchema];
    }

    public function testQuerySummary()
    {
        $conn = $this->getMasterConnection();
        $status = new MySQLTableStatus($conn);
        $summary = $status->querySummary(['authors']);
        $this->assertNotEmpty($summary);
    }

    public function testQueryDetails()
    {
        $conn = $this->getMasterConnection();
        $status = new MySQLTableStatus($conn);
        $summary = $status->queryDetails(['authors']);
        $this->assertNotEmpty($summary);
    }
}
