<?php if (!defined('NEOFRAG_CMS')) exit;
/**************************************************************************
Copyright © 2015 Michaël BILCOT & Jérémy VALENTIN

This file is part of NeoFrag.

NeoFrag is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

NeoFrag is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with NeoFrag. If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/

class w_forum_m_forum extends Model
{
	public function get_last_messages()
	{
		$forums = $this->_get_forum();
		
		return $this->db->select('m.message_id', 'm.topic_id', 'm.message', 'm.date', 't.title as topic_title', 'm.user_id', 'u.username', 'up.avatar', 'up.sex')
						->from('nf_forum_messages m')
						->join('nf_forum_topics t',    'm.topic_id = t.topic_id')
						->join('nf_users u',           'u.user_id  = m.user_id')
						->join('nf_users_profiles up', 'up.user_id = m.user_id')
						->where('t.forum_id', $forums)
						->order_by('m.date DESC')
						->limit(3)
						->get();
	}

	public function get_last_topics()
	{
		$forums = $this->_get_forum();
		
		return $this->db->select('t.topic_id', 't.title', 'm.message_id', 'm.user_id', 'm.date', 'u.username', 'up.avatar', 'up.sex', 't.count_messages')
						->from('nf_forum_messages m')
						->join('nf_forum_topics t',    'm.topic_id = t.topic_id')
						->join('nf_users u',           'u.user_id     = m.user_id')
						->join('nf_users_profiles up', 'up.user_id    = m.user_id')
						->where('t.forum_id', $forums)
						->group_by('t.topic_id')
						->order_by('m.date DESC')
						->limit(3)
						->get();
	}
	
	public function _get_forum()
	{
		$categories = array();
		
		foreach ($this->db->select('category_id')->from('nf_forum_categories')->get() as $category_id)
		{
			if (is_authorized('forum', 'category_read', $category_id))
			{
				$categories[] = $category_id;
			}
		}
		
		return $this->db->select('forum_id')->from('nf_forum')->where('is_subforum', FALSE)->where('parent_id', $categories)->get();
	}
}

/*
NeoFrag Alpha 0.1
./widgets/forum/models/forum.php
*/