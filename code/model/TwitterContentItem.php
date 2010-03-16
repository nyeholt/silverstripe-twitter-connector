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
class TwitterContentItem extends ExternalContentItem
{
    public function __construct($source=null, $object=null)
	{
		parent::__construct($source, is_object($object) ? $object->id : $object);
		if (is_object($object)) {
			$createUser = isset($object->user) ? $object->user->screen_name : $object->from_user;
			$this->remoteProperties['Title'] = sprintf(_t('TwitterConnector.CREATED_AT', 'Created at %s by %s'), $object->created_at, $createUser);
			$this->remoteProperties['CreatedBy'] = $createUser;
			$this->remoteProperties['Content'] = $object->text;
			$this->remoteProperties['Created'] = $object->created_at;
			$this->remoteProperties['Link'] = 'http://twitter.com/'.$createUser.'/status/'.$object->id;
		}
	}

	/**
	 * Overridden to pass the content through as its downloaded (if it's not cached locally)
	 */
	public function streamContent()
	{
	}

	/**
	 * Return the asset type
	 * @see external-content/code/model/ExternalContentItem#getType()
	 */
	public function getType()
	{
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
	public function numChildren()
	{
		return 0;
	}
}
?>