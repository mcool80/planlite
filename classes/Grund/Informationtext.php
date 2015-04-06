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
 * Class Informationtext
 * Contains information about informationtext
 * @author Markus Svensson
 * @version 1.01
 */
class Informationtext
{
   /** Id of informationtext in database */
   var $informationtextid;
   /** Id of unit which informationtext exist in */
   var $unitid;
   /** Information text */
   var $informationtext;
   /** Date on start of showing text */
   var $startdate;
   /** Date on sttop showing text */
   var $stopdate;
   
   /** Description field */
   var $description;   
   
  /**
   * Construct
   *
   * @param p_informationtextid Id of informationtext
   * @param p_unitid Id of unit informationtext exists for
   * @param p_informationtext Informationtext
   * @param p_startdate Start date when information text should be shown
   * @param p_stopdate Stop date when information text should be shown
   * @returns 
   */
   function Informationtext($p_informationtextid, $p_unitid, $p_informationtext, $p_startdate, $p_stopdate, $p_description = "")
   {
      $this->informationtextid = $p_informationtextid ;
      $this->unitid = $p_unitid ;
      $this->informationtext = $p_informationtext;
      $this->startdate = $p_startdate;
      $this->stopdate = $p_stopdate; 
	  $this->description = $p_description;  
   }
   
  /**
   * Checks wheater the information is shown currently
   *
   * @returns True if the information is to be shown currently else false
   */
   function showinformation()
   {
      if ( strcmp($this->startdate, date('Y-m-d H:i:s')) <= 0 && 
           strcmp($this->stopdate, date('Y-m-d H:i:s')) >= 0 )
         return true;
      return false;
   }
}
?>