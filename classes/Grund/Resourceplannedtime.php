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
 
include_once('Activitytime.php');

/**
 * Class Resourceplannedtime
 * Contains information about plannedtime
 * @author Markus Svensson
 * @version 1.01
 */
class Resourceplannedtime extends Activitytime
{
   /** Id of resource */
   var $resourceid;
   /** Id of the plannedtime */
   var $resourceplannedtimeid;

   /** Description field */
   var $description;
      
  /**
   * Constructor
   *
   * @param p_resourceplannedtimeid Id of resource planned time
   * @param p_activityslotid Id of activity slot where planned time exists
   * @param p_resourceid Id of resource
   * @param p_plannedtime Planned time
   * @param p_unitsymbol Unit symbol for the planned time
   * @returns 
   */
   function Resourceplannedtime($p_resourceplannedtimeid, $p_activityslotid, $p_resourceid, $p_plannedtime,
                                $p_unitsymbol, $p_description = "")
   {
      $this->resourceplannedtimeid = $p_resourceplannedtimeid;   
      $this->activityslotid = $p_activityslotid;   
      $this->resourceid = $p_resourceid;
      $this->plannedtime = $p_plannedtime;
      $this->unitsymbol = $p_unitsymbol;
	  $this->description = $p_description;
   }
}
?>