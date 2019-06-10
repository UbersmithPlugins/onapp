<?php

namespace UbersmithPlugin\Onapp\Billing;

use UbersmithPlugin\Onapp\Billing\Method;
use DateTime;

class Client
{
	private $onapp;

	/**
	 * Constructor to initiate the OnappBilling Client.
	 *
	 * @param string $hostname client's OnApp instance URL
	 * @param string $username username for the instance
	 * @param string $password password for the instance
	 * @param int $timeout curl request timeout limit in seconds
	 */
	public function __construct($hostname, $username, $password, $timeout = 300)
	{
		$this->onapp = new OnAppFactoryWrapper($hostname, $username, $password, $timeout);
	}

	/**
	 * Fetch usage data depending on the billMethod type.
	 *
	 * @param string $billMethod billing method set from entity level config item.
	 * @param string $billMethodId id used for the billing method set.
	 * @param DateTime $start_time Start date as a DateTime object
	 * @param DateTime $end_time End date as a DateTime object
	 * @return mixed[] Returns array of 'total_cost' and 'details'.
	 */
	public function getUsage($billMethod, $billMethodId, DateTime $start_time, DateTime $end_time)
	{
		$billingMethods = [
			'bill_vdcs'      => Method\VDCS\Usage::class,
			'bill_user'      => Method\User\Usage::class,
			'bill_user_full' => Method\UserFull\Usage::class,
			'bill_cdn'       => Method\CDN\Usage::class
		];

		$urlArgs = [
			'period' => [
				'startdate' => $start_time->format('Y-m-d\TH:i:s.000\Z'),
				'enddate'   => $end_time->format('Y-m-d\TH:i:s.000\Z'),
			]
		];

		return (new $billingMethods[$billMethod]($this->onapp, $billMethodId, $urlArgs))->fetch();
	}
}
