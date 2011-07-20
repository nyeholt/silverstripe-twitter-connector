<?php
/**
 * 
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class TwitterContentItem extends ExternalContentItem {

	public static $icon = 'twitter-connector/images/tweet';

	public function __construct($source=null, $object=null) {
		parent::__construct($source, is_object($object) ? $object->id : $object);
		if (is_object($object)) {
			$createUser = isset($object->user) ? $object->user->screen_name : $object->from_user;
			$this->remoteProperties['Title'] = sprintf(_t('TwitterConnector.CREATED_AT', '%s - tweeted at %s'), $createUser, $object->created_at);
			$this->remoteProperties['CreatedBy'] = $createUser;
			$this->remoteProperties['Tweet'] = $object->text;
			$this->remoteProperties['Created'] = $object->created_at;
			$this->remoteProperties['Link'] = 'http://twitter.com/' . $createUser . '/status/' . $object->id;
		}
	}

	/**
	 * Overridden to pass the content through as its downloaded (if it's not cached locally)
	 */
	public function streamContent() {
		
	}

	/**
	 * Return the asset type
	 * @see external-content/code/model/ExternalContentItem#getType()
	 */
	public function getType() {
		return 'file';
	}

	/**
	 * Overridden to load all children from Alfresco instead of this node
	 * directly
	 *
	 * @param boolean $showAll
	 * @return DataObjectSet
	 */
	public function stageChildren($showAll = false) {
		if (!$this->ID) {
			return DataObject::get('TwitterContentItem');
		}

		return new DataObjectSet();
	}

	/**
	 * Check the object type; if it's a Document, return 0, otherwise
	 * return one as we don't know whether this type has children or not
	 *
	 * @return int
	 */
	public function numChildren() {
		return 0;
	}

}

?>