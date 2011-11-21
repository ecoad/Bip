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


    public function persist(Bip $bip) {
        if ($persistedBip = $this->fetchOneByName($bip->getName())) {
            $sql = <<<SQL
UPDATE {$this->table} SET Lat = :lat, Lon = :lon, Accuracy = :accuracy, LastUpdate = :lastUpdate WHERE Name = :name
SQL;
            $query = $this->connection->prepare($sql);
            $params = array(
                ':lat' => $bip->getLat(), 
                ':lon' => $bip->getLon(), 
                ':accuracy' => $bip->getAccuracy(), 
                ':lastUpdate' => $bip->getLastUpdate(),
                ':name' => $bip->getName()
            );
        } else {
            $sql = <<<SQL
INSERT INTO {$this->table} ('Name', 'EmailAddress', 'Lat', 'Lon', 'Accuracy', 'LastUpdate', 'Group')
    VALUES (:name, :emailAddress, :lat, :lon, :accuracy, :lastUpdate, :group)
SQL;

            $query = $this->connection->prepare($sql);
            $params = array(
                ':name' => $bip->getName(),
                ':emailAddress' => $bip->getEmailAddress(),
                ':lat' => $bip->getLat(),
                ':lon' => $bip->getLon(),
                ':accuracy' => $bip->getAccuracy(),
                ':lastUpdate' => $bip->getlastUpdate(),
                ':group' => $bip->getGroup()
            );
        }

        $query->execute($params);
    }
}