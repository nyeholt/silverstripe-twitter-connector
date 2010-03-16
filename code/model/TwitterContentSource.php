<?php
/*

Copyright (c) 2009, SilverStripe Australia PTY LTD - www.silverstripe.com.au
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
 * 
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class TwitterContentSource extends ExternalContentSource
{
    public static $db = array(
		'Query' => 'Varchar(128)',
		'Username' => 'Varchar(64)',
		'Password' => 'Varchar(64)',
	);

	/**
	 *
	 * @var TwitterClient
	 */
	protected $client;

	
	protected $objectCache = array();

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', new TextField('Query', _t('TwitterConnector.SEARCH_QUERY', 'Search Query (optional)')));
		$fields->addFieldToTab('Root.Main', new TextField('Username', _t('ExternalContentSource.USER', 'Username')));
		$fields->addFieldToTab('Root.Main', new TextField('Password', _t('ExternalContentSource.PASS', 'Password')));
		
		return $fields;
	}


	public function getRemoteRepository()
	{
		if (!$this->client) {
			$this->client = new TwitterClient();
		}

		if (!$this->client->isConnected()) {
			$config = array(
				'username' => $this->Username,
				'password' => $this->Password,
			);

			try {
				$this->client->connect($config);
			} catch (Exception $zue) {
				error_log("Failed connecting to repository: ".$zue->getMessage()."\n");
			}
		}

		return $this->client;
	}

	public function encodeId($id) { return $id; }
	public function decodeId($id) { return $id; }


	/**
	 * Get the object represented by ID
	 *
	 * @param String $objectId
	 * @return DataObject
	 */
	public function getObject($objectId)
	{
		if (!isset($this->objectCache[$objectId])) {
			// get the object from the repository
			try {
				// need to pull the object from the twitter client
				$object = $this->getRemoteRepository()->call('showStatus', array('id' => $objectId));
				if ($object && $object->id) {
					$item = new TwitterContentItem($this, $object);
					$this->objectCache[$objectId] = $item;
				}
			} catch (Zend_Http_Client_Adapter_Exception $e) {
				error_log("Failed connecting to matrix server: ".$e->getMessage());
				$this->objectCache[$objectId] = null;
			}
		}

		return $this->objectCache[$objectId];
	}

	public function getRoot()
	{
		return $this;
	}

	/**
	 * Override to fool hierarchy.php
	 *
	 * @param boolean $showAll
	 * @return DataObjectSet
	 */
	public function stageChildren($showAll = false) {
		// if we don't have an ID directly, we should load and return ALL the external content sources
		if (!$this->ID) {
			return DataObject::get('TwitterContentSource');
		}

		$children = new DataObjectSet();
		try {
			if ($this->Query) {
				$client = $this->getRemoteRepository();
				if ($client->isConnected()) {
					$items = $client->search(array('q' => $this->Query));
					if ($items && isset($items->results)) {
						foreach ($items->results as $object) {
							$item = new TwitterContentItem($this, $object);
							$children->push($item);
						}
					}
				}
			}
		} catch (Exception $e) {
			SS_Log::log(__CLASS__.':'.__LINE__.':: '.$e->getMessage(), SS_Log::ERR);
		}

		return $children;
	}
}
?>