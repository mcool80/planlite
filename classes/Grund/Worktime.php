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
 
/**
 * Class Worktime
 * Contains information about worktime
 * @author Markus Svensson
 * @version 1.01
 */
class Worktime
{
   /** Id of the worktime */
   var $worktimeid;
   /** Id of Unit for this worktime */
   var $unitid;
   /** Id for User for this worktime, if null the time is for all users in unit */
   var $userid;
   /** Year  */
   var $year;
   /** Month when used week must be null or0 */
   var $month;
   /** Week, if used month and dat_in_month must be null or 0 */
   var $week;
   /** Day of given month 1-31, used only if month is not null or 0 */
   var $day_in_month;
   /** Worktime i hour */
   var $worktime;
   
   /** Description field */
   var $description;   
   
  /**
   * Constructor
   *
   * @param p_worktimeid Id of worktime
   * @param p_unitid Id of unit
   * @param p_userid Id of user
   * @param p_year Year
   * @param p_month Month 
   * @param p_week Week
   * @param p_day_in_month Day in month
   * @param p_worktime Worktime
   * @returns Worktime object 
   */
   function Worktime($p_worktimeid, $p_unitid, $p_userid, $p_year, $p_month, $p_week, $p_day_in_month, $p_worktime, $p_description = "")
   {
      $this->worktimeid = $p_worktimeid;
      $this->unitid = $p_unitid;
      $this->userid = $p_userid;
      $this->year = $p_year;
      $this->month = $p_month;
      $this->week = $p_week;
      $this->day_in_month = $p_day_in_month;
      $this->worktime = $p_worktime;
	  $this->description = $p_description;   
   }
}
?>