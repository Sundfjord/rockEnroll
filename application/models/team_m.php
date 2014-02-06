<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Team_m extends MY_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    public function get_page($id)
    {
            $this->db->select('id, teamname, sport');
            $this->db->where('id', $id);
            
            $query = $this->db->get('teams', 1);
            
            if($query->num_rows() === 1)
                
                return $query->row();
    }
    
    public function get_team_by_coach()
    {
        $this->db->select('teamname, id');
        $this->db->from('is_coach_of');
        $this->db->join('teams', 'is_coach_of.team_id = teams.id');
        $this->db->where('is_coach_of.user_id', $this->session->userdata('user_id'));
        $this->db->order_by('teamname', 'asc');
        $coachteam = $this->db->get();
        
        if($coachteam->num_rows() > 0)
        {
            foreach($coachteam->result_array() as $row)
            {
                $data[] = $row;
            }
            return $data;
        }
    }
    
    public function get_team_by_player()
    {
        $this->db->select('teamname, id');
        $this->db->from('plays_for');
        $this->db->join('teams', 'plays_for.team_id = teams.id');
        $this->db->where('plays_for.user_id', $this->session->userdata('user_id'));
        $this->db->order_by('teamname', 'asc');
        $playerteam = $this->db->get();
        
        if($playerteam->num_rows() > 0)
        {
            foreach($playerteam->result_array() as $row)
            {
                $data[] = $row;
            }
            return $data;
        }
    }
    
    public function search_team($search_term='default')
    {
        $this->db->select('teamname, id, sport');
        $this->db->from('teams');
        $this->db->like('teamname', $search_term);

        $query = $this->db->get();

        return $query->result_array();
    }
    
    public function get_teams()
    {
        // Fetch all teams in the database
        $this->db->select('teams.id AS id, 
                           teams.teamname AS teamname, 
                           teams.sport AS sport');
        $this->db->select('users.username AS username');
        $this->db->from('is_coach_of');
        $this->db->join('teams', 'is_coach_of.team_id = teams.id');
        $this->db->join('users', 'is_coach_of.user_id = users.id');
        $query = $this->db->get(); 

        $teams = array();
        foreach ($query->result_array() as $team)
        {	
                $teams[] = array(
                    'id'        =>  $team['id'],
                    'teamname'  =>  $team['teamname'],
                    'sport'     =>  $team['sport'],
                    'coach'     =>  $team['username']
                        );
        }

        // Returns teams
        return $teams;
    }
    
    public function update_team()
    {
        $data = array (
            'teamname'  => $this->input->post('teamname'),
            'sport'     => $this->input->post('sport'));
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('teams', $data);
        
        return $this->db->affected_rows() > 0;
    }
       
    public function delete_team() {
        
        $this->db->where('id', $this->uri->segment(3));
        $this->db->delete('teams');
        
        return $this->db->affected_rows() > 0;
    }
    
    public function get_squad()
    {
        $this->db->select('username, id, email');
        $this->db->from('plays_for');
        $this->db->join('users', 'plays_for.user_id = users.id');
        $this->db->where('plays_for.team_id', $this->uri->segment(2));
        $this->db->order_by('username', 'asc');
        $squad = $this->db->get();
        
        if($squad->num_rows() > 0)
        {
            foreach($squad->result_array() as $v)
            {
                $data[] = array(
                    'id'        =>  $v['id'],
                    'username'  =>  $v['username'],
                    'email'     =>  $v['email']
                    );
            }
            return $data;
        }
    }
    
    public function getTeamEvents()
    {
        $this->db->select('id, name, description, location, start_time, end_time');
        $this->db->from('events');
        $this->db->where('team_id', $this->uri->segment(2));
        $events = $this->db->get();
        
        if($events->num_rows() > 0)
        {
            foreach($events->result_array() as $v)
            {
                $start_time = substr($v['start_time'], 0, -3);
                $end_time = substr($v['end_time'], 0, -3);
                
                $data[] = array(
                    'id'            =>  $v['id'],
                    'name'          =>  $v['name'],
                    'description'   =>  $v['description'],
                    'location'      =>  $v['location'],
                    'start_time'    =>  $start_time,
                    'end_time'      =>  $end_time
                    );
            }
            return $data;
        }
    }
    
    public function get_players()
    {		
        // Fetch players from database
        $this->db->select('id, username, email');
        $this->db->from('users');
        $this->db->where('group_id', '300');
        $this->db->order_by('username asc');
        $plrs = $this->db->get(); 

        // Store in array
        $players = array();
        foreach ($plrs->result_array() as $v)
        {	
                $players[] = array(
                    'id'        =>  $v['id'],
                    'username'  =>  $v['username'],
                    'email'     =>  $v['email']
                    );
        }

        // Returns players
        return $players;
    }
    
    public function add_player($players)
    {
        $this->db->set('user_id', $players);
        $this->db->set('team_id', $this->uri->segment(3));
        $this->db->insert('plays_for');
        
        $this->db->select('id');
        $this->db->from('events');
        $this->db->where('team_id', $this->uri->segment(3));
        $eventids = $this->db->get();
        
        if ($eventids->num_rows() > 0) {
            $eventarray = array();
            foreach ($eventids->result_array() as $id) 
                {
                    $eventarray[] = $id['id'];
                }

            $eventarray['num_rows'] = count($eventarray['id']);

            $this->db->select('id');
            $this->db->from('episodes');
            $this->db->where_in('event_id', $eventarray);
            $episodes = $this->db->get();

            if ($episodes->num_rows() > 0) 
            {

            foreach ($episodes->result_array() as $row) 
                {
                    $attendanceinsert = array(
                        'user_id'       =>  $players,
                        'episode_id'    =>  $row['id'],
                        'is_attending'  =>  0
                        );

                        $this->db->insert('attendance_status', $attendanceinsert);
                }

            return $this->db->affected_rows() > 0;
            }
        }
        
    }
   
    public function remove_player($players)
    {
        $this->db->delete('plays_for', array(
            'user_id'   =>  $players,
            'team_id'   =>  $this->uri->segment(3)
        ));
        
        $this->db->select('id');
        $this->db->from('events');
        $this->db->where('team_id', $this->uri->segment(3));
        $eventids = $this->db->get();
        
        if ($eventids->num_rows() > 0) {
        
            $eventarray = array();
            foreach ($eventids->result_array() as $id) 
                {
                    $eventarray[] = $id['id'];
                }

            $this->db->select('id');
            $this->db->from('episodes');
            $this->db->where_in('event_id', $eventarray);
            $episodes = $this->db->get();

            if ($episodes->num_rows() > 0) 
            {

            foreach ($episodes->result_array() as $row) 
                {
                    $this->db->where('episode_id' , $row['id']);
                    $this->db->where('user_id', $players);
                    $this->db->delete('attendance_status');
                }

            return $this->db->affected_rows() > 0;
            }
        }
    }
    
    public function join_team($value)
    {      
        $this->db->set('user_id', $this->session->userdata('user_id'));
        $this->db->set('team_id', $value);
        $this->db->insert('plays_for');
        
        $this->db->select('id');
        $this->db->from('events');
        $this->db->where('team_id', $value);
        $eventids = $this->db->get();
        
        $eventarray = array();
        foreach ($eventids->result_array() as $id) 
            {
                $eventarray[] = $id['id'];
            }
        
        $this->db->select('id');
        $this->db->from('episodes');
        $this->db->where_in('event_id', $eventarray);
        $episodes = $this->db->get();

        if ($episodes->num_rows() > 0)

        foreach ($episodes->result_array() as $row) 
            {
                $attendanceinsert = array(
                    'user_id'       =>  $this->session->userdata('user_id'),
                    'episode_id'    =>  $row['id'],
                    'is_attending'  =>  0
                    );

                    $this->db->insert('attendance_status', $attendanceinsert);
            }

        return $this->db->affected_rows() > 0;
    }
    
    public function jsonEvents()
    {
        $this->db->select('episodes.id, team_id, event_id, 
                           event_date, name, description, location, start_time, end_time, 
                           altered_name, altered_description, altered_location, altered_start_time, altered_end_time, is_altered,
                           ');
        $this->db->order_by('event_date', 'asc');
        $this->db->join('events', 'episodes.event_id = events.id');
        $events = $this->db->get('episodes');
        
        $jsonevents = array();
        
        foreach ($events->result_array() as $e)
        {
            $nullcheck = $e['is_altered'];
            if($nullcheck === '0')
            {
                $jsonevents[] = array(
                    'id'            =>  $e['id'],
                    'event_id'      =>  $e['event_id'],
                    'team'          =>  $e['team_id'],
                    'start'         =>  $e['event_date'] . " " . $e['start_time'],
                    'date'          =>  $e['event_date'],
                    'title'         =>  $e['name'],
                    'description'   =>  $e['description'],
                    'location'      =>  $e['location'],
                    'start_time'    =>  $e['start_time'],
                    'end_time'      =>  $e['end_time']
                );  
            }
            else
            {
                 $jsonevents[] = array(
                    'id'            =>  $e['id'],
                    'event_id'      =>  $e['event_id'],
                    'team'          =>  $e['team_id'],
                    'start'         =>  $e['event_date'] . " " . $e['altered_start_time'],
                    'date'          =>  $e['event_date'],
                    'title'         =>  $e['altered_name'],
                    'description'   =>  $e['altered_description'],
                    'location'      =>  $e['altered_location'],
                    'start_time'    =>  $e['altered_start_time'],
                    'end_time'      =>  $e['altered_end_time']
                );
            }
        }
        
        return json_encode($jsonevents); 
    }
    
    public function add_event($teamid)
    {
        $eventinfo = array(
            'team_id'       =>  $teamid,
            'name'          =>  $this->input->post('eventname'),
            'description'   =>  $this->input->post('eventdesc'),
            'location'      =>  $this->input->post('eventlocation'),
            'start_time'    =>  $this->input->post('start_time'),
            'end_time'      =>  $this->input->post('end_time')
        );
        
        $this->db->insert('events', $eventinfo);
        
        return $this->db->affected_rows() > 0;
    }
    
    public function add_episodes($eventid, $result, $teamid)
    {   
        $datestring = $result->format('Y-m-d');
        $episodesinfo = array(
            'event_date'    => $datestring,
            'event_id'      => $eventid,
                );
        $this->db->insert('episodes', $episodesinfo);
        
        $episodeid = $this->db->insert_id();
        
        $this->db->select('user_id')->from('plays_for')->where('team_id',$teamid);
        $eventplayers = $this->db->get();
        
        if ($eventplayers->num_rows() > 0)
        
        foreach ($eventplayers->result_array() as $row) 
            {
                $attendanceinsert = array(
                    'user_id'       =>  $row['user_id'],
                    'episode_id'    =>  $episodeid,
                    'is_attending'  =>  0
                    );
                
                    $this->db->insert('attendance_status', $attendanceinsert);
            }
    }
    
    public function edit_event() //$date should be available
    {
        $updateinfo = array(
            'name'          =>  $this->input->post('edited_eventname'),
            'description'   =>  $this->input->post('edited_eventdesc'),
            'location'      =>  $this->input->post('edited_location'),
            'start_time'    =>  $this->input->post('edited_start_time'),
            'end_time'      =>  $this->input->post('edited_end_time') 
        );
        
        $this->db->where('id', $this->uri->segment(3));
        $this->db->update('events', $updateinfo);
        
        return $this->db->affected_rows() > 0;
    }
    
    public function delete_event($events)
    {
        $this->db->where('id', $events);
        $this->db->delete('events');
        
        return $this->db->affected_rows() > 0;
    }
    
    public function edit_episode($id, $date)
    {
        $updateinfo = array(
            'event_date'            =>  $date,
            'altered_name'          =>  $this->input->post('edited_episodeName'),
            'altered_description'   =>  $this->input->post('edited_episodeDesc'),
            'altered_location'      =>  $this->input->post('edited_episodeLocation'),
            'altered_start_time'    =>  $this->input->post('edited_episodeStartTime'),
            'altered_end_time'      =>  $this->input->post('edited_episodeEndTime'),
            'is_altered'            =>  1
        );
        
        $this->db->join('events', 'episodes.event_id = events.id');
        $this->db->where('id', $id);
        $this->db->update('episodes', $updateinfo);

        return $this->db->affected_rows() > 0;
    }
    
    public function delete_episode($id) 
    {
        $this->db->where('id', $id);
        $this->db->delete('episodes');
        
        return $this->db->affected_rows() > 0;
    }
    
    public function set_attendance($ep_id, $status)
    {
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('episode_id', $ep_id);
        $exists = $this->db->get('attendance_status');
        
        if($exists->num_rows() !== 0) 
        {
            $this->db->set('is_attending', $status);
            $this->db->where('user_id', $this->session->userdata('user_id')); 
            $this->db->where('episode_id', $ep_id); 
            
            $this->db->update('attendance_status');
        
            return $this->db->affected_rows() > 0;
        }
        else
        {
            $info = array(
                'user_id' => $this->session->userdata('user_id'),
                'episode_id' => $ep_id,
                'is_attending'  => $status
                );
            $this->db->insert('attendance_status', $info);
            
            return $this->db->affected_rows() > 0;
        }    
    }
    
    public function get_attending($epId) 
    {
        $this->db->select('username');
        $this->db->where('episode_id', $epId); 
        $this->db->where('is_attending', 1);
        $this->db->join('users', 'attendance_status.user_id = users.id');
        $this->db->join('episodes', 'attendance_status.episode_id = episodes.id');
        $attending = $this->db->get('attendance_status');
        
        return $attending;
    }
    
    public function get_not_attending($epId) 
    {
        $this->db->select('username');
        $this->db->where('episode_id', $epId); 
        $this->db->where('is_attending', 2);
        $this->db->join('users', 'attendance_status.user_id = users.id');
        $this->db->join('episodes', 'attendance_status.episode_id = episodes.id');
        $notattending = $this->db->get('attendance_status');
        
        return $notattending;
    }
    
    public function get_not_responding($epId) 
    {
        $this->db->select('username');
        $this->db->where('episode_id', $epId);
        $this->db->where('is_attending', 0);
        $this->db->join('users', 'attendance_status.user_id = users.id');
        $this->db->join('episodes', 'attendance_status.episode_id = episodes.id');
        $notresponding = $this->db->get('attendance_status');
        
        return $notresponding;
    }
    
    public function get_statistics($team_id, $startrange, $endrange)
    {
        //$this->archive_attendance(); //remove when page go live, as this method will be executed through a cron job
        
        $this->db->select('id');
        $this->db->from('events');
        $this->db->where('team_id', $team_id);
        $eventids = $this->db->get();
        
        if ($eventids->num_rows() > 0) 
        {
            $eventarray = array();
            foreach ($eventids->result_array() as $id) 
            {
                $eventarray[] = $id['id'];
            }

            $this->db->select('id, event_date');
            $this->db->from('episodes');
            $this->db->where_in('event_id', $eventarray);
            $this->db->where('event_date >=', $startrange);
            $this->db->where('event_date <=', $endrange);
            //Fetches all episodes for a specific team
            $episodes = $this->db->get();
            
            if ($episodes->num_rows() > 0) 
            {
                $epscheck = array();
                foreach ($episodes->result_array() as $eps) 
                {
                    $epscheck[] = $eps['id'];
                }
                
                $this->db->select('id');
                $this->db->from('plays_for');
                $this->db->join('users', 'plays_for.user_id = users.id');
                $this->db->where('plays_for.team_id', $team_id);
                $this->db->order_by('username', 'asc');
                $squad = $this->db->get();
                
                $squadcheck = array();
                foreach ($squad->result_array() as $squad) 
                {
                    $squadcheck[] = $squad['id'];
                }
                
                $this->db->select('user_id');
                $this->db->from('is_coach_of');
                $this->db->join('users', 'is_coach_of.user_id = users.id');
                $this->db->where('is_coach_of.team_id', $team_id);
                $this->db->order_by('username', 'asc');
                $coach = $this->db->get();
                
                $coachcheck = array();
                foreach ($coach->result_array() as $c) 
                {
                    $coachcheck[] = $c['user_id'];
                }
                
                $this->db->select('user_id, username, episode_id,count(user_id) as num_of_eps');
                $this->db->join('users', 'attendance_statistics.user_id = users.id');
                $this->db->join('episodes', 'attendance_statistics.episode_id = episodes.id');
                $this->db->where('event_date >=', $startrange);
                $this->db->where('event_date <=', $endrange);
                $this->db->group_by('user_id');
                $this->db->order_by('num_of_eps', 'desc');
                $attendance = $this->db->get('attendance_statistics');
                
                foreach ($attendance->result_array() as $att)
                {
                    if (in_array($att['user_id'], $squadcheck) || in_array($att['user_id'], $coachcheck) && in_array($att['episode_id'], $epscheck))
                    {
                        $statArray[] = array(
                            'user_id'   =>  $att['user_id'],
                            'username'  =>  $att['username'],
                            'count'     =>  $att['num_of_eps']
                        );
                    }
                }
                return $statArray;
                    
            } 
        }
        
    }
    
    public function archive_attendance() 
    {
        $this->db->select('id, event_date');
        $this->db->from('episodes');
        $this->db->order_by('event_date', 'asc');
        $episodes = $this->db->get();
        
        if ($episodes->num_rows() > 0) 
        {
            foreach ($episodes->result_array() as $row) 
            {
                //checks if the episode is more than 24 hours (1day) old relative to current date
                $now = date('Y-m-d');
                $eventdate = date('Y-m-d', strtotime($row['event_date']));
                $validstat = date('Y-m-d', strtotime($eventdate. ' + 1 day'));
                
                if ($now > $validstat) 
                {   
                    $this->db->select('user_id, episode_id');
                    $this->db->where('is_attending', 1);
                    $this->db->where('episode_id', $row['id']);
                    //$stats is rows where user id attended episode with episode id and should be added to statistics
                    $stats = $this->db->get('attendance_status');
                    
                    foreach ($stats->result_array() as $stat) 
                    {
                        $statinfo = array(
                            'user_id'       =>  $stat['user_id'],
                            'episode_id'    =>  $stat['episode_id']
                            );
                        $this->db->on_duplicate('attendance_statistics', $statinfo);
                    }
                    
                }     
                
                return $this->db->affected_rows();
            }
        }
        else 
        { 
            continue; 
        }
    
    }
    
}