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
 * Class Modulefile
 * Contains information about modulefile
 * @author Markus Svensson
 * @version 1.00
 */
class Modulefile
{
   /** Filename where it is installed, relative name */
   var $filename;
   /** Id of module where filename exists */
   var $moduleid;   
   /** Filetypename */
   var $filetypename;
   /** Version of the modulefile */
   var $version;
   /** Name of page, used when file is a Control-class */
   var $pagename;
   
  /**
   * Constructor
   *    
   * @param p_filename Filename
   * @param p_moduleid Module id for file
   * @param p_filetypename File type id
   * @param p_version Version of file
   * @param p_pagename Page name
   * @returns Modulefile object
   */
   function Modulefile($p_filename, $p_moduleid, $p_filetypename, $p_version, $p_pagename)
   {
      $this->filename = $p_filename;
      $this->moduleid = $p_moduleid;   
      $this->filetypename = $p_filetypename;
      $this->version = $p_version;   
      $this->pagename = $p_pagename;
   }
}
?>