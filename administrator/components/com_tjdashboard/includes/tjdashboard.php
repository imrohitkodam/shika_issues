<?php
/**
 * @package     TJDashboard
 * @subpackage  com_tjdashboard
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

JLoader::discover("Tjdashboard", JPATH_ADMINISTRATOR . '/components/com_tjdashboard/libraries');

/**
 * Tjdashboard factory class.
 *
 * This class perform the helpful operation for truck app
 *
 * @since  1.0.0
 */
class TjdashboardFactory
{
	/**
	 * Retrieves a table from the table folder
	 *
	 * @param   string  $name  The table file name
	 *
	 * @return	JTable object
	 *
	 * @since 	1.0.0
	 **/
	public static function table($name)
	{
		// @TODO Improve file loading with specific table file.

		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjdashboard/tables');

		// Create table instance using new method
		$className = 'TjdashboardTable' . ucfirst($name);
		
		if (!class_exists($className))
		{
			$tablePath = JPATH_ADMINISTRATOR . '/components/com_tjdashboard/tables/' . $name . '.php';
			
			if (file_exists($tablePath))
			{
				require_once $tablePath;
			}
		}
		
		if (class_exists($className))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			return new $className($db);
		}

		return null;
	}

	/**
	 * Retrieves a model from the model folder
	 *
	 * @param   string  $name    The model name to instantiate
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return	JModel object
	 *
	 * @since 	1.0.0
	 **/
	public static function model($name, $config = array())
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjdashboard/models');

		// Create model instance using new method
		$className = 'TjdashboardModel' . ucfirst($name);
		
		if (!class_exists($className))
		{
			$modelPath = JPATH_ADMINISTRATOR . '/components/com_tjdashboard/models/' . strtolower($name) . '.php';
			
			if (file_exists($modelPath))
			{
				require_once $modelPath;
			}
		}
		
		if (class_exists($className))
		{
			return new $className($config);
		}

		return null;
	}
}
