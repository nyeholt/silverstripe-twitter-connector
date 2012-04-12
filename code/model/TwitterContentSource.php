<?php

/**
 * 
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class TwitterContentSource extends ExternalContentSource {

	static $icon = 'twitter-connector/images/twitter';
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

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Main', new TextField('Query', _t('TwitterConnector.SEARCH_QUERY', 'Search Query (optional)')));
		$fields->addFieldToTab('Root.Main', new TextField('Username', _t('ExternalContentSource.USER', 'Username')));
		$fields->addFieldToTab('Root.Main', new TextField('Password', _t('ExternalContentSource.PASS', 'Password')));

		return $fields;
	}

	/**
	 * Return a new content importer 
	 * @see external-content/code/dataobjects/ExternalContentSource#getContentImporter()
	 */
	public function getContentImporter($target=null) {
		return new TwitterContentImporter();
	}

	public function allowedImportTargets() {
		return array('sitetree' => true);
	}

	public function getRemoteRepository() {
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
				error_log("Failed connecting to repository: " . $zue->getMessage() . "\n");
			}
		}

		return $this->client;
	}

	public function encodeId($id) {
		return $id;
	}

	public function decodeId($id) {
		return $id;
	}

	/**
	 * Get the object represented by ID
	 *
	 * @param String $objectId
	 * @return DataObject
	 */
	public function getObject($objectId) {
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
				error_log("Failed connecting to matrix server: " . $e->getMessage());
				$this->objectCache[$objectId] = null;
			}
		}

		return $this->objectCache[$objectId];
	}

	public function getRoot() {
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
			SS_Log::log(__CLASS__ . ':' . __LINE__ . ':: ' . $e->getMessage(), SS_Log::ERR);
		}

		return $children;
	}

}
