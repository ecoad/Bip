<?php
namespace Bip\Repository;

use \PDO;
use \PDOException;
use \Pimple;

abstract class AbstractRepository {
    /**
     * @var Pimple $container
     */
    protected $container;

    /**
     * @param PDO $connection
     */
    protected $connection;

    /**
     * Set the DI service container
     *
     * @param Pimple $container
     * @return Repository
     */
    public function setContainer(Pimple $container) {
        $this->container = $container;
        return $this;
    }

    /**
     * Connect to database
     * 
     * @return Repository
     */
    public function connect() {
        $this->connection = new PDO($this->container['db.driver'] . ':' . $this->container['db.path']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    /**
     * TEMPORARY
     */
    public function devel() {
        if (!$this->connection) {
            $this->connect();
        }

        try {
            $result = $this->connection->exec("ALTER TABLE 'Bip' ADD COLUMN 'Group' TEXT");
        } catch (PDOException $exception) {
            echo $exception->getMessage(); exit;
        }
    }

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

    /**
     * Retrieve array of results by query and params
     * 
     * @param string $sql
     * @param array $params
     *
     * @return array $results
     */
    public function fetchArrayByQuery($sql, array $params = array()) {
        if (!$this->connection) {
            $this->connect();
        }

        try {
            $query = $this->connection->prepare($sql);
            $query->execute($params);
        } catch (PDOException $exception) {
            echo $exception->getMessage(); exit;
        }

        $query->setFetchMode(PDO::FETCH_ASSOC);

        $results = array();
        while ($result = $query->fetch()) {
            $results[] = $result;
        }

        return $results;
    }
}