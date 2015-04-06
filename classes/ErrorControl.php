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
 
include_once('IPageControl.class.php'); 
/**
 * Class ErrorControl
 * Controls errors in application, created when a error occured and will be shown to user.
 * @author Markus Svensson
 * @version 1.00
 */
class ErrorControl extends IPageControl {

	/** Errorid */
	var $errorid;
	/** Errormsg (extra message to user or the only when text missing in database */
	var $errormsg;
	/** Error object with information about the error */
	var $error;

	
	/** 
	 * Creates a ErrorControl wirh id and if there is no id a error message.
	 * @param p_errorid Id for error in database
	 * @param p_errormsg Extra message or message used when errorid not in database
	 */
	function ErrorControl($p_errorid, $p_errormsg)
	{
		$this->errorid = $p_errorid;
		$this->errormsg = $p_errormsg;
		/* Get error from database */
		global $dc;
		$this->error = $dc->getError($p_errorid);
	}
	
	/**
	 * No actions
	 * @param action 
	 */
	function execAction($action)
	{
	}

	/**
  	 * Displays the page
	 */
	function display()
	{
		$smarty = new Smarty();
		if ( is_object($this->error) )
		{
			$smarty->assign('errorid', $this->error->errorid);
			$smarty->assign('errormsg', $this->error->errormsg);
			$smarty->assign('errorheader', $this->error->errorheader);
		}
		else
		{
			$smarty->assign('errorid', $this->errorid);
			$smarty->assign('errormsg', $this->errormsg);
			$smarty->assign('errorheader', 'Det har inträffat ett fel');
		}
		$smarty->assign('backtrace', array_reverse(debug_backtrace()));
		$smarty->display('error.tpl');
	}
}
?>