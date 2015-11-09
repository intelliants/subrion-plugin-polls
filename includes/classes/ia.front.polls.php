<?php
//##copyright##

class iaPolls extends abstractCore
{

	protected static $_table = 'polls';

	protected static $_tableOptions = 'poll_options';

	protected static $_tableClicks = 'poll_clicks';

	public function getPoll($aId)
	{
		$where = "`lang`='" . IA_LANGUAGE . "' AND `status` = 'active' AND `id` = '" . $aId . "'";
		$return = $this->iaDb->row('`id`,`title`, `date`, `expires`', $where, self::getTable());

		return $return;
	}

	function getOptions($aId)
	{
		$out = $this->iaDb->all('`id`,`title`, `votes`', "`poll_id` = '{$aId}' ", 0, null, self::$_tableOptions);

		return $out;
	}

	/**
	* Return polls for specified category id and if recursive then it gets child categories
	*
	* @param str $position the position of the poll (top,left,bottom,right ...)
	* @param int $currentCategory current category
	*
	* @return arr
	*/
	public function getPolls()
	{
		$this->iaDb->setTable(self::getTable());
		$limit = $this->iaCore->get('polls_count', 1);
		$rand = $this->iaCore->get('polls_rand', 0);
		$return = $this->iaDb->all(
			'`id`,`title`, `date`, `expires` ',
			"`date`<=NOW() AND `expires` > NOW() AND `lang`='" . IA_LANGUAGE . "' AND `status` = 'active' ORDER BY " . ($rand ? 'RAND()' : '`expires` DESC'), 0, $limit);
		$this->iaDb->resetTable();

		return $return;
	}

	function printPollResults($options)
	{
		$iaSmarty = $this->iaCore->factory(iaCore::CORE, 'smarty');
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
	* @param str $aId poll id
	*
	* @return bool
	*/
	function isVoted($aId)
	{
		$ip = $this->iaCore->util()->getIp();

		$fromIp = $this->iaDb->exists("`poll_id`='" . $aId . "' AND `ip`='" . $ip . "' AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= 1 ", array(), self::$_tableClicks);

		$fromCookie = isset($_COOKIE['votedPolls']) && in_array($aId, explode(',', $_COOKIE['votedPolls']));

		if($fromIp || $fromCookie)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* Adds record when poll is voted
	*
	* @param int $aId poll id
	* @param str $aIp ip address
	*/
	public function addVote($pollId, $optionId)
	{
		$entry = array(
			'poll_id' => $pollId,
			'ip' => $this->iaCore->util()->getIp()
		);

		$this->iaDb->insert($entry, array('date' => iaDb::FUNCTION_NOW), self::$_tableClicks);

		if (isset($_COOKIE['votedPolls']) && !empty($_COOKIE['votedPolls']))
		{
			setcookie('votedPolls', $_COOKIE['votedPolls'] . ',' . $pollId, time() + 3600, '/');
		}
		else
		{
			setcookie('votedPolls', $pollId, time() + 3600, '/');
		}

		return $this->iaDb->update(array(), "`id`='" . $optionId . "' AND `poll_id`='" . $pollId . "'", array("votes" => "votes + 1"), self::$_tableOptions);
	}
}