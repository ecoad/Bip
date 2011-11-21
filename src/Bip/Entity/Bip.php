<?php
namespace Bip\Entity;

use \stdClass;

class Bip {
	/**
	 * @var integer $id
	 */
	protected $id;

	/**
	 * @var string $name
	 */
	protected $name;

	/**
	 * @var string $emailAddress
	 */
	protected $emailAddress;

	/**
	 * @var float $lat
	 */
	protected $lat;

	/**
	 * @var float $lon
	 */
	protected $lon;

	/**
	 * @var integer $accuracy
	 */
	protected $accuracy;

	/**
	 * @var integer $lastUpdate
	 */
	protected $lastUpdate;

	/**
	 * @var integer $group
	 */
	protected $group;

	/**
	 * Description of time since last update
	 */
	protected $timeSince;

	public function __call($callName, array $params) {
		switch (true) {
			case substr($callName, 0, 3) === "get":
				$property = lcfirst(substr($callName, 3));
				return $this->$property;
				break;
			case substr($callName, 0, 3) === "set":
				$property = lcfirst(substr($callName, 3));
				$this->$property = $params[0];
				break;
			default:
				trigger_error(sprintf('Call to undefined function: %s::%s().', get_class($this), $callName), 
					E_USER_ERROR);
				break;
		}
	}

	public function getTimeSince() {
		//TODO
		return '99 hours';
	}

	/**
	 * Return a plain object to allow serialisation, such as JSON
	 * @return stdClass
	 */
	public function getPlainObject() {
		$object = new stdClass();
		foreach ($this as $property => $value) {
			$getter = 'get' . ucfirst($property);
			$object->$property = $this->$getter();
		}

		return $object;
	}
}