<?php

use Auditor\Activity;
use Auditor\ActivityRecorder;

class TestAuditorIntegration extends WP_UnitTestCase
{
	/**
	 * Make sure the extra tables are setup
	 */
	function setUp()
	{
		global $wpdb;
		activate_auditor();
		$this->recorder = new ActivityRecorder($wpdb);
	}

	/**
	 * Capture the user login event data
	 */
	public function testAuditLogin() 
	{
		$id    = 99999999999999;
		$ip    = '127.0.0.1';
		$login = 'bobby_login';

		$_SERVER['HTTP_USER_AGENT']      = 'Bond, James Bond';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = $ip;
		
		do_action('wp_login', $login, (object) ['ID' => $id]);

		$events = $this->recorder->find(new Activity('wp.user', $id));

		$this->assertTrue(count($events) > 0);
		$event = array_pop($events);
		$this->assertEquals($event->entity_id, $id);
		$this->assertEquals($event->meta['ip'], $ip);
	}
}
