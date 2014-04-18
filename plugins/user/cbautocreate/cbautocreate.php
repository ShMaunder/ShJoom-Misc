<?php
/**
 * PHP Version 5.3
 *
 * Thanks to Chris for the original plugin used for a previous version of
 * Community Builder and Joomla 1.5. This plugin has been rewritten from
 * scratch to work with Joomla 2.5 and Community Builder 1.8.
 * http://www.joomlapolis.com/forum/40-cb-newbies/57781-auto-approve-and-auto-confirm-users-with-sso#57882
 *
 * @package     Shmanic.Plugin
 * @subpackage  User
 * @author      Shaun Maunder <shaun@shmanic.com>
 *
 * @copyright   Copyright (C) 2011-2013 Shaun Maunder. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.plugin.plugin');

/**
 * Community Builder auto user creation plugin.
 *
 * The plugin was created to help with CB Single Sign On.
 *
 * @package     Shmanic.Plugin
 * @subpackage  User
 * @since       1.0
 */
class PlgUserCBAutoCreate extends JPlugin
{
	/**
	 * Method is called after user data is stored in the database.
	 *
	 * This method creates a CB user if one doesn't exist.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isNew    True if a new user has been stored.
	 * @param   boolean  $success  True if user was successfully stored in the database.
	 * @param   string   $msg      An error message.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onUserAfterSave($user, $isNew, $success, $msg)
	{
		if (!$this->params->get('only_new', true) || $isNew)
		{
			$userId = (int) $user['id'];

			$db = JFactory::getDBO();

			// Check if the CB user already exists
			$db->setQuery(
				$db->getQuery(true)
					->select($db->quoteName('id'))
					->from($db->quoteName('#__comprofiler'))
					->where($db->quoteName('id') . ' = ' . $db->quote($userId))
			);

			if (!$db->loadResult())
			{
				// Create the CB user table entry
				$db->setQuery(
					$db->getQuery(true)
						->insert($db->quoteName('#__comprofiler'))
						->columns(array($db->quoteName('id'), $db->quoteName('user_id')))
						->values($db->quote($userId) . ', ' . $db->quote($userId))
				);

				$db->execute();
			}
		}
	}
}
