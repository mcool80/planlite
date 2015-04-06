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
 * Class Planningtype
 * Contains information about planningtype
 * @author Markus Svensson
 * @version 1.01
 */
class Planningtype
{
   /** Id of planningtype */
   var $planningtypeid;
   /** Planningtypename */
   var $planningtypename;
   /** Timeunit for planning, smallest planningsize.
       in hours, 720 hours means month (28-31 days) */
   var $timeunit;
   
   /** Description field */
   var $description;
   
  /**
   * Constructor
   *
   * @param p_planningtypeid id of planningtype
   * @param p_planningtypename Name of the planning type
   * @param p_timeunit Smallest time unit for the planning type
   * @returns Planningtype object
   */
   function Planningtype($p_planningtypeid, $p_planningtypename, $p_timeunit, $p_description = "")
   {
      $this->planningtypeid = $p_planningtypeid;
      $this->planningtypename = $p_planningtypename;
      $this->timeunit = $p_timeunit;
	  $this->description = $p_description;
   }
}
?>