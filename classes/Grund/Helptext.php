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
 * Class Helptext
 * Contains information about helptext
 * @author Markus Svensson
 * @version 1.01
 * Revisions:
 * #1.01   Markus Svensson    Added title
 */
class Helptext
{
   /** Page name */
   var $pagename;
   /** Sub page name */
   var $subpagename;
   /** Title on page */
   var $title;
   /** Help text */
   var $text;
   
  /**
   * Constructor
   *
   * @param p_pagename Page name
   * @param p_subpagename Sub page name
   * @param p_text Text for the sub page
   * @returns Helptext object
   */
   function Helptext($p_pagename, $p_subpagename, $p_title, $p_text)
   {
      $this->pagename = $p_pagename;
      $this->subpagename = $p_subpagename;
      $this->title = $p_title;
      $this->text = $p_text;
   }
}
?>