<?php
namespace Bip\Repository;
use Bip\Repository\AbstractRepository;
use Bip\Entity\Bip;

class BipRepository extends AbstractRepository {

    /**
     * @var string $table
     */
    protected $table = 'Bip';

    /**
     * Retrieve Bips by providing a group name
     *
     * @param string $group
     * @return array Bips
     */
    public function fetchAllByGroup($group) {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE "Group" = ?';
        $results = $this->fetchArrayByQuery($sql, array($group));
        return $results;
    }

    /**
     * Retrieve a Bip by providing a name
     *
     * @param string $group
     * @return Bip
     */
    public function fetchOneByName($name) {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE "Name" = ? LIMIT 1';
        $results = $this->fetchArrayByQuery($sql, array($name));
        if (count($results) !== 1) {
            return null;
        }
        return $results[0];
    }

    public function updateBip(Bip $bip) {
        if ($persistedBip = $this->fetchOneByName($bip->getName())) {
            $sql = <<<SQL
UPDATE {$this->table} SET Lat = ?, Lon = ?, Accuracy = ? WHERE Name = ?
SQL;
            $query = $this->connection->prepare($sql);
            $query->execute(array($bip->getLat(), $bip->getLon(), $bip->getAccuracy(), $bip->getName()));
            $this->connection->exec($query);
        }

    }
}