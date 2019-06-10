<?php

namespace UbersmithPlugin\Onapp\Billing\Method\User;

use UbersmithPlugin\Onapp\Billing\ResourceDictionary;
use function UbersmithSDK\Util\I18n;

class Details extends \UbersmithPlugin\Onapp\Billing\Method\Details
{
	protected $resourceTitle = 'User Billing (VM Resource Only)';

	protected function usageCostsBreakdown()
	{
		$table = '<div class="report-box usage"><div class="title"><b>' . I18n('Breakdown') . '</b></div>';
		$table .= '<table class="report-box table-content usage-breakdown" style="width:100%;" width="100%" cellpadding="4" border="1">';

		foreach ($this->resourceBreakdown as $resourceGroupName => $resourceGroup) {
			foreach ($resourceGroup as $groupComponentName => $groupComponent) {
				$headerLabel = ResourceDictionary::label($resourceGroupName);

				if (isset($groupComponent['label'])) {
					$headerLabel = $groupComponent['label'] . ' (ID:' . $groupComponentName . ')';
				}

				$table .= '<thead>
								<tr class="header">
									<th bgcolor="#e0e2e6">' . $headerLabel . '</th>
									<th bgcolor="#e0e2e6">' . I18n('Per Hour Average') . '</th>
									<th bgcolor="#e0e2e6">' . I18n('Cost') . '</th>
								</tr>
							</thead>
							<tbody>';

				if (isset($groupComponent['cost']) && $groupComponent['cost'] == 0) {
					$table .= '<tr><td class="label" colspan="3" style="text-align:center">' . I18n('No Usage Billed') . '</td></tr>';
					continue;
				}

				foreach ($groupComponent as $componentResourceName => $componentResource) {
					if (!is_array($componentResource)) {
						$label = ResourceDictionary::label($componentResourceName);

						if ($componentResourceName !== 'cost' && $componentResourceName !== 'label' && $componentResourceName !== 'id') {
							$table .= '<tr><td class="label">' . $label . '</td><td>' . $componentResource['value'] . '</td></tr>';
						}

						continue;
					}

					foreach ($componentResource as $resourceName => $resourceValue) {
						foreach ($resourceValue as $resourceValueName => $resourceValueInfo) {
							$label = ResourceDictionary::label($resourceValueName);
							$unit = ResourceDictionary::unit($resourceValueName);

							$table .= '<tr>';

							if (isset($resourceValueInfo['cost'])) {
								$avgPerHour = number_format($resourceValueInfo['value'] / $this->resourceInfo['number_of_hours'], 5) . $unit;

								if ($resourceValueInfo['cost'] > 0) {
									$table .= '<td class="label" style="padding-left: 20px;">' . $label . '</td><td><b>' . $avgPerHour . '</b></td><td><b>' . currency($resourceValueInfo['cost']) . '</b></td>';
								} else {
									$table .= '<td class="label" style="padding-left: 20px;">' . $label . '</td><td>' . $avgPerHour . '</td><td>' . currency($resourceValueInfo['cost']) . '</td>';
								}
							} else {
								$formatted = ResourceDictionary::label($componentResourceName) . ' (' . $resourceValueInfo . ')';
								$table .= '<td style="padding-left: 10px"><b>' . $formatted . '</b></td>';
								$table .= '<td style="padding-left: 10px"></td>';
								$table .= '<td style="padding-left: 10px"></td>';
							}

							$table .= '</tr>';
						}
					}
				}

				$table .= '</tbody>';
			}

			$table .= '</table></div>';
		}

		return $table;
	}
}
