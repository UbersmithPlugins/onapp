<?php

/**
 * Overridden class from onapp/onapp-php-wrapper-external composer package.
 *
 * Goal
 * - To pass $url_args to OnApp::getList
 */
class OnApp_User_BillingStatistics_Ubersmith extends OnApp_User_BillingStatistics
{
	function getList($user_id = null, $url_args = null)
	{
		if ($user_id === null && $this->_user_id !== null) {
			$user_id = $this->_user_id;
		}

		if ($user_id !== null) {
			$this->_user_id = $user_id;
			return OnApp::getList($user_id, $url_args);
		}

		$this->logger->error(
			'getList: argument _user_id not set.',
			__FILE__,
			__LINE__
		);
	}
}
