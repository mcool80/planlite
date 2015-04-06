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
include_once('GrundControl.php');
include_once('DatePlanlite.php');
/**
 * Class WorktimeControl
 * Controls the worktime
 * updates the worktime for user and unit
 * @author Markus Svensson
 * @version 1.00
 */
class WorktimeControl extends GrundControl {
   /** Local errormesage */
   var $errormsg;
   /** User to be displayed */
   var $displayeduser;
   /** Local message */
   var $message;
   /** Edit work time, if 1 */
   var $editworktime;
   /** Parentunits */
   var $parentunits = array();
   /** Worktimes */
   var $worktimes = array();
   /** Subunits to shown unit */
   var $subunits = array();
   /** Subusers to shown unit */
   var $subusers = array();
   /** nextdate */
   var $nextlink;
   /** prevdate */
   var $prevlink;   
   /** Editable, tru if values are editable */
   var $editable;

   var $timeunit;
   var $year;
   var $month;   
   var $months = array();
   var $weeks = array();
   var $days = array();

   /** Weeksdays */
   var $weekdays = array( 0 => 'Sö', 1 => 'Må', 2 => 'Ti', 3 => 'On', 4 => 'To', 5 => 'Fr', 6 => 'Lö');
   var $monthnames = array( 1 => "jan", 2 => "feb", 3 => "mars", 4 => "april", 5 => "maj", 6 => "juni",
                      7 => "juli", 8 => "aug", 9 => "sept", 10 => "okt", 11 => "nov", 12 => "dec");
                     

  /**
   * Constructor
   */
   function WorktimeControl()
   {
      $this->currentpagename = 'Worktime';
      $this->addTemplates(array("Grund/worktimepage.tpl"), "Grund/grundhead.tpl", "Grund/grundtail.tpl");
   }
   
  /**
   * Execute a requested action
   * There are thw following actions:
   *   - showworktime Display worktime for user and unit for given time (indata startdate, stopdate, unitid and userid)
   *   - editworktimepage  Edit worktime for user or unit (indata startdate, stopdate and userid/unitid)
   *   - editworktime Updates worktime for user (indata userid, unitid, worktimeid, year, month, week, worktime)
   * @param action - requested action
   */
   function execAction($action)
   {
      global $dc;
      $this->checkLoggedinuser();
      switch ( $action )
      {
         case 'editworktimepage':
            $err = $this->editWorktime($_GET['userid'], $_GET['unitid'], $_GET['startdate']);
            break;
         case 'editworktime':
            $err = $this->addWorktime($_GET['worktimeid'], $_GET['unitid'], $_GET['userid'], $_GET['year'], 
                                      $_GET['month'], $_GET['week'], $_GET['day_in_month'], $_GET['worktime']);
            if ( $err )
               $this->displayError($err, "");
            $this->editWorktime($_GET['userid'], $_GET['unitid'], $_GET['startdate']);   
            header("Location: sida.php?page=worktime&action=editworktimepage&userid=".$_GET['userid']."&unitid=".$_GET['unitid']."&year=".$_GET['year']."&month=".$_GET['month']);
            break;
      }
   }
   
  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();
      $this->displayUser($smarty, $this->loggedinuser);

      $this->displayUser($smarty, $this->user, true, "worktime");
      $this->displayUnit($smarty, $this->unit, true);
      
      $this->displayUnits($smarty, $this->parentunits, 'parentunits'); 
      $this->displayUnits($smarty, $this->subunits, 'subunits'); 
      $this->displayUnits($smarty, $this->subusers, 'subusers'); 
      $smarty->assign('sizesubobjects', sizeof($this->subunits)+sizeof($this->subusers));

      $this->displayUnits($smarty, $this->worktimes, 'worktimes'); 

      $this->displayErrorText($smarty);      

      $smarty->assign('months', $this->months);
      $smarty->assign('weeks', $this->weeks);      
      $smarty->assign('days', $this->days);            
      $smarty->assign('timeunit', $this->timeunit);
      $smarty->assign('year', $this->year);
      $smarty->assign('month', $this->month);      
      $smarty->assign('monthname', $this->monthnames[$this->month]);      

      $smarty->assign('prevlink', $this->prevlink);
      $smarty->assign('nextlink',  $this->nextlink);      

      $smarty->assign('showmodule', '0');
      $smarty->assign('message', $this->message);

      $smarty->assign('editable', $this->editable);

