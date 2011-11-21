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
     * @param Pimple $container
     */
    public function __construct(Pimple $container) {
        $this->setContainer($container);
    }

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


        $query = $this->connection->prepare($sql);
        $query->execute($params);

        $query->setFetchMode(PDO::FETCH_ASSOC);

        $results = array();
        $entityMapper = $this->container['bip.entityMapper.bip'];

        while ($result = $query->fetch()) {
            $results[] = $entityMapper->mapEntity($result);
        }

        return $results;
    }
}