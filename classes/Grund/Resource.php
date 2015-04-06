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
 * Class Resource
 * Contains information about resource
 * @author Markus Svensson
 * @version 1.01
 */
class Resource
{
   /** Id of resource */
   var $resourceid;
   /** Id of the unit */
   var $unitid;
   /** Name of resource */
   var $resourcename;
   
   /** Description field */
   var $description;
   
  /**
   * Constructor
   *
   * @param p_resourceid Id of resource
   * @param p_unitid Id of unit where resource exists
   * @param p_resurcename Name of resource
   * @returns Resource object
   */
   function Resource($p_resourceid, $p_unitid, $p_resurcename, $p_description = "")
   {
      $this->resourceid = $p_resourceid;
      $this->unitid = $p_unitid;
      $this->resourcename = $p_resurcename;
	  $this->description = $p_description;
   }
   
  /**
   * Get plannedtime for Resource
   *
   * @returns Array of Activitytime
   */
   function getPlannedtime()
   {
      global $dc;
      return $dc->getResourceplannedtimeInUser("'$this->userid'");
   }
}
?>