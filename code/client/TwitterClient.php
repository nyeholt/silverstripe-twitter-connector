<?php
/**

Copyright (c) 2009, SilverStripe Australia Limited - www.silverstripe.com.au
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the 
      documentation and/or other materials provided with the distribution.
    * Neither the name of SilverStripe nor the names of its contributors may be used to endorse or promote products derived from this software 
      without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
OF SUCH DAMAGE.
 
*/
 
/**
 * A web client of the APIs that have been made available for access via twitter's APIs
 * 
 * Currently, only the following methods have been implemented
 * 
 * search
 * 
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 *
 */
class TwitterClient
{
	
	/**
	 * The webapiclient to use for making the requests
	 * 
	 * @var WebApiClient
	 */
	protected $restApi;

	/**
	 * The webapiclient to use for searching
	 *
	 * @var WebApiClient
	 */
	protected $searchApi;
	
	public function __construct()
	{
		$this->restApi = new WebApiClient(null, self::$rest_methods);
		$this->restApi->setBaseUrl('http://api.twitter.com/1');

		$this->searchApi = new WebApiClient(null, self::$search_methods);
		$this->searchApi->setBaseUrl('http://search.twitter.com');
	}
	
	/**
	 * Just assume it's true for now... 
	 * @return unknown_type
	 */
	public function isConnected()
	{
		// TODO: Make this check the login functionality or something..!!
		return true;
	}
	
	/**
	 * Connect to the matrix server via the JS API
	 * 
	 * @param $details
	 */
	public function connect($details)
	{
		$this->restApi->setAuthInfo($details['username'], $details['password']);
		$this->searchApi->setAuthInfo($details['username'], $details['password']);
	}

	/**
	 * Doesn't do anything for now
	 */
	public function disconnect()
	{
	}

	/**
	 * Call a method on twitter
	 * 
	 * @param $method
	 * 				The method name
	 * @param $args
	 * 				The arguments to pass to the method in key => value form
	 * @return mixed
	 */
	public function call($method, $args)
	{
		return $this->restApi->callMethod($method, $args);
	}

	/**
	 * Execute a twitter search
	 *
	 */
	public function search($args)
	{
		return $this->searchApi->callMethod('search', $args);
	}
	
	private static $search_methods = array(
		'search' => array(
			'method' => 'GET',
			'url' => '/search.json',
			'params' => array('q'),
			'cache' => 300,
			'return' => 'json',
		),
	);

	private static $rest_methods = array(
		'showStatus' => array(
			'method' => 'GET',
			'url' => '/statuses/show/{id}.json',
			'cache' => 300,
			'return' => 'json',
		)
	);
}

?>