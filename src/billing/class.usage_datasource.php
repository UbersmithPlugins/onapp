<?php

namespace UbersmithPlugin\Onapp\Billing;

use UbersmithSDK\Usage;
use UbersmithSDK\Parameter;

use DateTime;

/**
 * @UsageDataSource onapp_billing_usage_datasource
 * @label OnApp Billing Datasource
 * @Module onapp_billing_usage_datasource
 */
class UsageDataSource implements Usage\Data\Source
{
	private $plugin;

	/**
	 * @inheritDoc
	 */
	public function fetch(Parameter\Source\Service $service, $resources, $start_ts, $end_ts)
	{
		$onappInstance = Instance::factory($this->plugin, $service);

		$start = (new DateTime())->setTimestamp($start_ts);
		$end = (new DateTime())->setTimestamp($end_ts);
		$usage = [];

		$resource_usage = $onappInstance->fetchAccountUsage($start, $end);

		foreach ($resources as $res) {
			$amount = $resource_usage[$res->get_identifier()] ?? 0;
			$usage[] = new Usage\Data($res, $amount, $resource_usage['details']);
		}

		return $usage;
	}

	/**
	 * @inheritDoc
	 */
	public function get_supported_resources()
	{
		$out = [];

		$onappInstance = Instance::factory($this->plugin);
		foreach ($onappInstance->resources() as $name => $resource) {
			// May throw an SDKException but let it get caught upstream
			$out[] = new Usage\MarkupResource($name, $resource['label']);
		}

		return $out;
	}

	/**
	 * @inheritDoc
	 */
	public function convert_amount($amount, Usage\Resource $resource, $to_unit)
	{
		return $amount;
	}

	/**
	 * @inheritDoc
	 */
	public function set_plugin(Parameter\Plugin $plugin)
	{
		$this->plugin = $plugin;
	}
}
