<?php
/**
 * @package     TJDashboard
 * @subpackage  com_tjdashboard
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

// Include dependancies
jimport('joomla.application.component.controller');

$document = Factory::getDocument();
$script  = 'const root_url = "' . Uri::root() . '";';
$document->addScriptDeclaration($script, 'text/javascript');

JLoader::registerPrefix('Tjdashboard', JPATH_ADMINISTRATOR . '/components/com_tjdashboard');
JLoader::register('TjdashboardController', JPATH_ADMINISTRATOR . '/components/com_tjdashboard/controller.php');

$app = Factory::getApplication();
$input = $app->getInput();

// Execute the task.
$controller = BaseController::getInstance('Tjdashboard');
$controller->execute($input->get('task'));
$controller->redirect();
