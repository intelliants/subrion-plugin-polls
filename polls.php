<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaPolls = $iaCore->factoryPlugin('polls');

	$iaDb->setTable('polls');

	$id = false;

	if (isset($iaCore->requestPath[0]))
	{
		$id = (int)$iaCore->requestPath[0];
	}

	if ($id)
	{
		// get one poll array by id
		$singlePoll = $iaPolls->getPoll($id);

		if (!empty($singlePoll))
		{
			$votedPolls = array();

			iaBreadcrumb::add(iaLanguage::get('polls'), IA_URL . 'polls/');
			iaBreadcrumb::replaceEnd(iaLanguage::get('poll'), IA_SELF);
			$title = $singlePoll['title'];

			$singlePoll['alreadyVoted'] = false;
			$singlePoll['options'] = $iaPolls->getOptions($id);

			if ($iaPolls->isVoted($id))
			{
				if (!$iaCore->get('polls_google_chart'))
				{
					$singlePoll['results'] = $iaPolls->printPollResults($singlePoll['options']);
				}

				$singlePoll['alreadyVoted'] = true;
			}
		}

		$iaView->assign('single_poll', $singlePoll);
	}
	else
	{
		$num_polls = $iaCore->get('polls_count_page');

		iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

		/** gets current page and defines start position **/
		$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
		$page = ($page < 1) ? 1 : $page;
		$start = ($page - 1) * $num_polls;

		$total = $iaPolls->iaDb->one(iaDb::STMT_COUNT_ROWS, "`status` = 'active'");

		/** get polls by status **/
		$all_polls = $iaPolls->iaDb->all(array('id', 'title', 'date', 'expires'), "`date` <= NOW() AND `expires` > NOW() AND `lang`='" . $iaView->language . "' AND `status` = 'active' ORDER BY `expires` DESC", $start, $num_polls, 'polls');

		foreach ($all_polls as $i => $poll)
		{
			$alias = $poll['title'];
			if(!utf8_is_ascii($alias))
			{
				$alias = utf8_to_ascii($alias);
			}
			$all_polls[$i]['allias'] = iaSanitize::alias($alias);
		}

		$iaView->assign('total', $total);
		$iaView->assign('all_polls', $all_polls);

		$title = iaLanguage::get('polls');
	}

	$iaDb->resetTable();

	$iaView->assign('aTemplate', IA_URL . 'polls/?page={page}');
	$iaView->title(iaSanitize::tags($title));

	$iaView->display('polls');
}