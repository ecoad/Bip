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
        $bip = $this->mapDataToBip($data);
        $bip->setLastUpdate(time());

        $this->container['bip.service']->updateBip($data);
    }

    /**
     * Get the Bips
     * 
     * @return array Bips
     */
    public function getBipsByGroup($group) {
        $results = $this->container['bip.repository.bip']->fetchAllByGroup($group);

        foreach ($results as &$result) {
            $result['TimeSince'] = $this->getFormattedTimeSince($result);
        }
        return $results;
    }

    protected function getTimeSince(array $bip) {
        return time() - $bip['LastUpdate'];
    }

    protected function getFormattedTimeSince(array $bip) {
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

        var_dump($bip->getEmailAddress()); exit;
        /*
        $bip->setId($data['Id']);
        $bip->setName($data['Name']);
        $bip->setEmailAddress($data['EmailAddress']);
        $bip->setLat($data['Lat']);
        $bip->setLon($data['Lon']);
        $bip->setAccuracy($data['Accuracy']);
        */
        return $bip;
    }
}