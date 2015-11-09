<?php
//##copyright##

$iaPolls = $iaCore->factoryPlugin('polls', 'admin', 'polls');

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	if (isset($_GET['action']))
	{
		$out = array('data' => '', 'total' => 0);

		if ('get' == $_GET['action'])
		{
			$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
			$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
			$order = '';

			$sort = $_GET['sort'];
			$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

			if ($sort && $dir)
			{
				$order = "ORDER BY `{$sort}` {$dir}";
			}

			$out['total'] = $iaDb->one(iaDb::STMT_COUNT_ROWS, null, $iaPolls->getTable());
			$out['data'] = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "1=1 {$order}", $start, $limit, $iaPolls->getTable());

			function remove_btn($item)
			{
				$item['remove'] = 1;
				$item['edit'] = 1;

				return $item;
			}
			$out['data'] = array_map('remove_btn', $out['data']);
		}

		$iaView->assign($out);
	}

	// process default actions
	if (isset($_POST['action']))
	{
		$out = array('msg' => 'Unknown error', 'error' => true);
		$where = '';

		if ('update' == $_POST['action'])
		{
			$result = $iaDb->update(array($_POST['field'] => $_POST['value']), "`id` IN('" . implode("','", $_POST['ids']) . "')", null, $iaPolls->getTable());

			if ($result)
			{
				$out['error'] = false;
				$out['msg'] = iaLanguage::get('changes_saved');
			}
			else
			{
				$out['error'] = true;
				$out['msg'] = $_SESSION['error'];
			}
		}

		if ('remove' == $_POST['action'])
		{
			$result = $iaPolls->delete("`id` IN('" . implode("','", $_POST['ids']) . "')");

			if ($result)
			{
				$out['error'] = false;
				$out['msg'] = iaLanguage::get('poll') . ' ' . iaLanguage::get('deleted');
			}
			else
			{
				$out['error'] = true;
				$out['msg'] = $iaPolls->message;
			}
		}

		$iaView->assign($out);
	}
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (in_array($pageAction, array(iaCore::ACTION_ADD, iaCore::ACTION_EDIT)))
	{
		$error = false;
		$messages = array();
		$options = array();
		// edit and add
		$form = array(
			'id'			=> isset($_GET['id']) ? (int)$_GET['id'] : 0,
			'title'			=> '',
			'expire_date'	=> date(iaDb::DATE_FORMAT, time() + 7 * 24 * 60 * 60),
			'start_date'	=> date(iaDb::DATE_FORMAT),
			'status'		=> iaCore::STATUS_INACTIVE,
			'recursive'		=> 0,
			'language'		=> 'en',
			'options'		=> array(),
			'newoptions'	=> array()
		);

		if ($pageAction == 'edit')
		{
			$poll_id = (int)$_GET['id'];

			$rows = $iaPolls->row(iaDb::ALL_COLUMNS_SELECTION, "`id` = " . $poll_id . ' ORDER BY `id`');

			$form['language'] = $rows['lang'];
			$form['title'] = $rows['title'];
			$form['status'] = $rows['status'];
			$form['recursive'] = $rows['recursive'];

			foreach($rows['options'] as $key => $val)
			{
				$form['options'][$val['id']] = $val['title'];
				$options[$val['id']] = $val['title'];
			}
			$date = explode(' ', $rows['date']);
			$form['start_date'] = $date[0];

			$date = explode(' ', $rows['expires']);
			$form['expire_date'] = $date[0];
		}

		if (isset($_POST['send']))
		{
			if (isset($_POST['language']) && isset($iaCore->languages[$_POST['language']]))
			{
				$form['language'] = $_POST['language'];
			}

			// Check title
			if (isset($_POST['title']) && trim($_POST['title']) != '')
			{
				$form['title'] = $_POST['title'];
			}
			else
			{
				$error = true;
				$messages[] = iaLanguage::get('poll_title_empty');
			}

			// Check status
			if (isset($_POST['status']) && in_array($_POST['status'], array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE)))
			{
				$form['status'] = $_POST['status'];
			}

			// Check expires
			list($y, $m, $d) = explode('-', $_POST['expire_date']);
			$m	= (int)$m;
			$d	= (int)$d;
			$y	= (int)$y;
			$end_date = 0;
			if (checkdate($m, $d, $y))
			{
				$form['expire_date'] = $y . '-' . $m . '-' . $d;
				$end_date = mktime(0, 0, 0, $m, $d, $y);
			}

			// Check stardate
			list($y, $m, $d) = explode('-', $_POST['start_date']);
			$m	= (int)$m;
			$d	= (int)$d;
			$y	= (int)$y;
			if (checkdate($m, $d, $y))
			{
				$form['start_date'] = $y . '-' . $m . '-' . $d;
			}

			if (mktime(0, 0, 0, $m, $d, $y) > $end_date)
			{
				$error = true;
				$messages[] = iaLanguage::get('error_poll_expire_date_less');
			}

			$count = 0;
			if ($pageAction == 'edit')
			{
				// Check OLD options
				if (empty($_POST['options']) || !is_array($_POST['options']))
				{
					$error = true;
					$messages[] = iaLanguage::get('error_poll_options_required');
				}
				else
				{
					$form['options'] = array_map('trim', $_POST['options']);
					$form['options'] = array_unique($form['options']);
					$count = count($form['options']);
					foreach($options as $i => $val)
					{
						if (!isset($form['options'][$i]))
						{
							$form['options'][$i] = '';
							$count--;
							continue;
						}
						$form['options'][$i] = $form['options'][$i];
					}
				}
			}

			// Check NEW options
			if (empty($_POST['newoptions']) || !is_array($_POST['newoptions']))
			{
				$error = true;
				$messages[] = iaLanguage::get('error_poll_options_required');
			}
			else
			{
				$form['newoptions'] = array_map('trim', $_POST['newoptions']);
				$form['newoptions'] = array_unique($form['newoptions']);
				foreach($form['newoptions'] as $i => $val)
				{
					if (empty($val))
					{
						unset($form['newoptions'][$i]);
						continue;
					}
					$form['newoptions'][$i] = $form['newoptions'][$i];
				}
				if (count($form['newoptions']) + $count < 2)
				{
					$error = true;
					$messages[] = iaLanguage::get('error_poll_options_required');
				}
			}
			$recursive = isset($_POST['recursive']) ? 1 : 0;

			if (!$error)
			{
				if ($pageAction == 'edit')
				{
					$form['id'] = $poll_id;
					$iaPolls->update($form, "`id` = '" . $poll_id . "'");
					$iaView->setMessages(iaLanguage::get('poll_updated'), iaView::SUCCESS);

					// get new values from database
					unset($form);
					$rows = $iaPolls->row(iaDb::ALL_COLUMNS_SELECTION, "`id` = " . $poll_id . ' ORDER BY `id`');
					$form['language']	= $rows['lang'];
					$form['title']		= $rows['title'];
					$form['status']		= $rows['status'];
					$form['recursive']	= $rows['recursive'];
					$form['newoptions']	= array();

					foreach($rows['options'] as $key => $val)
					{
						$form['options'][$val['id']] = $val['title'];
					}
					$date = explode(' ', $rows['date']);
					$form['start_date'] = $date[0];

					$date = explode(' ', $rows['expires']);
					$form['expire_date'] = $date[0];

				}
				else
				{
					$poll_id = $iaPolls->insert($form);
					$iaView->setMessages(iaLanguage::get('poll_added'), iaView::SUCCESS);
				}

				iaCore::util();
				if (isset($_POST['goto']))
				{
					$url = IA_ADMIN_URL . 'polls/';
					iaUtil::post_goto(array(
						'add' => $url . 'add/',
						'list' => $url,
						'stay' => $url . 'edit/' . $poll_id . '/',
					));
				}
				else
				{
					iaUtil::go_to(IA_ADMIN_URL . 'polls/edit/' . $poll_id . '/');
				}
			}
			else
			{
				$iaView->setMessages($messages);
			}
		}
		$iaView->assign('form', $form);

		$title = iaLanguage::get($pageAction . '_poll');
		$iaView->title($title);

		iaBreadcrumb::add($title, IA_SELF);

		$iaView->display('polls');
	}
	else
	{
		$iaView->grid('_IA_URL_plugins/polls/js/admin/grid');
	}
}