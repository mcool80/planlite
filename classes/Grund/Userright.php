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
 * Class Userright
 * Contains information about right for users
 * @author Markus Svensson
 * @version 1.01
 */
class Userright
{
   /** Id of unit for rights */
   var $unitid;
   /** Name of Right */
   var $rightname;
   /** Short "user-friendly" name for right */
   var $shortname;
   /** Is this right currently set */
   var $setval;

   /** Description field */
   var $description;   

  /**
   * Constructor
   *
   * @param p_unitid Id of unit
   * @param p_rightname The right
   * @param p_shortname Short name
   * @param p_is_set 1 if set else 0
   * @returns Userright object
   */
   function Userright($p_unitid, $p_rightname, $p_shortname, $p_is_set, $p_description = "")
   {
      $this->unitid = $p_unitid;
      $this->rightname = $p_rightname;
      $this->shortname = $p_shortname;
      $this->setval = $p_is_set;
	  $this->description = $p_description;
   }
}
?>