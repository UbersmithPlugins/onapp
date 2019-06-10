<?php

namespace UbersmithPlugin\Onapp\Billing\Method\CDN;

use UbersmithPlugin\Onapp\Billing\ResourceDictionary;
use function UbersmithSDK\Util\I18n;

class Details extends \UbersmithPlugin\Onapp\Billing\Method\Details
{
	protected $resourceTitle = 'CDN Resource';

	public function usageCostsBreakdown()
	{
		$table = '<div class="report-box usage"><div class="title"><b>' . I18n('Breakdown') . '</b></div>';
		$table .= '<table class="report-box table-content usage-breakdown" style="width:100%;" width="100%" cellpadding="4" border="1">';
		$table .= '<thead></thead><tbody>';

		foreach ($this->resourceBreakdown as $name => $value) {
			$label = ResourceDictionary::label($name);
			$unit = ResourceDictionary::unit($name);

			$table .= '<tr>
							<td class="label" > ' . $label . '</td>
							<td> ' . $value . $unit . '</td>
						</tr>';
		}
		$table .= '</tbody></table></div>';

		return $table;
	}
}
