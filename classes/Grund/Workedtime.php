<?php
/*
 *  Planlite - Online planning program in php 
 *  Copyright (C) 2008  Markus Svensson, CelIT
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
include_once('Usertime.php');

/**
 * Class Workedtime
 * Contains information about workedtime
 * @author Markus Svensson
 * @version 1.00
 */
class Workedtime extends Usertime
{
   /** Id of workedtime */
   var $workedtimeid;
   /** Workedtime */
   var $workedtime;

  /**
   * Constructor
   *
   * @param p_workedtimeid Id of workedtime
   * @param p_activityslotid Id of activityslot with workedtime
   * @param p_userid Id of user
   * @param p_startdate Startdate
   * @param p_workedtime Worked time
   * @returns Workedtime object
   */
   function Workedtime($p_workedtimeid, $p_activityslotid, $p_userid, $p_startdate, $p_workedtime)
   {
      $this->workedtimeid = $p_workedtimeid;   
      $this->activityslotid = $p_activityslotid;   
      $this->userid = $p_userid;
      $this->startdate = $p_startdate;      
      $this->workedtime = $p_workedtime;   
   }
}
?>