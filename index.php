<?php

//##copyright##
if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$optionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$pollId = isset($_GET['poll_id']) ? (int)$_GET['poll_id'] : 0;

	if ($pollId == 0 || $optionId == 0)
	{
		return iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}

	$iaPolls = $iaCore->factoryPlugin('polls', iaCore::FRONT, 'polls');

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