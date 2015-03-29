<nav id="nav-menu" class="navbar navbar-default" role="navigation">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo base_url(); ?>">rockEnroll</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <?php if ( $playercoach === true) :
                $playercoachteam = array_merge($playerteam, $coachteam);
                foreach($playercoachteam as $k => $v)
                        {
                            foreach($playercoachteam as $key => $value)
                            {
                                if($k != $key && $v['team_id'] == $value['team_id'])
                                {
                                     unset($playercoachteam[$k]);
                                }
                            }
                        }
                if (empty($playercoachteam)) {
                    echo '<li class="team"><a id="home_create_or_join_team" href="#team_modal"><span class="glyphicon glyphicon-plus"></span>Create or join a team</a></li>';
                } else {
                    foreach ($playercoachteam as $row) :
                        echo '<li class="team"><a href="'.base_url().'index.php/team/' . $row['team_id'] . '">' . $row['teamname'] . '</a></li>';?>
                    <?php endforeach;
                    if(count($playercoachteam) <= 2)
                    {
                        echo '<li class="team"><a id="home_create_or_join_team" href="#team_modal"><span class="glyphicon glyphicon-plus"></span>Create or join a team</a></li>';
                    }
                }
            elseif ( $coach === TRUE ) :
                if (empty($coachteam)) {
                    echo
                    '<li class="team"><a id="home_create_team" href="#create_team_modal"><span class="glyphicon glyphicon-plus"></span>Create a team</a></li>';
                } else {
                    foreach ($coachteam as $row):
                        echo
                        '<li class="team"><a href="'.base_url().'index.php/team/' . $row['team_id'] . '">' . $row['teamname'] . '</a></li>';
                    endforeach;
                    if(count($coachteam) <= 2)
                    {
                        echo '<li class="team"><a id="home_create_team" href="#create_team_modal"><span class="glyphicon glyphicon-plus"></span>Create a team</a></li>';
                    }
                }
            else :
                if (empty($playerteam)) {
                    echo
                    '<li class="team"><a id="home_join_team" href="#join_team_modal"><span class="glyphicon glyphicon-plus"></span>Join a team</a></li>';
                } else {
                foreach ($playerteam as $row):
                    echo
                    '<li class="team"><a href="'.base_url().'index.php/team/' . $row['team_id'] . '">' . $row['teamname'] . '</a></li>';
                endforeach;
                if(count($playerteam) <= 2)
                {
                    echo '<li class="team"><a id="home_join_team" href="#join_team_modal"><span class="glyphicon glyphicon-plus"></span>Join a team</a></li>';
                }
            } ?>
            <?php endif; ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
                <li class="no-bgc"><a class="no-bg glyphicon glyphicon-bell" href="#" id="notifications">&nbsp;<span class="badge pull-right"></span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $userdata->username; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo base_url(); ?>index.php/profile"><span class="glyphicon glyphicon-user"></span>My Profile</a></li>
                        <li><a href="<?php echo base_url(); ?>index.php/auth/logout"><span class="glyphicon glyphicon-log-out"></span>Logout</a></li>
                    </ul>
                </li>
        </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div id="notificationsFlyOut">
    <div class="beeperHeader">
        <div class="beeperWrapper">
            <div class="beeper"></div>
        </div>
    </div>
    <div class="notificationsHeader">
        <h4>Notifications</h4>
    </div>
    <div class="notifications">

    </div>
    <div class="allNotifications">
        <a href="<?php echo base_url(); ?>index.php/notification/allNotifications" title="See all notifications">See all</a>
    </div>
</div>

<div id="team_modals">
<?php if ($playercoach) { ?>
<div class="modal fade" id="team_modal" keyboard="true" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Create or join team</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#join" data-toggle="tab">Join a team</a></li>
                    <li><a href="#create" data-toggle="tab">Create a team</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="join">
                        <table id="join_team_table" class="table">
                            <thead>
                                <tr class="tabellheader">
                                    <th class="left" scope="col"><input rel="popover" class="check_all" id="selectall" type="hidden" /></th>
                                    <th class="middle_l" scope="col">Team Name</th>
                                    <th class="middle_r" scope="col">Sport</th>
                                    <th class="right" scope="col">Coach</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $playerteamlist = array();
                            if (empty($playerteam))
                            {}
                            else
                            {
                                foreach ($playerteam as $pt)
                                {
                                    $playerteamlist[] = $pt['team_id'];
                                }
                            } ?>
                            <?php foreach ($teams as $team):?>
                                <tr>
                                    <td class="left"><input form="team" type="checkbox" name="team[]" id="teamids" value="<?php echo $team['id'];?>" <?php if(in_array($team['id'], $playerteamlist)) {echo "disabled='disabled'";}?> > </td>
                                    <td class="middle_l"><div class="teamname"><?php echo $team['teamname'];?></div></td>
                                    <td class="middle_r"><div class="sport"><?php echo $team['sport'];?></div></td>
                                    <td class="middle_r"><div class="sport"><?php echo $team['coach'];?></div></td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                        <span class='help-inline' id='errorinline_join'><p class="danger"></p></span>
                        <form id="team" action="" method="post">
                            <button class="btn btn-info" id="jointeamsubmit" type="button" disabled>Join team</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="create">
                        <form id="input_form" action="" method="POST">
                            <div class="form-group" id="error_createteam">
                                <label class='control-label' for="teamname">Team Name:</label>
                                <input type="text" class="form-control" id="create_teamname" value="<?php echo set_value('teamname'); ?>" name="teamname" placeholder="Team name" autofocus />
                                <span class='help-inline' id='errorinline_createteam'><p class="danger"></p></span>
                            </div>
                            <div class="form-group" id="error2_createteam">
                                <label class='control-label' for="sport">Sport:</label>
                                <select class="form-control" name="sport" id="create_sport" value="">
                                    <option value="0" name="choose" selected>Choose sport</option>
                                    <option value="Football" name="Football">Football</option>
                                    <option value="Badminton" name="Badminton">Badminton</option>
                                    <option value="Bandy" name="Bandy">Bandy</option>
                                    <option value="Baseball" name="Baseball">Baseball</option>
                                    <option value="Basketball" name="Basketball">Basketball</option>
                                    <option value="Biathlon" name="Biathlon">Biathlon</option>
                                    <option value="Cross-country" name="Cross-country">Cross-country</option>
                                    <option value="Cycling" name="Cycling">Cycling</option>
                                    <option value="Field hockey" name="Field hockey">Field hockey</option>
                                    <option value="Handball" name="Handball">Handball</option>
                                    <option value="Ice hockey" name="Ice hockey">Ice hockey</option>
                                    <option value="Lacrosse" name="Lacrosse">Lacrosse</option>
                                    <option value="Rugby" name="Rugby">Rugby</option>
                                    <option value="Track and field" name="Track and field">Track and field</option>
                                    <option value="Volleyball" name="Volleyball">Volleyball</option>
                                    <option value="Water polo" name="Water polo">Water polo</option>
                                </select>
                                <span class='help-inline' id='error2inline_createteam'><p class="danger"></p></span>
                            </div>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="createteamsubmit" class="btn btn-info">Create team</button>
                        </form>
                    </div><!-- /.tab-content -->
                </div><!-- /.modal-content -->
            </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php }

