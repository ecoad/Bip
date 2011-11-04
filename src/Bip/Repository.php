<?php
namespace Bip;

use \PDO;
use \PDOException;
use \Pimple;

class Repository {
    /**
     * @var ContainerInterface $container
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

    public function connect() {
        $this->connection = new PDO($this->container['db.driver'] . ':' . $this->container['db.path']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

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


    public function fetchAllByGroup($group) {
        $sql = 'SELECT * FROM Bip WHERE "Group" = ?';
        $results = $this->fetchArrayByQuery($sql, array($group));
        return $results;
    }

    public function fetchArrayByQuery($sql, array $params) {
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

    public function fetchOneByName($name) {
        exit;
    }

    public function getByUsername($username) {
        $username = strtolower($username);

        if (!$this->connection) {
            $this->connect();
        }

        $result = $this->connection->query('SELECT * FROM Location');
        foreach ($result as $bip) {
            var_dump($bip);
        }
        exit;
        
        $this->retrieve(array("username" => $username));

        $results = $this->container['db']->fetchArray(
            "SELECT * FROM {$this->repositoryName} WHERE username = {$username} LIMIT 1");
        
        var_dump($results); exit;
    }
}