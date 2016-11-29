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

if (iaView::REQUEST_HTML == $iaView->getRequestType() && $iaView->blockExists('polls'))
{
	$iaPolls = $iaCore->factoryPlugin('polls', iaCore::FRONT, 'polls');

	$polls = $iaPolls->getPolls(0, $iaCore->get('polls_count'));
	$ip = $iaCore->util()->getIp();

	if ($polls)
	{
		foreach ($polls as $k => $p)
		{
			$polls[$k]['options'] = $iaPolls->getOptions($p['id']);
			$polls[$k]['alreadyVoted'] = false;
			if ($iaPolls->isVoted($p['id'], $ip))
			{
				$polls[$k]['results'] = $iaPolls->printPollResults($polls[$k]['options']);
				unset($polls[$k]['options']);

				$polls[$k]['alreadyVoted'] = true;
			}
		}

		$iaView->assign('block_polls', $polls);
	}
}