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
 * Class Entity
 * Contains information about unitsymbol
 * @author Markus Svensson
 * @version 1.00
 */
class Entity
{
   /** Symbol for the unit */
   var $unitsymbol;

   /** Unit symbol name */
   var $name;

   /** Description */
   var $description;

  /**
   * Creates a entity object 
   *
   * @param p_unitsymbol The symbol for example %
   * @param p_name Name of the symbol
   * @param p_description Description
   * @returns Entity object
   */
   function Entity($p_unitsymbol, $p_name, $p_description)
   {
      $this->unitsymbol = $p_unitsymbol;
      $this->name = $p_name;
      $this->description = $p_description;
   }
}
?>