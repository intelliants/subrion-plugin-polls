<?php
//##copyright##

class iaPolls extends abstractCore
{

	protected static $_table = 'polls';

	function row($fields, $where='')
	{
		$iaDb = &$this->iaDb;

		$iaDb->setTable(self::getTable());
		$poll = $iaDb->row($fields, $where);
		$iaDb->resetTable();

		$iaDb->setTable("poll_options");
		$where = $t = str_replace("id", "poll_id", $where);
		$poll['options'] = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, $where);
		$iaDb->resetTable();

		return $poll;
	}

	public function delete($where = '')
	{
		$iaDb = &$this->iaDb;

		$iaDb->delete($where, self::getTable());

		$where = str_replace('id', 'poll_id', $where);

		$iaDb->delete($where, 'poll_options');
		$iaDb->delete($where, 'poll_clicks');

		return true;
	}

	function update($fields, $where='', $addit = array())
	{
		$iaDb = &$this->iaDb;

		if(isset($fields['newoptions']))
		{
			$opt = $fields['newoptions'];
			unset($fields['newoptions']);
		}
		if(isset($fields['options']))
		{
			// update existant options
			$options = $fields['options'];
			unset($fields['options']);
		}

		if(!empty($options))
		{
			$iaDb->setTable("poll_options");
			$deleteOptionsIds = array();
			// updat existant options
			foreach($options as $id => $t)
			{
				if(empty($t))
				{
					$deleteOptionsIds[] = $id;
				}
				else
				{
					$iaDb->update(array("id" => $id,"title" => $t));
				}
			}
			if(!empty($deleteOptionsIds))
			{
				$ids = implode(",", $deleteOptionsIds);
				$iaDb->delete("`id` IN(" . $ids . ")");
			}
			$iaDb->resetTable();
		}
		// end update existing options
		if(!empty($opt))
		{
			// insert new options
			$iaDb->setTable("poll_options");
			$options = array();
			foreach($opt as $title)
			{
				if ($title != '')
				{
					$options[] = array("title" => $title, "votes" => 0, "poll_id" => $fields['id']);
				}
			}
			$iaDb->insert($options);
			$iaDb->resetTable();
		}

		$f = array(
			'title'		=> $fields['title'],
			'lang'		=> $fields['language'],
			'status'	=> $fields['status'],
			'date'		=> $fields['start_date'],
			'expires'	=> $fields['expire_date'],
			'recursive'	=> $fields['recursive'],
		);
		// update poll itself
		$iaDb->update($f, $where);

		return true;
	}

	function insert($fields, $addit = array())
	{
		$iaDb = &$this->iaDb;

		$opt = $fields['newoptions'];

		$f = array(
			'title'		=> $fields['title'],
			'lang'		=> $fields['language'],
			'status'	=> $fields['status'],
			'date'		=> $fields['start_date'],
			'expires'	=> $fields['expire_date'],
			'recursive'	=> $fields['recursive'],
		);
		$iaDb->setTable('polls');
		$poll_id = $iaDb->insert($f);
		$iaDb->resetTable();

		$iaDb->setTable('poll_options');
		$options = array();
		foreach($opt as $title)
		{
			if (trim($title) != '')
			{
				$options[] = array("title" => $title, "votes" => 0, "poll_id" => $poll_id);
			}
		}
		$iaDb->insert($options);
		$iaDb->resetTable();

		return $poll_id;
	}

	/**
	* Checks if a link was already voted
	*
	* @param str $aIp vote ip
	* @param int $aId poll id
	*
	* @return int
	*/
	function isVoted($aIp, $aId)
	{
		$iaDb = &$this->iaDb;

		$iaDb->setTable("polls");
		$return = $iaDb->exists("`poll_id` = '" . $aId . "' AND `ip` = '" . $aIp . "' AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= 1 ");
		$iaDb->resetTable();

		return $return;
	}

	function num()
	{
		$iaDb = &$this->iaDb;

		$iaDb->setTable("polls");
		$return = $iaDb->one("COUNT(*) `num`");
		$iaDb->resetTable();

		return $return;
	}
}