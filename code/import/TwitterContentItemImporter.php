<?php

/**
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class TwitterContentItemImporter implements ExternalContentTransformer {
	protected function importItem($item, $parentObject, $duplicateStrategy) {
		if ($item) {
			$externalId = $item->getExternalId();
			$parentFilter = $parentObject ? ' AND "ParentID" = ' . ((int) $parentObject->ID) : '';
			$existing = DataObject::get_one('Page', '"MetaTitle" = \'' . $externalId . '\'' . $parentFilter);
			if ($existing && $existing->exists()) {
				if ($duplicateStrategy == ExternalContentTransformer::DS_SKIP) {
					return;
				}
				if ($duplicateStrategy == ExternalContentTransformer::DS_DUPLICATE) {
					$existing = new Page();
				}
			} else {
				$existing = new Page();
			}

			$existing->Title = 'Tweet: ' . $item->Title;
			$existing->MetaTitle = $externalId;
			$existing->Content = $item->Tweet;
			if ($parentObject) {
				$existing->ParentID = $parentObject->ID;
			}
			$existing->write();
			$existing->Created = date('Y-m-d H:i:s', strtotime($item->Created));
			$existing->LegacyURL = $item->Link;
			$existing->MetaDescription = 'Tweet from ' . $item->CreatedBy . ' at ' . $item->Created;
			
			$existing->write();
			
			
			return $existing;
		}
	}
	
	public function transform($item, $parentObject, $duplicateStrategy) {
		$new = $this->importItem($item, $parentObject, $duplicateStrategy);
		return new TransformResult($new, null);
	}
}
