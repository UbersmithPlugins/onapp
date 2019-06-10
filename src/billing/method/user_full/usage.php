<?php

namespace UbersmithPlugin\Onapp\Billing\Method\UserFull;

class Usage extends \UbersmithPlugin\Onapp\Billing\Method\Usage
{
	public function fetch()
	{
		$user = (new \UbersmithPlugin\Onapp\Billing\Method\User\Usage($this->onapp, $this->id, $this->urlArgs))->fetch();
		$userStat = $this->onapp->factory('User_Statistics')->getList($this->id, $this->urlArgs);
		$fields = [
			'template_cost'           => 0,
			'template_iso_cost'       => 0,
			'storage_disk_size_cost'  => 0,
			'backup_cost'             => 0,
			'backup_count_cost'       => 0,
			'backup_disk_size_cost'   => 0,
			'template_count_cost'     => 0,
			'template_disk_size_cost' => 0,
			'recovery_point_cost'     => 0,
			'autoscale_cost'          => 0,
			'acceleration_cost'       => 0,
			'ova_count_cost'          => 0,
			'ova_size_cost'           => 0,
			'edge_group_cost'         => 0,
			'user_resources_cost'     => 0,
		];

		$details = $user['details'];
		foreach ($fields as $key => $value) {
			$details->setResourceCosts($key, $userStat[0]->$key);
		}

		return $user;
	}
}
