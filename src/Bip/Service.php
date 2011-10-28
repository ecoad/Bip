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
                'Lat' => $coords['lat'], 
                'Lon' => $coords['lon'], 
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
        return $results;
    }
}