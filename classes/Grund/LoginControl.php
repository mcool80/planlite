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
include_once('GrundControl.php');
/**
 * Class LoginControl
 * Controls the user login and logout sesssions
 * sets and unsets the global variable 'loggedinuserid' (IMPORTANT a User-id for loggedin user)
 * @author Markus Svensson
 * @version 1.01
 */
class LoginControl extends GrundControl {
   /** 1 if Loginpage is to be shown */
   var $loginpage;
   /** Message to user */
   var $message;
   /** Redirect information */
   var $redirecturl;
   /** Page error  if this is 1 */
   var $pageerror;

  /**
   * Execute a requested action
   * There are the following actions:
   *   - loginpage Display loginpage
   *   - logout Logout loggedinuser
   *   - loginuser Login a not logged in user (verify password) (indata username and password)
   *    - forgotpassword Show information how to retrive a lost password
   * @param action - requested action
   */
   function execAction($action)
   {
      switch ($action)
      {
         case 'loginpage':
		    if ( $_GET['wap'] == '1' )
				$this->addTemplates(array('Grund/login_wap.tpl'), '', '');
			else
				$this->addTemplates(array('Grund/login.tpl'), '', '');
            $this->loginpage = '1';
            break;
         case 'logout':
		    if ( $_GET['wap'] == '1' )
				$this->addTemplates(array('Grund/login_wap.tpl'), '', '');
			else
				$this->addTemplates(array('Grund/login.tpl'), '', '');				 
            $this->loginpage = '1';
            $_SESSION['loggedinuserid'] = '';
            $this->message = "Du är nu utloggad";            
            $this->pageerror = 1;            
            break;
         case 'loginuser':
		    if ( $_GET['wap'] == '1' )
				$this->addTemplates(array('Grund/login_wap.tpl'), '', '');
			else
				$this->addTemplates(array('Grund/login.tpl'), '', '');		 
            $this->loginuser($_POST['username'], $_POST['password']);
            break;
         default:
            $this->loginpage = '1';
            break;         
      }
   }

  /**
   * Displays the page
   */
   function display()
   {
      $smarty = new Smarty();
      $smarty->assign('loginpage', $this->loginpage);
      $smarty->assign('pageerror', $this->pageerror);
      $smarty->assign('message', $this->message);
	  
      $this->displayContent($smarty);
//      $smarty->display('Grund/login.tpl');
   }

  /**
   * Checks that username is correct
   * If it is correct the loggedinuserid is set
   * A redirect to user default module or module Grund is set
   * @param p_username User name to check
   * @param p_password Password to check
   */
   function loginuser($p_username, $p_password)
   {
      global $dc;

      /* Get user */
      $userarr = $dc->getUser("SELECT userid FROM pl_user WHERE username='$p_username'");
      if ( sizeof($userarr) != 1 )
      {
         $this->pageerror = 1;
         $this->message = "Felaktigt användarnamn eller lösenord";
         return;
      }   
      $user = array_pop($userarr);
      /* Check if user locked */
      if ( $user->locked )
      {
         $this->pageerror = 1;
         $this->message = "Användaren är låst, kontakta din administratör";
         return;      
      }
      
      /* Check password */
      if ( $user->passwordOk($p_password) )
      {
         /* Save userid in session */
         $_SESSION['loggedinuserid'] = $user->userid;
         
         /* Create redirect link */      
         if ( $user->default_moduleid == 0 || $user->default_moduleid == '' )
            $module = array_pop($dc->getModule("SELECT moduleid FROM pl_module WHERE modulename='Grund'"));
         else
            $module = array_pop($dc->getModule($user->default_moduleid));
         
         if ( $module == null )
         {
            $this->pageerror = 1;
            $this->message = "Inloggningen misslyckades modul saknas. Kontakta Support!";
            return;
         }
         $user->addUserlogin(1);
         /* Redirect user to new page */
         header("Location: $module->defaultpagename&wap=".$_POST['wap']);
      }
      $this->pageerror = 1;
      $user->addUserlogin(0);
      $this->message = "Felaktigt användarnamn eller lösenord";
      return;
   }
}
?>