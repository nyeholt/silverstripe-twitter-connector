<?php

/**
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class TweetImporter implements ExternalContentTransformer {
	public function transform($item, $parent, $strategy) {
		$page = new Page();
		$page->Title = $item->Title;
		$page->Content = $item->Tweet;
		$page->ParentID = $parent->ID;
		$page->write();
	}
}
