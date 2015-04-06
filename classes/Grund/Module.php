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
 
include_once('Modulefile.php');

/**
 * Class Module
 * Contains information about module
 * @author Markus Svensson
 * @version 1.00
 */
class Module
{
   /** Id of the module in database */
   var $moduleid;
   /** Modulename */
   var $modulename;
   /** Supplier of module */
   var $supplier;
   /** Version if the installed module */
   var $version;
   /** Description of the module */
   var $description;
   /** Default pagename when using this module */
   var $defaultpagename;
   
   /** Module files */
   var $modulefile = array();

   /**
    * Constructor
    * Gets modulefiles from database
    *
    * @param p_moduleid Id of the module
    * @param p_modulename Name of the module
    * @param p_supplier Supplier of the module
    * @param p_version Version of the module
    * @param p_description Description about the module
    * @param p_defaultpagename Default page link for the module
    * @returns Module object
    */
    function Module($p_moduleid, $p_modulename, $p_supplier, $p_version, $p_description, $p_defaultpagename)
   {
      $this->moduleid = $p_moduleid;
      $this->modulename = $p_modulename;
      $this->supplier = $p_supplier;
      $this->version = $p_version;
      $this->description = $p_description;   
      $this->defaultpagename = $p_defaultpagename;
      /* Get modulefiles in this module*/
      $this->getModulefiles();
   }   

  /**
   * Adds a modulefile to module
   *
   * @param p_modulefile Modulefile object
   * @returns 0 if all ok else a errorcode
   */
   function addModulefile($p_modulefile)
   {
      $this->modulefile[$p_modulefile->filename] = $p_modulefile;
   }
   
  /**
   * Removes a modulefile from module
   *
   * @param p_filename Filename of the modulefile
   * @returns 0 if all ok else a errorcode
   */
   function removeModulefile($p_filename)
   {
      $newarr = array();
      foreach ( $this->modulefile as $modulefile )
         if ( $modulefile->filename != $p_filename )
            $newarr[$modulefile->filename] = $modulefile;
      $this->modulefile = $newarr;
      return 0;   
   }
   
  /**
   * Get modules file for this module
   *
   * @returns Array of Modulefile, key filename
   */
   function getModulefiles()
   {
      global $dc;
      $this->modulefile = $dc->getModulefile("'$this->moduleid'");
   }
}
?>