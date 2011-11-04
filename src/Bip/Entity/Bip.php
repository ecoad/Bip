<?php
namespace Bip\Entity;

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
		}
	}
}