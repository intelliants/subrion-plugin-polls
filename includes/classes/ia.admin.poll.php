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
 * @package Subrion\Plugin\PersonalBlog\Admin
 * @link http://www.subrion.org/
 * @author https://intelliants.com/ <support@subrion.org>
 * @license http://www.subrion.org/license.html
 *
 ******************************************************************************/

class iaPoll extends abstractModuleAdmin
{
    protected static $_table = 'polls';
    protected $_tableOptions = 'poll_options';
    protected $_tableClicks = 'poll_clicks';

    protected $_itemName = 'poll';

    protected $_activityLog = ['item' => 'poll'];

    protected $_statuses = [iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE];

    public $dashboardStatistics = ['icon' => 'folder', 'url' => 'polls/'];

    public function getOptions($id)
    {
        return $this->iaDb->keyvalue(array('id', 'title'), iaDb::convertIds($id, 'poll_id') . ' ORDER BY `id`', $this->_tableOptions);
    }

    public function delete($id)
    {
        $this->iaDb->delete(iaDb::convertIds($id), self::getTable());
        $this->iaDb->delete(iaDb::convertIds($id, 'poll_id'), $this->_tableOptions);
        $this->iaDb->delete(iaDb::convertIds($id, 'poll_id'), $this->_tableClicks);

        return true;
    }
}
