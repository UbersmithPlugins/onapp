<?php

namespace UbersmithPlugin\Onapp\Billing\Method\VDCS;

use UbersmithPlugin\Onapp\Billing\ResourceDictionary;
use function UbersmithSDK\Util\I18n;

class Details extends \UbersmithPlugin\Onapp\Billing\Method\Details
{
	protected $resourceTitle = 'vCloud Resource Pool';

	protected function usageCostsBreakdown()
	{
		$table = '<div class="report-box usage"><div class="title"><b>' . I18n('Breakdown') . '</b></div>';
		$table .= '<table class="report-box table-content usage-breakdown" style="width:100%;" width="100%" cellpadding="4" border="1">';

		foreach ($this->resourceBreakdown as $resourceGroupName => $resources) {
			foreach ($resources as $resourceName => $resource) {
				$label = ResourceDictionary::label($resourceGroupName);

				if (isset($resource['label'])) {
					$headerLabel = $label . ' (ID: ' . $resourceName . ') ' . $resource['label'];
				} else {
					$headerLabel = $label;
				}

				$table .= '<thead><tr class="header"><th bgcolor="#e0e2e6" colspan="2">' . $headerLabel . '</th></tr></thead>';

				krsort($resource);
				if ($resource['cost'] == 0) {
					$table .= '<tbody><tr><td class="label" colspan="2" style="text-align:center">' . I18n('No Usage Billed') . '</td></tr></tbody>';
					continue;
				}

				$table .= '<tbody>';
				foreach ($resource as $resourceKey => $resourceValue) {
					$label = ResourceDictionary::label($resourceKey);
					$unit = ResourceDictionary::unit($resourceKey);

					if ($resourceKey === 'label') {
						continue;
					}

					$table .= '<tr>';

					if ($resourceKey === 'cost') {
						$table .= '<td><b>' . $label . '</b></td>';
						$table .= '<td><b>' . currency($resourceValue) . '</b></td>';
					} else {
						$avgPerHour = number_format($resourceValue / $this->resourceInfo['number_of_hours'], 5) . $unit;

						$table .= '<td>' . $label . ' (' . I18n('Per Hour Average') . ')</td>';
						$table .= '<td>' . $avgPerHour . '</td>';
					}

					$table .= '</tr>';
				}

				$table .= '</tbody>';
			}
		}

		$table .= '</table></div>';

		return $table;
	}
}
