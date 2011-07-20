<?php

/**
 * Description of TwitterImporter
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class TwitterImporter extends ExternalContentImporter {
	public function __construct() {
		$this->contentTransforms['tweet'] = new TweetImporter();
	}

	public function getExternalType($item) {
		return 'tweet';
	}
}