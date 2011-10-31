<?php
namespace Bip;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use \Pimple;

/**
 * The Bip Service
 *
 * @author Elliot Coad
 */
class Service {
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * Set the DI service container
     *
     * @param Pimple $container
     */
    public function setContainer(Pimple $container) {
        $this->container = $container;
    }

    /**
     * Persist the location of the given user
     * 
     * @param Request $request
     */
    public function updatePosition(Request $request) {
        $coords = $request->get('coords');

        $this->container['db']->update('Location', 
            array(
                'Lat' => $coords[0], 
                'Lon' => $coords[1], 
                'Accuracy' => $request->get('accuracy'), 
                'LastUpdate' => time()
            ), 
            array('Person' => $request->get('person'))
        );
    }

    /**
     * Get the Bips
     * 
     * @return array Bips
     */
    public function getBips() {
        $results = $this->container['db']->fetchAll('SELECT * FROM Location');

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
                return "$timeSinceMins mins ago ";
                break;
            default:
                $timeSinceHours = round(($timeSince / 60) / 60);
                return "$timeSinceHours hours ago";
        }
    }
}