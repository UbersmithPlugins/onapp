<?php

namespace UbersmithPlugin\Onapp\Billing\Method\CDN;

class Usage extends \UbersmithPlugin\Onapp\Billing\Method\Usage
{
	public function fetch()
	{
		$cdn = $this->onapp->factory('CDNResource')->load($this->id);
		$hourlyStats = $this->onapp->factory('CDNResource_BillingStatistic')->getList($this->id, $this->urlArgs) ?? [];
		$totalCost = 0;
		$info = [
			'id'              => $this->id,
			'cdn_hostname'    => $cdn->cdn_hostname,
			'resource_type'   => $cdn->resource_type,
			'cdn_reference'   => $cdn->cdn_reference,
			'number_of_hours' => 0,
		];
		$overviewCosts = [
			'traffic' => 0,
		];
		$breakdownCosts = [];

		foreach ($hourlyStats as $hourly) {
			++$info['number_of_hours'];
			$totalCost += $hourly->cost;

			$overviewCosts['traffic'] += $hourly->cost;
			$breakdownCosts['traffic'] += $hourly->value;
		}

		return [
			'total_cost' => $totalCost,
			'details'    => new Details($totalCost, $info, $overviewCosts, $breakdownCosts)
		];
	}
}
