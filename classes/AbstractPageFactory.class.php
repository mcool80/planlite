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
 * Class AbstractPageFactory
 * Abstract class for Page Factory
 * @author Markus Svensson
 * @version 1.00
 */
class AbstractPageFactory {
   /**
    * Creates a page object
    * @param p_id Id of the page to create a object for
    * @param p_page Textid of a page that do not exist in database
    * @return PaceControl object that can handle the page found, if page cannot be handled null is returned 
    */
   function createPage($p_id, $p_page)
   {
   }
}
?>