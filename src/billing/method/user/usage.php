<?php

namespace UbersmithPlugin\Onapp\Billing\Method\User;

class Usage extends \UbersmithPlugin\Onapp\Billing\Method\Usage
{
	public function fetch()
	{
		$user = $this->onapp->factory('User')->load($this->id);
		$hourlyStats = $this->onapp->factory('User_BillingStatistics_Ubersmith')->getList($this->id, $this->urlArgs) ?? [];
		$billingPlanMonthlyFee = 0;
		if (!empty($hourlyStats)) {
			$userBillingPlan = $this->onapp->factory('BillingUser')->load($user->bucket_id);
			$billingPlanMonthlyFee = $userBillingPlan->monthly_price;
		}

		$totalCost = 0;
		$info = [
			'id'              => $this->id,
			'email'           => $user->email,
			'identifier'      => $user->identifier,
			'status'          => $user->status,
			'first_name'      => $user->first_name,
			'last_name'       => $user->last_name,
			'billing_plan_id' => $user->billing_plan_id,
			'bucket_id'       => $user->bucket_id,
			'number_of_hours' => 0,
		];
		$overviewCosts = [
			'vm_resources'             => 0,
			'usage'                    => 0,
			'billing_plan_monthly_fee' => $billingPlanMonthlyFee
		];
		$breakdownCosts = [
			'vm_resources' => []
		];

		foreach ($hourlyStats as $hourly) {
			++$info['number_of_hours'];

			$totalCost += $hourly->total_cost;
			$overviewCosts['usage'] += $hourly->usage_cost;
			$overviewCosts['vm_resources'] += $hourly->vm_resources_cost;

			if (!isset($breakdownCosts['vm_resources'][$hourly->virtual_machine_id])) {
				$breakdownCosts['vm_resources'][$hourly->virtual_machine_id] = [
					'label' => $hourly->billing_stats->virtual_machines[0]->label,
					'id'    => $hourly->virtual_machine_id,
					'cost'  => 0
				];
			}

			// $vmData is a placeholder array to collect each VM's resource info and costs.
			$vmData = $breakdownCosts['vm_resources'][$hourly->virtual_machine_id];
			foreach ($hourly->billing_stats as $statName => $statValue) {
				if (!isset($vmData[$statName])) {
					$vmData[$statName] = [];
				}

				foreach ($statValue as $resource) {
					if (!isset($vmData[$statName][$resource->id])) {
						$vmData[$statName][$resource->id] = [
							'label' => $resource->label
						];
					}

					foreach ($resource->costs as $resourceInfo) {
						if (!isset($vmData[$statName][$resource->id][$resourceInfo->resource_name])) {
							$vmData[$statName][$resource->id][$resourceInfo->resource_name] = [
								'cost'  => 0,
								'value' => 0
							];
						}

						$vmData[$statName][$resource->id][$resourceInfo->resource_name]['cost'] += $resourceInfo->cost;
						$vmData[$statName][$resource->id][$resourceInfo->resource_name]['value'] += $resourceInfo->value;
						$vmData['cost'] += $resourceInfo->cost;
					}
				}
			}

			// $vmData gets put back into $breakdownCosts array.
			$breakdownCosts['vm_resources'][$hourly->virtual_machine_id] = $vmData;
			unset($vmData);
		}

		return [
			'total_cost' => $totalCost,
			'details'    => new Details($totalCost, $info, $overviewCosts, $breakdownCosts)
		];
	}
}
