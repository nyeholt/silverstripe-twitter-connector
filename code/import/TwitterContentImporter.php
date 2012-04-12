<?php

/**
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class TwitterContentImporter extends ExternalContentImporter {
	public function __construct() {
		parent::__construct();
		$this->contentTransforms['Tweet'] = new TwitterContentItemImporter();
	}

	protected function getExternalType($item) {
		return 'Tweet';
	}
}
