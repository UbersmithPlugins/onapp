<?php

namespace UbersmithPlugin\Onapp\Billing\Method;

use UbersmithPlugin\Onapp\Billing\OnAppFactoryWrapper;

/**
 * Class Usage
 *
 * Abstract class for OnApp usage data.
 *
 * @package UbersmithPlugin\Onapp\Billing\Method
 */
abstract class Usage
{
	/**
	 * @var OnAppFactoryWrapper Onapp wrapper factory instance.
	 */
	protected $onapp;
	/**
	 * @var string id used to fetch usage data for.
	 */
	protected $id;
	/**
	 * @var array an array of options for calling hosted OnApp instances.
	 */
	protected $urlArgs;

	/**
	 * Usage constructor.
	 * @param OnAppFactoryWrapper $onapp
	 * @param $id
	 * @param $urlArgs
	 */
	public function __construct(OnAppFactoryWrapper $onapp, $id, $urlArgs)
	{
		$this->onapp = $onapp;
		$this->id = $id;
		$this->urlArgs = $urlArgs;
	}

	/**
	 * @return array an array of total_cost and details object.
	 *
	 * [
	 *    'total_cost' => 2000,
	 *    'details' => \Details
	 * ]
	 */
	abstract public function fetch();
}
