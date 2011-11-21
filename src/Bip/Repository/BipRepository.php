<?php
namespace Bip\Repository;
use Bip\Repository\AbstractRepository;

class BipRepository extends AbstractRepository {

    /**
     * Retrieve Bips by providing a group name
     *
     * @param string $group
     * @return array Bips
     */
    public function fetchAllByGroup($group) {
        $sql = 'SELECT * FROM Bip WHERE "Group" = ?';
        $results = $this->fetchArrayByQuery($sql, array($group));
        return $results;
    }

    /**
     * Retrieve a Bip by providing a name
     *
     * @param string $group
     * @return array Bips
     */
    public function fetchOneByName($name) {
        $sql = 'SELECT * FROM Bip WHERE "Name" = ? LIMIT 1';
        $results = $this->fetchArrayByQuery($sql, array($name));
        if (count($results) != 1) {
            return null;
        }
        return $results[0];
    }
}