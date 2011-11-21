<?php
namespace Bip\EntityMapper;

use \Pimple;

class BipEntityMapper {

    /**
     * @var Pimple $container
     */
    protected $container;

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
     * Map an array of data to a Bip entity
     * 
     * @param array $data
     * @return Bip
     */
    public function mapEntity(array $data) {
        $bip = $this->container['bip.entity.bip'];
        foreach ($data as $key => $value) {
            $setter = "set" . $key;
            $bip->$setter($value);
        }

        return $bip;
    }
}