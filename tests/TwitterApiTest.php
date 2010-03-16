<?php

class TwitterApiTest extends SapphireTest
{
	function testSearch()
	{
		$client = new TwitterClient();
		$results = $client->search(array('q' => 'from:nyeholt'));

		print_r($results);

		$this->assertTrue(is_object($results));
		$this->assertTrue(isset($results->results));
		$this->assertTrue(count($results->results) > 0);
	}

	function testShow()
	{
		$client = new TwitterClient();
		$results = $client->call('showStatus', array('id' => '10557474139'));

		$this->assertTrue(is_object($results));
		print_r($results);
	}
}
?>