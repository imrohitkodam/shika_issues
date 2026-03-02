<?php
/**
 * @package    Com.Api
 *
 * @copyright  Copyright (C) 2005 - 2017 Techjoomla, Techjoomla Pvt. Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for list of keys
 *
 * @since  1.0
 */
class ApiViewKeys extends HtmlView
{
	/**
	 * The model state.
	 *
	 * @var   \stdClass
	 * @since 1.0
	 */
	protected $state;

	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $items;

	/**
	 * The pagination object.
	 *
	 * @var   Pagination
	 * @since 1.0
	 */
	protected $pagination;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		ApiHelper::addSubmenu('keys');

		$this->publish_states = array(
			'' => Text::_('JOPTION_SELECT_PUBLISHED'), '1' => Text::_('JPUBLISHED'), '0' => Text::_('JUNPUBLISHED'), '*' => Text::_('JALL')
		);

		$this->addToolbar();

		// JHtmlSidebar is deprecated in Joomla 4+, only use for Joomla 3.x
		if (version_compare(JVERSION, '4.0', 'lt'))
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$componentPath = JPATH_ADMINISTRATOR . '/components/com_api';
		require_once $componentPath . '/helpers/api.php';

		$state = $this->get('State');
		$canDo = ApiHelper::getActions($state->get('filter.category_id'));

		if (JVERSION >= '3.0')
		{
			ToolbarHelper::title(Text::_('COM_API_TITLE_KEYS'), 'key');
		}
		else
		{
			ToolbarHelper::title(Text::_('COM_API_TITLE_KEYS'), 'keys.png');
		}

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_ADMINISTRATOR . '/components/com_api/views/key';

		if (file_exists($formPath))
		{
			if ($canDo->core_create)
			{
				ToolbarHelper::addNew('key.add', 'JTOOLBAR_NEW');
			}

			if ($canDo->core_edit && isset($this->items[0]))
			{
				ToolbarHelper::editList('key.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->core_edit_state)
		{
			if (isset($this->items[0]->state))
			{
				ToolbarHelper::divider();
				ToolbarHelper::custom('keys.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolbarHelper::custom('keys.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($canDo->core_delete)
			{
				ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'keys.delete', 'JTOOLBAR_DELETE');
				ToolbarHelper::divider();
			}
		}

		if ($canDo->core_admin)
		{
			ToolbarHelper::preferences('com_api');
		}

		// Set sidebar action - New in 3.0, deprecated in Joomla 4+
		if (version_compare(JVERSION, '3.0.0', 'ge') && version_compare(JVERSION, '4.0', 'lt'))
		{
			JHtmlSidebar::setAction('index.php?option=com_api&view=keys');
			$this->extra_sidebar = '';
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.id' => Text::_('JGRID_HEADING_ID'), 'a.userid' => Text::_('COM_API_KEYS_USERID'), 'a.domain' => Text::_('COM_API_KEYS_DOMAIN'),
				'a.state' => Text::_('JSTATUS'), 'a.last_used' => Text::_('COM_API_KEYS_LAST_USED')
		);
	}
}
