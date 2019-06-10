<?php

namespace UbersmithPlugin\Onapp\Billing\Method;

use UbersmithPlugin\Onapp\Billing\ResourceDictionary;
use function UbersmithSDK\Util\I18n;

/**
 * Class Details
 *
 * Abstract class to populate markup from OnApp usage data. Utilized by \Usage.
 *
 * @package UbersmithPlugin\Onapp\Billing\Method
 */
abstract class Details
{
	/**
	 * @var string Display label for resource.
	 */
	protected $resourceTitle;
	/**
	 * @var array General info for resource.
	 */
	protected $resourceInfo;
	/**
	 * @var array Simple cost breakdown for resource usage data.
	 */
	protected $resourceCosts;
	/**
	 * @var mixed Contains nested arrays for resource usage breakdown data.
	 */
	protected $resourceBreakdown;
	/**
	 * @var int Total costs for resource usage.
	 */
	protected $resourceTotal;

	/**
	 * Details constructor.
	 *
	 * @param $usageTotalCost
	 * @param $usageInfo
	 * @param $usageOverviewCosts
	 * @param $usageBreakdownCosts
	 */
	public function __construct($usageTotalCost, $usageInfo, $usageOverviewCosts, $usageBreakdownCosts)
	{
		$this->resourceTotal = $usageTotalCost;
		$this->resourceInfo = $usageInfo;
		$this->resourceCosts = $usageOverviewCosts;
		$this->resourceBreakdown = $usageBreakdownCosts;
	}

	/**
	 * @return int
	 */
	public function getUsageTotalCost()
	{
		return $this->resourceTotal;
	}

	/**
	 * @param int $value
	 */
	public function setUsageTotalCost($value)
	{
		$this->resourceTotal += $value;
	}

	/**
	 * @return mixed
	 */
	public function getResourceCosts()
	{
		return $this->resourceCosts;
	}

	/**
	 * @param string $key
	 * @param int $value
	 */
	public function setResourceCosts($key, $value)
	{
		if (!isset($this->resourceCosts[$key])) {
			$this->resourceCosts[$key] = 0;
		}

		$this->resourceCosts[$key] += $value;
		$this->resourceTotal += $value;
	}

	/**
	 * @return string Returns combined html markup for display.
	 */
	public function __toString()
	{
		return $this->generalInfo() . $this->usageCostsOverview() . $this->usageCostsBreakdown() . $this->usageCostsTotal();
	}

	/**
	 * @return string
	 */
	protected function generalInfo()
	{
		$table = '<div class="report-box usage"><div class="title"><b>' . I18n('Resource') . ': ' . $this->resourceTitle . '</b></div>';
		$table .= '<table class="table-content usage-breakdown" style="width:100%;" width="100%" cellpadding="4" border="1">';

		foreach ($this->resourceInfo as $name => $value) {
			$label = ResourceDictionary::label($name);
			$unit = ResourceDictionary::unit($name);
			$value .= $unit;
			$table .= '<tr><td class="label" colspan="2">' . $label . '</td><td>' . $value . '</td></tr>';
		}
		$table .= '</table></div>';

		return $table;
	}

	/**
	 * @return string
	 */
	protected function usageCostsOverview()
	{
		$table = '<div class="report-box usage"><div class="title"><b>' . I18n('Overview') . '</b></div>';
		$table .= '<table class="table-content usage-breakdown" style="width:100%;" width="100%" cellpadding="4" border="1">';

		foreach ($this->resourceCosts as $name => $value) {
			$label = ResourceDictionary::label($name);
			$table .= '<tr><td class="label">' . $label . '</td><td>' . currency($value ?? 0) . '</td></tr>';
		}

		$table .= '</table></div>';

		return $table;
	}

	/**
	 * @return mixed
	 */
	abstract protected function usageCostsBreakdown();

	/**
	 * @return string
	 */
	protected function usageCostsTotal()
	{
		$table = '<div class="report-box usage"><div class="title"><b>' . I18n('Total') . '</b></div>';
		$table .= '<table class="table-content usage-breakdown" style="width:100%;" width="100%" cellpadding="4" border="1">';
		$table .= '<tr><td class="label" colspan="2"><b>' . I18n('Cost') . '</b></td><td><b>' . currency($this->resourceTotal) . '</b></td></tr>';
		$table .= '</table></div>';

		return $table;
	}
}
