<?php
/**
 * @package     TJDashboard
 * @subpackage  com_tjdashboard
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Load component dependencies
$componentPath = JPATH_ADMINISTRATOR . '/components/com_tjdashboard';

if (file_exists($componentPath . '/includes/tjdashboard.php'))
{
	require_once $componentPath . '/includes/tjdashboard.php';
}

if (file_exists($componentPath . '/libraries/dashboard.php'))
{
	require_once $componentPath . '/libraries/dashboard.php';
}

if (file_exists($componentPath . '/libraries/widget.php'))
{
	require_once $componentPath . '/libraries/widget.php';
}

/**
 * Tjdashboard API plugin
 *
 * @since  1.0.0
 */
class PlgAPITjdashboard extends ApiPlugin
{
	/**
	 * Constructor
	 *
	 * @param   STRING  &$subject  subject
	 * @param   array   $config    config
	 *
	 * @since 1.0.0
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config = array());

		// Set resource path
		ApiResource::addIncludePath(dirname(__FILE__) . '/resources');

		// Load language files
		$lang = Factory::getLanguage();
		$lang->load('plg_api_tjdashboard', JPATH_ADMINISTRATOR, '', true);

		// Set the resource to be public
		$this->setResourceAccess('widget', 'public', 'get');
		$this->setResourceAccess('dashboard', 'public', 'get');
	}
}
