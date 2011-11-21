<?php
namespace Bip;

use Symfony\Component\HttpFoundation\Request;
use \Pimple;

/**
 * The Bip Service
 *
 * @author Elliot Coad
 */
class Service {
    /**
     * @var Pimple $container
     */
    protected $container;

    /**
     * Set the DI service container
     *
     * @param Pimple $container
     * @return Service;
     */
    public function setContainer(Pimple $container) {
        $this->container = $container;
        return $this;
    }

    /**
     * Persist the location of the given user
     * 
     * @param array $data
     */
    public function setPosition(array $data) {
        $bip = $this->container['bip.entityMapper.bip']->mapEntity($data);
        $bip->setLastUpdate(time());

        $this->container['bip.repository.bip']->persist($bip);
        return $this;
    }

    /**
     * Get the Bips
     * 
     * @return array Bips
     * @return Service;
     */
    public function getBipsByGroup($group) {
        $results = $this->container['bip.repository.bip']->fetchAllByGroup($group);
        return $results;
    }

    protected function getTimeSince(Bip $bip) {
        return time() - $bip->getLastUpdate();
    }

    protected function getFormattedTimeSince(Bip $bip) {
        $timeSince = $this->getTimeSince($bip);
        switch (true) {
            case $timeSince < 60:
                return "$timeSince seconds ago";
                break;
            case $timeSince < (60 * 60):
                $timeSinceMins = round($timeSince / 60);
                return "$timeSinceMins mins ago";
                break;
            default:
                $timeSinceHours = round(($timeSince / 60) / 60);
                return "$timeSinceHours hours ago";
        }
    }

    /**
     * Return Bips as plain objects for serialisation
     * 
     * @param array $bips
     * @return array Bips as plain objects
     */
    public function getBipsAsPlainObjects(array $bips) {
        $plainObjects = array();

        foreach ($bips as $bip) {
            $plainObjects[] = $bip->getPlainObject();
        }
        return $plainObjects;
    }

    /**
     * Map an array of key value pairs to Bip
     * 
     * @param $data
     * @return Bip
     */
    protected function mapDataToBip(array $data) {
        $bip = $this->container['bip.entity.bip'];
        foreach ($data as $fieldName => $value) {
            $john = 'setId';
            $method = 'set' . $fieldName;
            $bip->$method($value);
        }

        return $bip;
    }
}