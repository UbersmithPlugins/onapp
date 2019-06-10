<?php

namespace UbersmithPlugin\Onapp\Billing\Method\UserFull;

class Details extends \UbersmithPlugin\Onapp\Billing\Method\Details
{
	protected $resourceTitle = 'User Billing (Full)';

	protected function usageCostsBreakdown()
	{
		return ''; // user_full uses User class' usage().
	}
}
