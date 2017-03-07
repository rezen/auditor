<?php

use Auditor\Activity;

class TestEventsAudited extends WP_UnitTestCase
{

	/**
	 * Make sure the extra tables are setup
	 */
	function setUp()
	{
		activate_auditor();
	}

	/**
	 * Test that the namespacing resolves correctly
	 */
	function testActivityNamespacing() 
	{
		$activity = new Activity('wp.user', 99, 'Created blog post');
		$this->assertEquals($activity->namespace, 'wp');

		$activity = new Activity('user', 99, 'Created blog post');
		$this->assertEquals($activity->namespace, null);
	}
}
