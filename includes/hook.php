<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType() && $iaView->blockExists('polls'))
{
	$iaPolls = $iaCore->factoryPlugin('polls', iaCore::FRONT, 'polls');

	$polls = $iaPolls->getPolls();
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

		$iaView->assign('polls', $polls);
	}
}