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
 * Class Error
 * Contains information about error
 * @author Markus Svensson
 * @version 1.00
 */
class Error
{
   /** Id of error in database */
   var $errorid;
   /** Header of error */
   var $errorheader;   
   /** Error message */
   var $errormsg;

  /**
   * Constructor, creates error
   *
   * @param p_errorid Id of error
   * @param p_errorheader Header text of error
   * @param p_errormsg Error message
   * @returns Error object
   */
   function Error($p_errorid, $p_errorheader, $p_errormsg)
   {
      $this->errorid = $p_errorid;  
      $this->errorheader = $p_errorheader;   
      $this->errormsg = $p_errormsg;
   }
}
?>