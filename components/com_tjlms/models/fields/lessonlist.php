<?php
/**
 * @package    Shika
 * @author     TechJoomla | <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list
 *
 * @since  1.0.0
 */
class JFormFieldLessonlist extends FormFieldList
{
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getOptions()
	{
		$db  = Factory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('title'));
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__tjlms_lessons'));
		$query->where(
			'id IN ' . '(SELECT tjt.lesson_id FROM `#__tjlms_tmtquiz` AS tjt LEFT JOIN #__tmt_tests AS tt ON(tt.id=tjt.test_id)
			WHERE tt.isObjective="0" OR tt.gradingtype ="exercise" OR tt.gradingtype ="feedback" )');
		$query->where($db->quoteName('created_by') . '=' . $db->quote(Factory::getUser()->id));

		$db->setQuery($query);

		$results = $db->loadObjectlist();

		$options[] = JHTML::_('select.option', '', Text::_("TJLMS_SELECT_LESSON"));

		foreach ($results as $result)
		{
			$options[]   = HTMLHelper::_('select.option', $result->id, $result->title);
		}

		return $options;
	}
}
