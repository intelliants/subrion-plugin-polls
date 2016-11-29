<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package Subrion\Plugin\PersonalBlog\Admin
 * @link http://www.subrion.org/
 * @author https://intelliants.com/ <support@subrion.org>
 * @license http://www.subrion.org/license.html
 *
 ******************************************************************************/

class iaBackendController extends iaAbstractControllerPluginBackend
{
	protected $_name = 'polls';

	protected $_table = 'polls';
	protected $_tableOptions = 'poll_options';
	protected $_tableBlogEntriesTags = 'blog_entries_tags';

	protected $_pluginName = 'polls';

	protected $_gridColumns = array('id', 'title', 'date_start', 'date_expire', 'status');
	protected $_gridFilters = array('status' => self::EQUAL, 'title' => self::LIKE);

	protected $_phraseAddSuccess = 'poll_added';
	protected $_phraseEditSuccess = 'poll_updated';


	public function __construct()
	{
		parent::__construct();

		$this->setHelper($this->_iaCore->factoryPlugin($this->getPluginName(), iaCore::ADMIN, $this->getName()));
	}

	protected function _indexPage(&$iaView)
	{
		$iaView->grid('_IA_URL_plugins/' . $this->getPluginName() . '/js/admin/index');
	}

	protected function _setPageTitle(&$iaView)
	{
		if (in_array($iaView->get('action'), array(iaCore::ACTION_ADD, iaCore::ACTION_EDIT)))
		{
			$iaView->title(iaLanguage::get($iaView->get('action') . '_poll'));
		}
	}

	protected function _setDefaultValues(array &$entry)
	{
		$entry['title'] = '';
		$entry['date_expire'] = date(iaDb::DATE_FORMAT, time() + 7 * 24 * 60 * 60);
		$entry['date_start'] = date(iaDb::DATE_FORMAT);
		$entry['status'] = iaCore::STATUS_ACTIVE;
		$entry['lang'] = $this->_iaCore->iaView->language;
	}

	protected function _entryDelete($id)
	{
		return (bool)$this->getHelper()->delete($id);
	}

	protected function _preSaveEntry(array &$entry, array $data, $action)
	{
		parent::_preSaveEntry($entry, $data, $action);

		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		if (!utf8_is_valid($data['title']))
		{
			$data['title'] = utf8_bad_replace($data['title']);
		}
		if (empty($data['title']))
		{
			$this->addMessage('title_is_empty');
		}

		if (empty($data['date_start']))
		{
			$data['date_start'] = date(iaDb::DATETIME_FORMAT);
		}

		if (empty($data['date_expire']))
		{
			$this->addMessage(iaLanguage::getf('field_empty', array('field' => iaLanguage::get('date_expire'))));
		}
		else
		{
			list($y, $m, $d) = explode('-', $data['date_expire']);
			$m = (int)$m;
			$d = (int)$d;
			$y = (int)$y;
			$end_date = 0;
			if (checkdate($m, $d, $y))
			{
				$item['date_expire'] = $y . '-' . $m . '-' . $d;
				$end_date = mktime(0, 0, 0, $m, $d, $y);
			}

			list($y, $m, $d) = explode('-', $data['date_start']);
			$m = (int)$m;
			$d = (int)$d;
			$y = (int)$y;
			if (checkdate($m, $d, $y))
			{
				$form['start_date'] = $y . '-' . $m . '-' . $d;
			}

			if (mktime(0, 0, 0, $m, $d, $y) > $end_date)
			{
				$this->addMessage('error_poll_expire_date_less');
			}
		}

		$data['newoptions'] = isset($data['newoptions']) ? $this->_checkOptions($data['newoptions']) : array();
		$data['options'] = isset($data['options']) ? $this->_checkOptions($data['options']) : array();

		if (1 >= count(array_merge($data['newoptions'], $data['options'])))
		{
			$this->addMessage('error_poll_options_required');
		}
		unset($entry['options'], $entry['newoptions']);

		return !$this->getMessages();
	}

	private function _checkOptions($options)
	{
		if (empty($options) || !is_array($options))
		{
			return array();
		}

		$options = array_unique($options);
		foreach ($options as $key => &$option)
		{
			$option = utf8_bad_replace($option);
			if (empty($options[$key]))
			{
				unset($options[$key]);
			}
		}

		return $options;
	}

	private function _saveOptions($options, $newOptions, $action)
	{
		$this->_iaDb->setTable($this->_tableOptions);
		if (iaCore::ACTION_EDIT == $action)
		{
			if ($oldOptions = $this->getHelper()->getOptions($this->getEntryId()))
			{
				$diff = array_keys(array_diff($oldOptions, array_filter($options)));
				empty($diff) || $this->_iaDb->query(iaDb::printf('DELETE FROM :table WHERE `id` IN (:ids)',
					array('table' => $this->_iaDb->prefix . $this->_tableOptions, 'ids' => implode(',', $diff))));
			}
			if ($options)
			{
				foreach ($options as $id => $option)
				{
					empty($option) || $this->_iaDb->update($option, iaDb::convertIds($id, 'poll_id'));
				}
			}
		}
		if ($newOptions)
		{
			foreach ($newOptions as $option)
			{
				empty($option) || $this->_iaDb->insert(array('poll_id' => $this->getEntryId(), 'votes' => 0, 'title' => $option));
			}
		}
	}

	protected function _postSaveEntry(array &$entry, array $data, $action)
	{
		$options = isset($data['options']) && is_array($data['options']) ? array_unique($data['options']) : array();
		$newoptions = isset($data['newoptions']) && is_array($data['newoptions']) ? array_unique($data['newoptions']) : array();
		$this->_saveOptions($options, $newoptions, $action);

		$iaLog = $this->_iaCore->factory('log');

		$actionCode = (iaCore::ACTION_ADD == $action)
			? iaLog::ACTION_CREATE
			: iaLog::ACTION_UPDATE;
		$params = array(
			'module' => 'polls',
			'item' => 'polls',
			'name' => $entry['title'],
			'id' => $this->getEntryId(),
		);

		$iaLog->write($actionCode, $params);
	}

	protected function _assignValues(&$iaView, array &$entryData)
	{
		$options = $this->getHelper()->getOptions($this->getEntryId());
		$newOptions = array();
		if (iaCore::ACTION_ADD == $iaView->get('action') && isset($_POST['newoptions']) && is_array($_POST['newoptions']))
		{
			$newOptions = array_filter($_POST['newoptions']);
		}
		$iaView->assign('options', $options);
		$iaView->assign('newoptions', $newOptions);
	}
}