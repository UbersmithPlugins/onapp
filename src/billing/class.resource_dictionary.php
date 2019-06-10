<?php

namespace UbersmithPlugin\Onapp\Billing;

use function UbersmithSDK\Util\I18n;

/**
 * Class to keep records of display labels for OnApp data.
 */
class ResourceDictionary
{
	private static function find($resourceName)
	{
		$resources = [
			'billing_plan_monthly_fee' => ['label' => I18n('Billing Plan Monthly Fee')],
			'resource_elements'        => ['label' => I18n('Pool Resources')],
			'network_interfaces'       => ['label' => I18n('Network Usage')],
			'data_stores'              => ['label' => I18n('Storage Policy Usage')],
			'disks'                    => ['label' => I18n('Disk Usage')],
			'virtual_machines'         => ['label' => I18n('Resource Usage')],
			'vm_resources'             => ['label' => I18n('VM Resource')],
			'usage'                    => ['label' => I18n('VM Usage')],
			'id'                       => ['label' => I18n('ID')],
			'label'                    => ['label' => I18n('Label')],
			'identifier'               => ['label' => I18n('Identifier')],
			'allocation_model'         => ['label' => I18n('Allocation Model')],
			'number_of_hours'          => ['label' => I18n('Number of Hours'), 'unit' => 'Hours'],
			'billing_plan_id'          => ['label' => I18n('Billing Plan ID')],
			'bucket_id'                => ['label' => I18n('Bucket ID')],
			'data_read'                => ['label' => I18n('Data Read'), 'unit' => 'GB'],
			'data_written'             => ['label' => I18n('Data Written'), 'unit' => 'GB'],
			'input_requests'           => ['label' => I18n('Input Requests')],
			'output_requests'          => ['label' => I18n('Output Requests')],
			'disk_size'                => ['label' => I18n('Disk Size'), 'unit' => 'GB'],
			'data_sent'                => ['label' => I18n('Data Sent'), 'unit' => 'Mbps'],
			'data_received'            => ['label' => I18n('Data Received'), 'unit' => 'Mbps'],
			'ip_addresses'             => ['label' => I18n('IP Addresses')],
			'port_speed'               => ['label' => I18n('Port Speed'), 'unit' => 'Mbps'],
			'cpu_time'                 => ['label' => I18n('CPU Time')],
			'cpus'                     => ['label' => I18n('CPUs'), 'unit' => 'CPUs'],
			'cpu_shares'               => ['label' => I18n('CPU Shares'), 'unit' => '%'],
			'cpu_units'                => ['label' => I18n('CPU Units'), 'unit' => 'Per Core'],
			'memory'                   => ['label' => I18n('Memory'), 'unit' => 'MB'],
			'template_usage'           => ['label' => I18n('Template Usage')],
			'email'                    => ['label' => I18n('Email')],
			'cost'                     => ['label' => I18n('Cost')],
			'compute_zone_id'          => ['label' => I18n('Compute Zone ID')],
			'memory_used'              => ['label' => I18n('Memory Used'), 'unit' => 'GB'],
			'count'                    => ['label' => I18n('Count')],
			'vcpu_speed'               => ['label' => I18n('vCPU Speed'), 'unit' => 'MHz'],
			'cpu_guaranteed'           => ['label' => I18n('CPU Guaranteed'), 'unit' => '%'],
			'cpu_allocation'           => ['label' => I18n('CPU Allocation'), 'unit' => 'GHz'],
			'cpu_used'                 => ['label' => I18n('CPU Used'), 'unit' => 'GHz'],
			'memory_guaranteed'        => ['label' => I18n('Memory Guaranteed'), 'unit' => '%'],
			'memory_allocation'        => ['label' => I18n('Memory Allocation'), 'unit' => 'GB'],
			'deployed_edge_gateways'   => ['label' => I18n('Deployed Edge Gateways')],
			'deployed_org_networks'    => ['label' => I18n('Deployed Org Networks')],
			'fast_provisioning_set'    => ['label' => I18n('Fast Provisioning Set')],
			'thin_provisioning_set'    => ['label' => I18n('Thin Provisioning Set')],
			'vs_count'                 => ['label' => I18n('VS Count')],
			'vs_limit'                 => ['label' => I18n('VS Limit')],
			'data_store_zone_id'       => ['label' => I18n('Data Store Zone ID')],
			'data_store_id'            => ['label' => I18n('Data Store ID')],
			'disk_size_used'           => ['label' => I18n('Disk Size Used'), 'unit' => 'GB'],
			'network_zone_id'          => ['label' => I18n('Network Zone ID')],
			'network_interface_id'     => ['label' => I18n('Network Interface ID')],
			'status'                   => ['label' => I18n('Status')],
			'first_name'               => ['label' => I18n('First Name')],
			'last_name'                => ['label' => I18n('Last Name')],
			// bill_cdn
			'traffic'                  => ['label' => I18n('Traffic'), 'unit' => 'B'],
			'cdn_reference'            => ['label' => I18n('CDN Reference')],
			'resource_type'            => ['label' => I18n('Resource Type')],
			'cdn_hostname'             => ['label' => I18n('CDN Hostname')],
			// bill_user_full
			'billing_plan_cost'        => ['label' => I18n('Billing Plan Monthly Fee')],
			'vm_cost'                  => ['label' => I18n('Virtual Servers Cost')],
			'backup_cost'              => ['label' => I18n('Backup Cost')],
			'template_cost'            => ['label' => I18n('Templates Cost')],
			'template_iso_cost'        => ['label' => I18n('Templates, ISOs, Backups Storage Cost')],
			'storage_disk_size_cost'   => ['label' => I18n('Storage Disk Size Cost')],
			'backup_count_cost'        => ['label' => I18n('Backup Count Cost')],
			'backup_disk_size_cost'    => ['label' => I18n('Backup Disk Size Cost')],
			'template_count_cost'      => ['label' => I18n('Template Count Cost')],
			'template_disk_size_cost'  => ['label' => I18n('Template Disk Size Cost')],
			'autoscale_cost'           => ['label' => I18n('Autoscaling Monitor Fee')],
			'acceleration_cost'        => ['label' => I18n('Acceleration Cost')],
			'ova_count_cost'           => ['label' => I18n('OVA Count Cost')],
			'ova_size_cost'            => ['label' => I18n('OVA Size Cost')],
			'edge_group_cost'          => ['label' => I18n('CDN Edge Group Cost')],
			'user_resources_cost'      => ['label' => I18n('User Resources Cost')],
			'recovery_point_cost'      => ['label' => I18n('Recovery Point Cost')],
		];

		return $resources[$resourceName] ?? '';
	}

	/**
	 * Returns a display name for the resource when available.
	 *
	 * @param string $resourceName name from self::resourceMap
	 * @return string matching 'label' value if available. Otherwise, returns $resourceName as is.
	 */
	public static function label($resourceName)
	{
		return self::find($resourceName)['label'] ?? $resourceName;
	}

	/**
	 * Returns a unit value for the resource when available.
	 *
	 * @param string $resourceName name from self::resourceMap
	 * @return string matching 'unit' if available. Otherwise, returns an empty string.
	 */
	public static function unit($resourceName)
	{
		$unit = self::find($resourceName)['unit'] ?? '';
		if (!empty($unit)) {
			$unit = ' ' . $unit;
		}

		return $unit;
	}
}
