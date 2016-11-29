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
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

class iaPolls extends abstractCore
{
	protected static $_table = 'polls';
	protected $_tableOptions = 'poll_options';
	protected $_tableClicks = 'poll_clicks';


	public function getOptions($id)
	{
		return $this->iaDb->all(array('id', 'title', 'votes'), iaDb::convertIds($id, 'poll_id'), 0, null, $this->_tableOptions);
	}

	public function getById($id)
	{
		return $this->iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id), self::getTable());
	}

	/**
	 * Return polls for specified category id and if recursive then it gets child categories
	 *
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getPolls($start, $limit)
	{
		$rand = $this->iaCore->get('polls_rand', 0);
		$stmt = 'date_start <= NOW() AND `date_expire` > NOW() AND lang = :lang AND status = :status';
		$this->iaDb->bind($stmt, array('lang' => $this->iaView->language, 'status' => iaCore::STATUS_ACTIVE));
		$order = iaDb::printf(' ORDER BY :order', array('order' => ($rand ? 'RAND()' : 'date_expire DESC')));

		return $this->iaDb->all(iaDb::STMT_CALC_FOUND_ROWS . ' ' . iaDb::ALL_COLUMNS_SELECTION, $stmt . $order, $start, $limit, self::getTable());
	}

	public function printPollResults($options)
	{
		$this->iaView->loadSmarty(true);
		$iaSmarty = $this->iaView->iaSmarty;
		$total = 0;
		$colors = array('info', 'success', 'warning', 'danger');

		foreach($options as $i => $o)
		{
			$options[$i]['votes'] = (int)$options[$i]['votes'];
			$total += $options[$i]['votes'];
		}

		$iaSmarty->assign('total_text', str_replace('{num}', $total, iaLanguage::get('total_votes')));
		$iaSmarty->assign('total', $total);
		$iaSmarty->assign('options', $options);
		$iaSmarty->assign('colors', $colors);

		return $iaSmarty->fetch(IA_PLUGINS . 'polls/templates/front/poll-results.tpl');
	}

	/**
	 * Checks if a user already voted (by IP)
	 *
	 * @param string $id poll id
	 *
	 * @return bool
	 */
	public function isVoted($id)
	{
		$ip = $this->iaCore->util()->getIp();

		$fromIp = $this->iaDb->exists('poll_id = :id AND ip = :ip AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= 1',
			array('id' => $id, 'ip' => $ip), $this->_tableClicks);

		$fromCookie = isset($_COOKIE['votedPolls']) && in_array($id, explode(',', $_COOKIE['votedPolls']));

		if($fromIp || $fromCookie)
		{
			return true;
		}

		return false;
	}

	/**
	 * Adds record when poll is voted
	 *
	 * @param int $pollId poll id
	 * @param int $optionId option id
	 *
	 * return @bool
	 */
	public function addVote($pollId, $optionId)
	{
		$entry = array(
			'poll_id' => $pollId,
			'ip' => $this->iaCore->util()->getIp(),
		);

		$this->iaDb->insert($entry, array('date' => iaDb::FUNCTION_NOW), $this->_tableClicks);

		if (isset($_COOKIE['votedPolls']) && $_COOKIE['votedPolls'])
		{
			setcookie('votedPolls', $_COOKIE['votedPolls'] . ',' . $pollId, time() + 3600, '/');
		}
		else
		{
			setcookie('votedPolls', $pollId, time() + 3600, '/');
		}
		$stmt = iaDb::printf('`id` = :option_id AND `poll_id` = :poll_id', array(
			'option_id' => $optionId,
			'poll_id' => $pollId,
		));

		return $this->iaDb->update(array(), $stmt, array('votes' => '`votes` + 1'), $this->_tableOptions);
	}
}