if ($coach) { ?>

<div class="modal fade" id="create_team_modal" keyboard="true" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Create team</h4>
            </div>
            <div class="modal-body">
                <form id="input_form" action="" method="POST">
                    <div class="form-group" id="error_createteam">
                        <label class='control-label' for="teamname">Team Name:</label>
                        <input type="text" class="form-control" id="create_teamname" value="<?php echo set_value('teamname'); ?>" name="teamname" placeholder="Team name" autofocus />
                        <span class='help-inline' id='errorinline_createteam'><p class="danger"></p></span>
                    </div>
                    <div class="form-group" id="error2_createteam">
                        <label class='control-label' for="sport">Sport:</label>
                        <select class="form-control" name="sport" id="create_sport" value="">
                            <option value="0" name="choose" selected>Choose sport</option>
                            <option value="Football" name="Football">Football</option>
                            <option value="Badminton" name="Badminton">Badminton</option>
                            <option value="Bandy" name="Bandy">Bandy</option>
                            <option value="Baseball" name="Baseball">Baseball</option>
                            <option value="Basketball" name="Basketball">Basketball</option>
                            <option value="Biathlon" name="Biathlon">Biathlon</option>
                            <option value="Cross-country" name="Cross-country">Cross-country</option>
                            <option value="Cycling" name="Cycling">Cycling</option>
                            <option value="Field hockey" name="Field hockey">Field hockey</option>
                            <option value="Handball" name="Handball">Handball</option>
                            <option value="Ice hockey" name="Ice hockey">Ice hockey</option>
                            <option value="Lacrosse" name="Lacrosse">Lacrosse</option>
                            <option value="Rugby" name="Rugby">Rugby</option>
                            <option value="Track and field" name="Track and field">Track and field</option>
                            <option value="Volleyball" name="Volleyball">Volleyball</option>
                            <option value="Water polo" name="Water polo">Water polo</option>
                        </select>
                        <span class='help-inline' id='error2inline_createteam'><p class="danger"></p></span>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="createteamsubmit" class="btn btn-info">Create team</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php }

if (!$playercoach && !$coach) { ?>

<div class="modal fade" id="join_team_modal" keyboard="true" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Join team</h4>
            </div>
            <div class="modal-body">
                <table id="join_team_table" class="table">
                    <thead>
                        <tr class="tabellheader">
                            <th class="left" scope="col"><input rel="popover" class="check_all" id="selectall" type="hidden" />
                            <th class="middle_l" scope="col">Team Name</th>
                            <th class="middle_r" scope="col">Sport</th>
                            <th class="right" scope="col">Coach</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $playerteamlist = array();
                    if (empty($playerteam))
                    {}
                    else
                    {
                        foreach ($playerteam as $pt)
                        {
                            $playerteamlist[] = $pt['team_id'];
                        }
                    } ?>
                    <?php foreach ($teams as $team):?>
                        <tr>
                            <td class="left"><input form="team" type="checkbox" name="team[]" id="teamids" value="<?php echo $team['id'];?>" <?php if(in_array($team['id'], $playerteamlist)) {echo "disabled='disabled'";}?> > </td>
                            <td class="middle_l"><div class="teamname"><?php echo $team['teamname'];?></div></td>
                            <td class="middle_r"><div class="sport"><?php echo $team['sport'];?></div></td>
                            <td class="middle_r"><div class="sport"><?php echo $team['coach'];?></div></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <span class='help-inline' id='errorinline_join'><p class="danger"></p></span>
            </div>
            <div class="modal-footer">
                <form id="team" action="" method="post">
                    <button class="btn btn-info" id="jointeamsubmit" type="button" disabled>Join team</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>
</div>