<?php

namespace UbersmithPlugin\Onapp\Billing;

use UbersmithSDK\Error;
use UbersmithSDK\Parameter;
use DateTime;
use function UbersmithSDK\Util\I18n;

define('ONAPP_USAGE_CACHE_TTL', 5184000); // $ttl = 60 days

class Instance
{
	private $client;

	protected $plugin;
	protected $service;

	/**
	 * Create a new instance of OnApp object
	 *
	 * @param Parameter\Plugin $plugin
	 * @param Parameter\Source $service
	 * @return instance
	 */
	public static function &factory(Parameter\Plugin $plugin, Parameter\Source $service = null)
	{
		$client = new Client($plugin->config->hostname, $plugin->config->username, $plugin->config->password, $plugin->config->timeout);
		$instance = new Instance($client, $plugin, $service);

		return $instance;
	}

	public function __construct(Client $client, Parameter\Plugin $plugin, Parameter\Source $service = null)
	{
		$this->client = $client;
		$this->plugin = $plugin;
		$this->service = $service;
	}

	/**
	 * Set the target service
	 *
	 * @param Parameter\Source $service
	 */
	public function setSource(Parameter\Source $service)
	{
		$this->service = $service;
	}

	/**
	 * Retrieve OnApp usage data and costs. Format is dependent on billing_method used.
	 *
	 * @param DateTime $start_time Start date as a DateTime object
	 * @param DateTime $end_time End date as a DateTime object
	 * @return mixed[] Returns array of resource costs.
	 */
	public function fetchAccountUsage(DateTime $start_time, DateTime $end_time)
	{
		$billMethod = $this->plugin->config->billing_method;
		$billMethodId = $this->plugin->config->billing_method_id;

		if (!$this->service->post_renew) {
			throw new Error\SDKException(I18n('OnApp service must be post renew'));
		}

		if (empty($billMethod) || empty($billMethodId)) {
			throw new Error\SDKException(I18n('OnApp billing method must be configured. Click "configure datasource" below'));
		}

		$beginning_of_next_month = new DateTime();
		$beginning_of_next_month->modify('first day of next month');

		if ($end_time > $beginning_of_next_month) {
			$end_time = $beginning_of_next_month;
		}

		$usage = $this->getCachedUsage($billMethod, $billMethodId, $start_time, $end_time);

		return [
			'onapp'   => $usage['total_cost'],
			'details' => $usage['details']
		];
	}

	/**
	 * Returns resources array with 'onapp' as key.
	 *
	 * OnApp plugin is retrofitted from old OnApp service module and offers only one resource type to ease transition.
	 *
	 * @return mixed[] Returns one onapp resource
	 */
	public function resources()
	{
		$resources = [
			'onapp' => [
				'label' => 'OnApp Billing',
			]
		];

		return $resources;
	}

	/**
	 * Retrieve OnApp usage costs for the accounts given a date range from plugin cache.
	 * Cached data TTL is based on ONAPP_USAGE_CACHE_TTL.
	 *
	 * @param string $billMethod billing method set from entity level config item.
	 * @param string $billMethodId id used for the billing method set.
	 * @param DateTime $start_time Start date as a DateTime object
	 * @param DateTime $end_time End date as a DateTime object
	 * @return mixed[] Returns array of resource usage data and costs
	 */
	protected function getCachedUsage($billMethod, $billMethodId, DateTime $start_time, DateTime $end_time)
	{
		$cache_key = $billMethod . '::' . $billMethodId . '::' . $start_time->format('Ymd') . $end_time->format('Ymd');
		$records = $this->plugin->cache->get($cache_key);

		// Pull usage if cache miss OR cache result is older than the date range end
		if (
			$records === null
			|| empty($records)
			|| (isset($records['__cachedate']) && strtotime($records['__cachedate']) < $end_time->getTimestamp())
		) {
			$records = $this->client->getUsage($billMethod, $billMethodId, $start_time, $end_time);
			// Use this to validate cache
			$records['__cachedate'] = date('Y-m-d');
			$this->plugin->cache->set($cache_key, $records, ONAPP_USAGE_CACHE_TTL);
		}

		return $records;
	}
}