      $this->displayHeader();
      if ( $this->editworktime == '1' )
         $this->displayContent($smarty); 
      $this->displayTail();         
   }
  /**
   * Show edit worktime page
   * @param p_userid Id of user
   * @param p_unitid Id of unit
   * @param p_startdate Startdate for worktimes
   * @param p_usecache Not used
   * @returns 0 if all ok, else errorcode
   */
   function editWorktime($p_userid, $p_unitid, $p_startdate, $p_usecache=false)
   {
      global $dc;
      if ( $this->loggedinuser->isAdmin($p_unitid) ||
           ( $this->loggedinuser->userid == $p_userid && 
             $this->loggedinuser->editdata == '1' ) )
         $this->editable = '1';
      else
         $this->editable = '0';
      if ( strlen($p_userid) > 0 )
      {
         $user = array_pop($dc->getUser("'".$p_userid."'"));
         if ( $this->loggedinuser->isAdmin($user->headunit->unitid) )
            $this->editable = '1';
      }
      if ( strlen($p_unitid) > 0 )      
         $unit = array_pop($dc->getUnit("'".$p_unitid."'"));

      /* Get input */
      $this->year = $_GET['year'];
      $this->month = $_GET['month'];
      if ( $this->year == '' )
         $this->year = date("Y");
      
      if ( $this->month == '' )
         $this->month = date("n");      

      if ( is_object($user) )
      {
         $timeunit = $user->headunit->planningtype->timeunit;
         $unit = $user->headunit;
         $this->user = $user;
         $worktimesobj = $user;         
         $this->parentunits = $this->getParentUnits($this->user->headunit);         
         array_push($this->parentunits, $this->user->headunit);
      }
      elseif ( is_object($unit) )
      {
         $timeunit = $unit->planningtype->timeunit;      
         $this->unit = $unit;
         $worktimesobj = $unit;
         $this->parentunits = $this->getParentUnits($this->unit);
         $this->subunits = $this->unit->getSubunits();
         $this->subusers = $this->unit->getUsers();
      }
      else
      {
          $ec = new ErrorControl(10063, "Användare eller enhet saknas");
          $ec->display();
          exit(0);
      }      

      $startweek = 0;
      $stopweek = 0;
      $startday = 0;
      $stopday = 0;
      $startmonth = $this->month;
      $stopmonth = $this->month;
      if ( $timeunit == 168 )
      {
         $this->timeunit = 'week';
         $startweek = 1;
         $stopweek = 53;
      } 
      elseif ( $timeunit < 168 )
      {
         $this->timeunit = 'day';      
         $startday = 1;
         $stopday = 31;
      } 
      else 
      {
         $this->timeunit = 'month';
         $startmonth = 1;
         $stopmonth = 12;
      }
      
      $this->worktimes = $worktimesobj->getWorktime($this->year, $this->year, $startmonth, $stopmonth,
                              $startweek, $stopweek, $startday, $stopday);

      if ( $this->timeunit == 'month' )
      {
         for ( $i = 0; $i < 12; $i++ )
         {
            $this->months[$i+1] = array('month' => $i+1, 'monthname' => $this->monthnames[$i+1], 'colspan' => 1, 'worktime' => 0);
         }
         foreach ( $this->worktimes as $wt )
         {
            $month = $this->months[$wt->month];
            foreach ( get_object_vars($wt) as $key => $val )
               $month[$key] = $val;
            $this->months[$wt->month] = $month;
         }
         $this->nextlink = "sida.php?page=worktime&action=editworktimepage&unitid=".$_GET['unitid']."&userid=".$_GET['userid']."&year=".($this->year+1);
         $this->prevlink = "sida.php?page=worktime&action=editworktimepage&unitid=".$_GET['unitid']."&userid=".$_GET['userid']."&year=".($this->year-1);         
      } 
      elseif ( $this->timeunit == 'week' )
      {
         $date = new DatePlanlite("$year-12-31");
//         $maxweek = $date->getWeek();
         /* TODO: Better solution please */
//         if ( $maxweek == 1 )
//            $maxweek = 52;
         $maxweek = 53;
         $this->weeks = array();
         foreach ( $this->worktimes as $wt )
         {
            $this->weeks[$wt->week] = get_object_vars($wt);
         }
         for ( $i = 1; $i <= $maxweek; $i++ )
         {
            if ( is_null($this->weeks[$i]) )
               $this->weeks[$i] = get_object_vars(new Worktime(null, $p_unitid, $p_userid, $this->year, null, $i, null, 0));  
         }
         ksort($this->weeks);                           
         $this->nextlink = "sida.php?page=worktime&action=editworktimepage&unitid=".$_GET['unitid']."&userid=".$_GET['userid']."&year=".($this->year+1);
         $this->prevlink = "sida.php?page=worktime&action=editworktimepage&unitid=".$_GET['unitid']."&userid=".$_GET['userid']."&year=".($this->year-1);         
      } 
      elseif ( $this->timeunit == 'day' )
      {
         $month = $this->month;
         if ( strlen($this->month) < 2 )
            $month = "0".$this->month;
         $date = new DatePlanlite($this->year."-$month-01");
         $weekday = $date->getWeekday();

         $maxday = $date->days_in_month[$this->month];
         $days = array();

         for ( $u = 1; $u <= ($weekday+6)%7; $u++ )
            $days[$u-7] = array('day' => '', 'colspan' => '1', 'weekday' => $this->weekdays[$u]);
         for ( $i = 0; $i < $maxday; $i++ )
            $days[$i+1] = array('day' => $i+1, 'colspan' => '1', 'weekday' => $this->weekdays[($i+$weekday)%7], 
                                'worktime' => 0, 'month' => $this->month, 'day_in_month' => $i+1);         

         foreach ( $this->worktimes as $wt )
         {
            $day = $days[$wt->day_in_month];
            foreach ( get_object_vars($wt) as $key => $val )
               $day[$key] = $val;
            $days[$wt->day_in_month] = $day;
         }

         $this->days = $days;
         $nextmonth = $this->month + 1;
         $prevmonth = $this->month - 1;         
         $prevyear = 0;
         $nextyear = 0;
         if ( $this->month+1 > 12 )
         { 
            $nextyear = 1;
            $nextmonth = 1;
         }
         elseif ( $this->month-1 < 1 ) 
         {
            $prevyear = -1;
            $prevmonth = 12;
         }

         $this->nextlink = "sida.php?page=worktime&action=editworktimepage&unitid=".$_GET['unitid']."&userid=".$_GET['userid']."&year=".($this->year+$nextyear)."&month=".$nextmonth;
         $this->prevlink = "sida.php?page=worktime&action=editworktimepage&unitid=".$_GET['unitid']."&userid=".$_GET['userid']."&year=".($this->year+$prevyear)."&month=".$prevmonth;
      }
      $this->editworktime = '1';
      $this->currentsubpagename = 'editworktime';
      return 0;
   }
   
  /**
   * Add or edit a worktime
   * @param p_worktimeid Id of worktime
   * @param p_unitid Id of unit
   * @param p_userid Id of user
   * @param p_year Year
   * @param p_month Month
   * @param p_week Week
   * @param p_day_in_month Day in month
   * @param p_worktime Worktime
   * @returns 0 of all ok, else errorcode
   */
   function addWorktime($p_worktimeid, $p_unitid, $p_userid, $p_year, $p_month, $p_week, $p_day_in_month, $p_worktime)
   {
      global $dc;
      if ( !( $this->loggedinuser->isAdmin($p_unitid) ||
            ( $this->loggedinuser->userid == $p_userid && 
              $this->loggedinuser->editdata == '1' ) ) )
      {
         return 10074;
      }
      if ( $p_worktimeid == '' )
      {
         $worktime = new Worktime(null, $p_unitid, $p_userid, $p_year, 
                                  $p_month, $p_week, $p_day_in_month, $p_worktime);
         $err = $dc->updateWorktime($worktime, false);
         if ( $err )
         {
            return $err;
         }
      }
      else
      {
         $worktime = array_pop($dc->getWorktime($p_worktimeid));
         $worktime->worktime = $p_worktime;
         $err = $dc->updateWorktime($worktime);
         if ( $err )
            return $err;
      }
      if ( is_object($worktime) )
      {               
         $key = $worktime->year."-";
         if ( strlen($worktime->month) < 2 )
            $key .= "0";
         $key .= $worktime->month."-";
         if ( strlen($worktime->week) < 2 )
            $key .= "0";
         $key .= $worktime->week."-";                  
         if ( strlen($worktime->day_in_month) < 2 )
            $key .= "0";                     
         $key .= $worktime->day_in_month;
         $this->worktimes[$key] = $worktime;
      }
      return 0;
   }   
}
?>