<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <http://www.intelliants.com>
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

$iaPolls = $iaCore->factoryModule('poll', 'polls');

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$optionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$pollId = isset($_GET['poll_id']) ? (int)$_GET['poll_id'] : 0;

	if (empty($pollId) || empty($optionId))
	{
		return iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}

	$affected = false;

	if (!$iaPolls->isVoted($pollId))
	{
		$affected = $iaPolls->addVote($pollId, $optionId);
	}

	if ($affected) // exists
	{
		$options  = $iaPolls->getOptions($pollId);
		$iaView->assign(array('results' => $iaPolls->printPollResults($options)));
	}
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$id = isset($iaCore->requestPath[0]) && (int)$iaCore->requestPath[0] ? (int)$iaCore->requestPath[0] : 0;

	if ($id)
	{
		$poll = $iaPolls->getById($id);
		if (empty($poll))
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}
		$title = $poll[0]['title'];

		iaBreadcrumb::add(iaLanguage::get('polls'), IA_URL . 'polls/');
		iaBreadcrumb::replaceEnd(iaLanguage::get('poll'), IA_SELF);

		$poll['alreadyVoted'] = false;
		$poll['options'] = $iaPolls->getOptions($id);

		if ($iaPolls->isVoted($id))
		{
			if (!$iaCore->get('polls_google_chart'))
			{
				$poll['results'] = $iaPolls->printPollResults($poll['options']);
			}

			$poll['alreadyVoted'] = true;
		}

		$iaView->assign('poll', $poll);
	}
	else
	{
		$title = iaLanguage::get('polls');

		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
		$page = ($page < 1) ? 1 : $page;

		$pagination = array(
			'start' => ($page - 1) * $iaCore->get('polls_count_page'),
			'limit' => $iaCore->get('polls_count_page'),
			'template' => 'polls?page={page}',
		);
		$polls = $iaPolls->getPolls($pagination['start'], $pagination['limit']);
		$pagination['total'] = $iaDb->foundRows();

		$iaView->assign('polls', $polls);
		$iaView->assign('pagination', $pagination);
	}

	$iaView->title($title);
	$iaView->display('polls');
}