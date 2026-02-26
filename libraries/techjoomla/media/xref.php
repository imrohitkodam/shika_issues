<?php
/**
 * @package     Techjoomla.Libraries
 * @subpackage  TjMedia
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Filesystem\Folder;

JLoader::import("/techjoomla/media/storage/local", JPATH_LIBRARIES);
jimport('techjoomla.object.object');

/* load language file for plugin frontend */
$lang = Factory::getLanguage();
$lang->load('lib_techjoomla', JPATH_SITE, '', true);

/**
 * TJMediaXref class.
 *
 * @since  1.0.0
 */
class TJMediaXref
{
	// Xref table id
	public $id = 0;

	// Media table id
	public $media_id = 0;

	public $client_id = null;

	public $client = null;

	public $is_gallery = null;

	/**
	 * Method to initialise class based on global setting
	 *
	 * @param   array  $configs  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 */
	public function __construct($configs = array())
	{
		$mediaConfig = array();

		if (isset($configs['mediaId']))
		{
			$mediaConfig['id'] = $configs['mediaId'];
		}

		if (isset($configs['id']))
		{
			$this->load($configs['id']);

			$data = $this->getProperties();

			$mediaConfig['id'] = $data['media_id'];
		}

		$this->media = TJMediaStorageLocal::getInstance($mediaConfig);
	}

	/**
	 * Method to load a media xref object by xref id
	 *
	 * @param   mixed  $id  The id of the object to get.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function load($id)
	{
		JLoader::import("/techjoomla/media/tables/xref", JPATH_LIBRARIES);

		$mvcFactory = Factory::getApplication()
			->bootComponent('com_tjlms')
			->getMVCFactory();
		$table = $mvcFactory->createTable('Xref', 'Administrator', ['prefix' => 'TJMediaTable']);

		// Load the object based on the id or throw a warning.
		if (! $table->load($id))
		{
			throw new \RuntimeException("LIB_TECHJOOMLA_MEDIA_NO_MEDIA_FILE_IN_XREF_TABLE");
		}

		// Assuming all is well at this point let's bind the data
		foreach ($table->getProperties() as $key => $value)
		{
			$this->$key = $value;
		}

		return true;
	}

	/**
	 * Returns the global media object
	 *
	 * @param   array  $options  media xref object to instantiate
	 *
	 * @return  object
	 *
	 * @since   1.0.0
	 */
	public static function getInstance($options = array())
	{
		if (empty($options))
		{
			return new TJMediaXref;
		}

		// @TODO Load from cache
		return new TJMediaXref($options);
	}

	/**
	 * Method to save the data.
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0.0
	 */
	public function save()
	{
		JLoader::import("/techjoomla/media/tables/xref", JPATH_LIBRARIES);

		$mvcFactory = Factory::getApplication()
			->bootComponent('com_tjlms')
			->getMVCFactory();
		$tjmediaXrefTable = $mvcFactory->createTable('Xref', 'Administrator', ['prefix' => 'TJMediaTable']);

		$data = get_object_vars($this);

		$tjmediaXrefTable->load(array('media_id' => (int) $data['media_id'], 'client_id' => (int) $data['client_id'], 'client' => $data['client']));

		$this->id = $tjmediaXrefTable->id;

		if (!$tjmediaXrefTable->save(get_object_vars($this)))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method to bind an associative array of data to a user object
	 *
	 * @param   array  $data  The associative array to bind to the object
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function bind($data = array())
	{
		// Bind the array
		foreach ($data as $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $value;
			}
		}

		return true;
	}

	/**
	 * Method to delete the media from table
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function delete()
	{
		JLoader::import("/techjoomla/media/tables/xref", JPATH_LIBRARIES);
		$mvcFactory = Factory::getApplication()
			->bootComponent('com_tjlms')
			->getMVCFactory();
		$mediaXrefTable = $mvcFactory->createTable('Xref', 'Administrator', ['prefix' => 'TJMediaTable']);

		if ($this->id)
		{
			if (!$mediaXrefTable->delete($this->id))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		elseif ($this->media->id)
		{
			// To check if the media is in use whith other client then don't delete
			$xrefMedia = $mediaXrefTable->load(array('media_id' => $this->media->id));

			if (!$xrefMedia)
			{
				if (!$this->media->delete())
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				throw new \RuntimeException(Text::_('LIB_TECHJOOMLA_MEDIA_IN_USE_UNABLE_TO_DELETE'));
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to retrive the media using the getItems
	 *
	 * @param   array  $data  Array of data that has to be set using setState
	 *
	 * @return  array Array of data based on the required data provided
	 *
	 * @since   1.0.0
	 */
	public function retrive($data = array())
	{
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/libraries/techjoomla/media/models');
		$tjMediaXrefsModel = BaseDatabaseModel::getInstance('Xref', 'TJMediaModel', array("ignore_request" => false));

		if (isset($data['id']))
		{
			$tjMediaXrefsModel->setState('filter.isGallery', $data['id']);
		}

		if (isset($data['isGallery']))
		{
			$tjMediaXrefsModel->setState('filter.isGallery', $data['isGallery']);
		}

		if (isset($data['client']))
		{
			$tjMediaXrefsModel->setState('filter.client', $data['client']);
		}

		if (isset($data['clientId']))
		{
			$tjMediaXrefsModel->setState('filter.clientId', $data['clientId']);
		}

		if (isset($data['mediaId']))
		{
			$tjMediaXrefsModel->setState('filter.mediaId', $data['mediaId']);
		}

		return $tjMediaXrefsModel->getItems();
	}
}
