<?php

namespace UbersmithPlugin\Onapp\Billing\Method\VDCS;

class Usage extends \UbersmithPlugin\Onapp\Billing\Method\Usage
{
	private $overviewCosts = [
		'data_stores'              => 0,
		'network_interfaces'       => 0,
		'resource_elements'        => 0,
		'billing_plan_monthly_fee' => 0,
	];

	private $breakdownCosts = [
		'data_stores'        => [],
		'network_interfaces' => [],
		'resource_elements'  => [],
	];

	public function fetch()
	{
		$VDCSResourcePool = $this->onapp->factory('VDCS')->load($this->id);
		$info = [
			'id'               => $VDCSResourcePool->id,
			'label'            => $VDCSResourcePool->label,
			'identifier'       => $VDCSResourcePool->identifier,
			'allocation_model' => $VDCSResourcePool->allocation_model,
			'number_of_hours'  => 0,
		];

		$hourlyStats = $this->onapp->factory('VDCS_Statistics')->getList($this->id, $this->urlArgs) ?? [];
		if (!empty($hourlyStats)) {
			$userGroupId = $hourlyStats[0]->company_id;
			$userGroup = $this->onapp->factory('UserGroup')->load($userGroupId);
			$companyBillingPlan = $this->onapp->factory('BillingUser')->load($userGroup->bucket_id);
			$this->overviewCosts['billing_plan_monthly_fee'] = $companyBillingPlan->monthly_price;
		}

		foreach ($hourlyStats as $hourly) {
			++$info['number_of_hours'];

			$this->processDataStores($hourly->data_stores->data);
			$this->processNetworkInterfaces($hourly->network_interfaces->data);
			$this->processResourceElements($hourly->resource_elements->data);
		}

		$totalCost = 0;
		foreach ($this->overviewCosts as $cost) {
			$totalCost += $cost;
		}

		return [
			'total_cost' => $totalCost,
			'details'    => new Details($totalCost, $info, $this->overviewCosts, $this->breakdownCosts)
		];
	}

	private function processDataStores($data)
	{
		foreach ($data as $hourly_data) {
			$id = $hourly_data->data_store_id;

			if (!isset($this->breakdownCosts['data_stores'][$id])) {
				$this->breakdownCosts['data_stores'][$id] = [
					'cost'           => 0,
					'label'          => $hourly_data->label ?? '',
					'disk_size_used' => 0,
				];
			}

			$this->breakdownCosts['data_stores'][$id]['disk_size_used'] += $hourly_data->disk_size_used ?? 0;
			$this->breakdownCosts['data_stores'][$id]['cost'] += $hourly_data->cost ?? 0;

			// Total cost of all data stores
			$this->overviewCosts['data_stores'] += $hourly_data->cost ?? 0;
		}
	}

	private function processNetworkInterfaces($data)
	{
		foreach ($data as $hourly_data) {
			$id = $hourly_data->network_interface_id;

			if (!isset($this->breakdownCosts['network_interfaces'][$id])) {
				$this->breakdownCosts['network_interfaces'][$id] = [
					'cost'          => 0,
					'label'         => $hourly_data->label ?? '',
					'data_sent'     => 0,
					'data_received' => 0,
				];
			}

			$this->breakdownCosts['network_interfaces'][$id]['data_sent'] += $hourly_data->data_sent ?? 0;
			$this->breakdownCosts['network_interfaces'][$id]['data_received'] += $hourly_data->data_received ?? 0;
			$this->breakdownCosts['network_interfaces'][$id]['cost'] += $hourly_data->cost ?? 0;

			// Total cost of all network interfaces
			$this->overviewCosts['network_interfaces'] += $hourly_data->cost ?? 0;
		}
	}

	private function processResourceElements($data)
	{
		$id = $data->compute_zone_id;

		if (!isset($this->breakdownCosts['resource_elements'][$id])) {
			$this->breakdownCosts['resource_elements'][$id] = [
				'cost'        => 0,
				'label'       => $data->label ?? '',
				'memory_used' => 0,
				'cpu_used'    => 0,
			];
		}

		$this->breakdownCosts['resource_elements'][$id]['memory_used'] += $data->memory_used ?? 0;
		$this->breakdownCosts['resource_elements'][$id]['cpu_used'] += $data->cpu_used ?? 0;
		$this->breakdownCosts['resource_elements'][$id]['cost'] += $data->cost ?? 0;

		// Total for resource Elements
		$this->overviewCosts['resource_elements'] += $data->cost ?? 0;
	}
}
