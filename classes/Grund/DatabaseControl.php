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
 
include_once('Organisation.php');
include_once('Unit.php');
include_once('Planningtype.php');
include_once('Resource.php');
include_once('User.php');
include_once('Resourceplannedtime.php');
include_once('Activitytime.php');
include_once('Userright.php');
include_once('Informationtext.php');
include_once('Activity.php');
include_once('Activityslot.php');
include_once('Module.php');
include_once('Modulefile.php');
include_once('Help.php');
include_once('Helptext.php');
include_once('Entity.php');
include('Error.php');

include('config.php');


/**
 * Class DatabaseControl
 * Fetches and writes data to database
 * @author Markus Svensson
 * @version 1.01
 * #1.0.01   Markus Svensson    Added title
 */
class DatabaseControl
{
   var $cache = array();
   var $last_qry = array();
   
  /**
   * Returns an array with objects of type, missing objects ids are returned
   *
   * @param p_type Type of object
   * @param p_ids List of ids, separated by comma (in). List of not found objects (out)
   * @returns A list of found objects
   */
   function getObjects($p_type, &$p_ids)
   {
      $p_ids_out = "";
      $idarr = split(",", str_replace(" ", "", str_replace("'", "", $p_ids)) );
    
      $retarr = array();
      if ( !is_array($this->cache[$p_type]) )
         return $retarr;
      $carr = $this->cache[$p_type];      
      foreach ( $idarr as $id )
      {
         if ( is_object($carr[$id]) )
         {
            global $hits;
            $hits++;
            $retarr[$id] = $carr[$id];
         }
         else
         {
            $p_ids_out .= "'$id',";
         }
     }
     $p_ids = substr($p_ids_out, 0, strlen($p_ids_out)-1);
     return $retarr;
   }
  
  /**
   * Add a cached objekt if it exists in cache, overwrite it
   * 
   * @param p_cachetype Type of cache for example 'activity'
   * @param p_key Key in the cache
   * @param p_object Object to add to cache
   */ 
   function addCacheobject($p_cachetype, $p_key, $p_object)
   {
      if ( !is_array($this->cache[$p_cachetype]) )
         $this->cache[$p_cachetype] = array();
      $carr = $this->cache[$p_cachetype];
      $carr[$p_key] = $p_object;   
      $this->cache[$p_cachetype] = $carr;
   }
   
  /**
   * Gets db user name depending on given configuration
   * @returns dbuser name
   */
   function getdbuser()
   {   
      global $dbuser;
      return $dbuser;
   }
   
  /**
   * Gets db name depending on given configuration
   * @returns db name
   */
   function getdbname()
   {
      global $dbname;
      return $dbname;
   }
   
  /**
   * Gets db password depending on given configuration
   * @returns db password
   */
   function getdbpwd()
   {
      global $dbpwd;
      return $dbpwd;
   }

  /**
   * Gets db internet address depending on given configuration
   * @returns db internet address
   */
   function getdbaddr()
   {
      global $dbaddr;
      return $dbaddr;
   }
   
  /**
   * Connects to database and runs sql-command.
   *
   * @param sql Sql-command (select/insert/update)
   * @returns query-line
   */
   function runSql($sql)
   {
      global $connection;
      $connection = mysql_connect($this->getdbaddr(),$this->getdbuser(), $this->getdbpwd());
      if (!$connection) 
      {
         /* ERROR */
         echo mysql_error();
      }
      $db_selected = mysql_select_db($this->getdbname(), $connection);
      if (!$db_selected) 
      {
         /* ERROR */
         echo mysql_error();
      }
      $qry = mysql_query($sql, $connection);
      if (!$qry) 
      {
         /* Error */
//         $errctrl = new ErrorControl(10068, "SQL: $sql");
//         $errctrl->display();
		 echo mysql_error();
		 return 10068;

         echo "SQL: $sql";
         exit();         
      }
     /* Empty cache */
     if ( strstr(strtoupper(substr($sql,0,10)), "SELECT") == false )
     {
        $this->cache = array(); 
      }
 
      global $sqlcnt, $sqlrader;
      $sqlcnt++;      
      array_push($sqlrader, $sql);
      return $qry;   
   }
   function getRow($p_line)
   {
      return mysql_fetch_row($p_line);
   }

  /** 
   * Create a comma-separated string from given sql-statement, that starts with SELECT
   *
   * @param sql Sql-statement retrievs one column from database
   * @returns A string with comma-separeted data
   */
   function createValues($sql)
   {
      if ( is_null($sql) )
         return '';
       
      if ( !is_string($sql) && !is_numeric($sql))
      {  
         $errctrl = new ErrorControl(10075, "");
         $errctrl->display();
         exit();         
      }
         
      if ( stristr($sql, "SELECT") )
      {
         $line = $this->runSql($sql);
         if ( $row = $this->getRow($line) )
            $sql = "'$row[0]'";
         else
            $sql = "null";
         while ( $row = $this->getRow($line) )
         {
            $sql = $sql.", '$row[0]'";
         }
      }
      if ( strlen($sql) == 0 )
         return "'-1'";
      return $sql;
   }
   
  /**
   * Creates an array with values from row[1] and id from row[0]
   *
   * @param line Created query with two rows, first(0) is keys and second(1) is values
   * @returns Array with values as row[1] and keys as row[0]
   */
   function createStringwithId($line)
   {
      $retarr = array();
      
      /* Add rows to array */
      while ( $row = $this->getRow($line) )
      {
         /* Add value column 1 usning column 0 as key */
         $retarr[$row[0]] = $row[1];
      }
      return $retarr;         
   }
   
  /**
   * Fetches all entitys with unit symbols from database.
   *
   * @returns Array of Entitys
   */
   function getEntitys()
   {
      $sql = "SELECT unitsymbol, name, description
              FROM pl_entity";
      $line = $this->runSql($sql);
      $retarr = array();      
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create organisation */
         $entity = new Entity($row[0], $row[1], $row[2]);
         array_push($retarr, $entity);
      }
      return $retarr;   
   }
      
  /**
   * Fetches organisations from database.
   *
   * @param p_organisationids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Organisation, organiastionid is key
   */
   function getOrganisation($p_organisationids)
   {
      $p_organisationids = $this->createValues($p_organisationids);
      $retarr = $this->getObjects("Organisation", $p_organisationids);
      if ( strlen($p_organisationids) == 0 )
         return $retarr;
      /* Create and run sql-query */
      $sql = "SELECT organisationid, organisationname, description, no_users, address, zipcode, city, contact, 
              phonenumbers, organisationtypeid
              FROM pl_organisation
              WHERE organisationid IN ($p_organisationids)";
      $line = $this->runSql($sql);
     
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create organisation */
         $org = new Organisation($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], 
                           $row[8], $row[9]);
         $retarr[$row[0]] = $org;
         $this->addCacheobject("Organisation", $row[0], $org);
      }
      return $retarr;
   }

  /** 
   * Add a user login to database
   * @param p_userlogin Userlogin object
   */
   function updateUserlogin($p_userlogin)
   {
      $sql = "INSERT INTO pl_userlogin (userid,  date, success )
              VALUES ( $p_userlogin->userid, '$p_userlogin->date', $p_userlogin->success )";
      $this->runSql($sql);
   }

  /**
   * Get userlogins for a user
   * @param p_userid Id of the user
   * @param p_startdate Start date where to fetch data, default all
   * @returns Array of userlogin objects
   */
   function getUserlogin($p_userid, $p_startdate='1900-01-01')
   {
      /* Create and run sql-query */
      $sql = "SELECT userid, date, success
              FROM pl_userlogin
              WHERE userid='$p_userid' AND date>'$p_startdate'";
      $line = $this->runSql($sql);
      
      /* Create an array to return */
      $retarr = array();
      
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create organisation */
         $userlogin = new Userlogin($row[0], $row[1], $row[2]);
         $retarr[$row[0]] = $userlogin;
      }
      return $retarr;   
   }
   
  /**
   * Fetches units from database
   *
   * @param p_unitids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Unit, unitid is key
   */
   function getUnit($p_unitids)
   {
      $p_unitids = $this->createValues($p_unitids);   
      $retarr = $this->getObjects("Unit", $p_unitids);
      if ( strlen($p_unitids) == 0 )
         return $retarr;
     
      /* Create and run sql-query */
      $sql = "SELECT unitid, organisationid, parentunitid, unitname, description, unittypeid, 
              planningtypeid, hour_limit
              FROM pl_unit
              WHERE unitid IN ($p_unitids)";
      $line = $this->runSql($sql);
     
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create organisation */
         $unit = new Unit($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], 
                          $row[7]);
         $retarr[$row[0]] = $unit;
         $this->addCacheobject('Unit', $unit->unitid, $unit);    
      }
      return $retarr;   
   }

  /**
   * Fetches users from database
   *
   * @param p_userids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of User, userid is key
   * @version 1.01
   */
   function getUser($p_userids)
   {
      $p_userids = $this->createValues($p_userids);   
      $retarr = $this->getObjects("User", $p_userids);
      if ( strlen($p_userids) == 0 )
         return $retarr;

      /* Create and run sql-query */
      $sql = "SELECT userid, head_unitid, username, password, name, email, phonenumber, color, 
              resourceid, internal, default_moduleid, locked, isappadmin, isadmin, editdata, description
              FROM pl_user
              WHERE userid IN ($p_userids)";
      $line = $this->runSql($sql);
      
      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create user */
         $user = new User($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], 
                          $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[13], $row[14], $row[15]);
         $retarr[$row[0]] = $user;
         $this->addCacheobject('User', $users->userid, $user);       
      }
      return $retarr;      
   }

  /**
   * Fetches activitys from database
   *
   * @param p_activityids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Activity, activityid is key
   */
   function getActivity($p_activityids, $p_startdate="1900-01-01 00:00:00", $p_stopdate="9999-12-31 00:00:00")
   {
      $p_activityids = $this->createValues($p_activityids);   
      $retarr = $this->getObjects("Activity", $p_activityids);
      if ( strlen($p_activityids) == 0 )
         return $retarr; 
      $p_activityids = 
         $this->createValues("SELECT a.activityid FROM pl_activity a
                              WHERE activityid IN ($p_activityids) AND activityid IN 
                              (SELECT activityid FROM pl_activityslot GROUP BY activityid 
                                 HAVING MIN(startdate) > '$p_startdate' AND MAX(stopdate) < '$p_stopdate')"); 
      $retarr = $this->getObjects("Activity", $p_activityids);
      if ( strlen($p_activityids) == 0 )
         return $retarr;         
      /* Create and run sql-query */
      $sql = "SELECT a.activityid, a.unitid, a.activityname, a.description, a.costdriver, 
              a.isinternaltime, a.notifytime
              FROM pl_activity a
              WHERE activityid IN ($p_activityids)";
      $line = $this->runSql($sql);
      
      /* Create an array to return */
      $retarr = array();
      
      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         /* Check dates */
         $activity = new Activity($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]);
         $this->addCacheobject('Activity', $activity->activityid, $activity);                     
         $retarr[$row[0]] = $activity;
         $this->addCacheobject('Activity', $activity->activityid, $activity);                              
      }
      return $retarr;         
   }

  /**
   * Fetches modules from database
   *
   * @param p_moduleids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Module, moduleid is key
   */
   function getModule($p_moduleids)
   {
      $p_moduleids = $this->createValues($p_moduleids);   
      $retarr = $this->getObjects("Module", $p_moduleids);
      if ( strlen($p_moduleids) == 0 )
         return $retarr;     
      /* Create and run sql-query */
      $sql = "SELECT moduleid, modulename, supplier, version, description, defaultpagename
            FROM pl_module
            WHERE moduleid IN ($p_moduleids)";
      $line = $this->runSql($sql);
    
      /* Add module to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create module */
         $module = new Module($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
         $retarr[$row[0]] = $module;
         $this->addCacheobject("Module", $row[0], $module);
      }
      return $retarr;         
   }

  /**
   * Fetches rights from database
   *
   * @param p_rightnames Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Right, rightname is key
   */
   function getRight($p_rightnames)
   {
      $p_rightnames = $this->createValues($p_rightnames);   
      $retarr = $this->getObjects("Right", $p_rightnames);
      if ( strlen($p_rightnames) == 0 )
         return $retarr;     
     
      /* Get rights  */
      $sql = "SELECT null, rightname, shortname, null
            FROM pl_userright WHERE rightname IN ($p_rightnames)";
      $line = $this->runSql($sql);
      
      /* Add Rights to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create right */
         $right = new Userright($row[0], $row[1], $row[2], $row[3]);
         $retarr[$row[1]] = $right;
         $this->addCacheobject("Right", $row[1], $right);
      }   
      return $retarr;
   }

  /**
   * Fetches rights from database for a given user
   *
   * @param p_userid Id of user
   * @returns Array of Array with Userright (with rightname as key), unitid is key for the big array
   */
   function getUserright($p_userid)
   {
      /* Get other units user has rights in */
      $sql = "( SELECT DISTINCT unitid FROM pl_otherunit WHERE userid=$p_userid )
                UNION
              ( SELECT head_unitid FROM pl_user WHERE userid=$p_userid )";         
      $line = $this->runSql($sql);
      $retarr = array();
      while ( $row = $this->getRow($line) )
      {
         /* Get rights  */
         $sql = "( SELECT $row[0], rightname, shortname, 0
                   FROM pl_userright )
                 UNION
                 ( SELECT riu.unitid, ur.rightname, ur.shortname, riu.isset
                   FROM pl_right_in_unit riu, pl_userright ur
                   WHERE riu.userid=$p_userid AND riu.unitid=$row[0] AND
                   riu.rightname=ur.rightname )";               
         $line2 = $this->runSql($sql);
         /* Create an array to return */
         $newarr = array();
      
         /* Add Rights to array */
         while ( $row2 = $this->getRow($line2) )
         {
            /* Create organisation */
            $right = new Userright($row2[0], $row2[1], $row2[2], $row2[3]);
            $newarr[$row2[1]] = $right;
         }
         $retarr[$row[0]] = $newarr;
      }
      return $retarr;      
   }

  /**
   * Fetches informationtext from database
   *
   * @param p_informationtextids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Informationtext, informationtextid is key
   */
   function getInformationtext($p_informationtextids)
   {
      $p_informationtextids = $this->createValues($p_informationtextids);   
      $retarr = $this->getObjects("Informationtext", $p_informationtextids);
      if ( strlen($p_informationtextids) == 0 )
         return $retarr;     
        
      /* Create and run sql-query */
      $sql = "SELECT informationtextid, unitid, informationtext,
              startdate, stopdate, description
              FROM pl_informationtext
              WHERE informationtextid IN ($p_informationtextids)";
      $line = $this->runSql($sql);
      
      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create organisation */
         $infotext = new Informationtext($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
         $this->addCacheobject('Informationtext', $infotext->informationtextid, $infotext);  
         $retarr[$row[0]] = $infotext;
      }
      return $retarr;            
   }

  /**
   * Fetches informationtext for a Unit from database
   *
   * @param p_unitd Unit id connected to Informationtext
   * @returns Array of Informationtext, informationtextid is key
   */
   function getInformationtextInUnit($p_unitid)
   {
      return $this->getInformationtext("SELECT informationtextid FROM pl_informationtext WHERE unitid=$p_unitid");
   }

  /**
   * Fetches resources from database
   *
   * @param p_resourceids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Resource, resourceid is key
   */
   function getResource($p_resourceids)
   {
      $p_resourceids = $this->createValues($p_resourceids);
      $retarr = $this->getObjects("Resource", $p_resourceids);
      if ( strlen($p_resourceids) == 0 )
         return $retarr;     
     
      /* Create and run sql-query */
      $sql = "SELECT resourceid, unitid, resourcename, description
            FROM pl_resource
            WHERE resourceid IN ($p_resourceids)";
      $line = $this->runSql($sql);
      
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create resource */
         $res = new Resource($row[0], $row[1], $row[2], $row[3]);
         $this->addCacheobject('Resource', $res->resourceid, $res);       
         $retarr[$row[0]] = $res;
      }
      return $retarr;   
   }

  /**
   * Fetches resources for Unit
   *
   * @param p_unitid Unit id the resource are connected to
   * @returns Array of Resource, resourceid is key
   */
   function getResourceInUnit($p_unitid)
   {
      return $this->getResource("SELECT resourceid FROM pl_resource WHERE unitid=$p_unitid");
   }
   
  /**
   * Fetches resource for User
   *
   * @param p_userid User id the resource are connected to
   * @returns Array of Resource with one Resource
   */
   function getResourceInUser($p_userid)
   {
      return $this->getResource("SELECT resourceid FROM pl_user WHERE userid=$p_unitid");   
   }
   
  /**
   * Fetches Resouceplannedtime from database
   *
   * @param p_resourceplannedtimeids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Resouceplannedtime, plannedtimeid is key
   */
   function getResourceplannedtime($p_resourceplannedtimeids)
   {
      $p_resourceplannedtimeids = $this->createValues($p_resourceplannedtimeids);
      $retarr = $this->getObjects("Resourceplannedtime", $p_resourceplannedtimeids);
      if ( strlen($p_resourceplannedtimeids) == 0 )
         return $retarr;     
     
      /* Create and run sql-query */
      $sql = "SELECT resourceplannedtimeid, activityslotid, resourceid, plannedtime, unitsymbol, description
              FROM pl_resourceplannedtime 
              WHERE resourceplannedtimeid IN ($p_resourceplannedtimeids)";   
      $line = $this->runSql($sql);
      
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create resourceplannedtime */
         $plannedtime = new Resourceplannedtime($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
         $this->addCacheobject('Resourceplannedtime', $plannedtime->resourceplannedtimeid, $plannedtime);
         $retarr[$row[0]] = $plannedtime;
      }
      return $retarr;   
   }
      
  /**
   * Fetches Resouceplannedtime from database
   *
   * @param p_userids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of Resouceplannedtime, plannedtimeid is key
   */
   function getResourceplannedtimeInUser($p_resourceids)
   {
      $p_resourceids = $this->createValues($p_resourceids);
      /* Create and run sql-query */
      return $this->getResourceplannedtime("SELECT resourceplannedtimeid FROM pl_plannedtime 
                                            WHERE resourceid IN ($p_resourceids)");
   }

  /**
   * Fetches Resourceplannedtime for given Activityslot
   *
   * @param p_activityslotids Activityslotids with resourceplannedtime
   * @returns Array of Resouceplannedtime, plannedtimeid is key
   */
   function getResourceplannedtimeInActivityslot($p_activityslotids)
   {
      $p_activityslotids = $this->createValues($p_activityslotids);
      /* Create and run sql-query */
      return $this->getResourceplannedtime("SELECT resourceplannedtimeid FROM pl_resourceplannedtime 
                                            WHERE activityslotid IN ($p_activityslotids)");
   }

  /**
   * Fetches Activitytime from database within given timespan
   *
   * @param p_activitytimeids Comma-separated string with ids or a sql-select that selects ids
   * @param p_startdate Startdate for timespan, default no timespan
   * @param p_stopdate Stopdate for timespan, default no timespan
   * @returns Array of Activitytime, activitytimeid is key
   */
   function getActivitytime($p_activitytimeids, $p_startdate='1900-01-01', $p_stopdate='9999-12-31')
   {
      $p_activitytimeids = $this->createValues($p_activitytimeids);
      $p_activitytimeids = 
      $this->createValues("SELECT activitytimeid FROM pl_activitytime actt, pl_activityslot acts 
                              WHERE actt.activitytimeid IN ($p_activitytimeids) AND (
                              ( acts.startdate >= '$p_startdate' AND acts.stopdate <= '$p_stopdate' ) OR
                              ( acts.startdate <= '$p_startdate' AND acts.stopdate >= '$p_startdate' ) OR 
                              ( acts.startdate <= '$p_stopdate' AND acts.stopdate >= '$p_stopdate') ) AND
                              actt.activityslotid = acts.activityslotid");
      $retarr = $this->getObjects("Activitytime", $p_activitytimeids);   
      if ( strlen($p_activitytimeids) == 0 )
         return $retarr;     
      
      /* Create and run sql-query */
      $sql = "SELECT activitytimeid, activityslotid, userid, startdate, plannedtime, workedtime, unitsymbol, description
              FROM pl_activitytime 
              WHERE activitytimeid IN ($p_activitytimeids)";

      $line = $this->runSql($sql);

      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create plannedtime */
         $activitytime = new Activitytime($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
         $retarr[$row[0]] = $activitytime;
         $this->addCacheobject("Activitytime", $row[0], $activitytime);
      }
      return $retarr;   
   }
      
  /**
   * Fetches Activitytime from database within given timespan for given users
   *
   * @param p_userids Comma-separated string with ids or a sql-select that selects ids
   * @param p_startdate Startdate for timespan, default no timespan
   * @param p_stopdate Stopdate for timespan, default no timespan
   * @returns Array of Activitytime, activitytimeid is key
   */
   function getActivitytimeInUser($p_userids, $p_startdate='1900-01-01', $p_stopdate='9999-12-31')
   {
      $p_userids = $this->createValues($p_userids);
      /* Create and run sql-query */
      return $this->getActivitytime("SELECT activitytimeid FROM pl_activitytime WHERE userid IN ($p_userids)", 
                              $p_startdate, $p_stopdate);
   }

  /**
   * Fetches Activitytime for given Activityslot in given timespan
   *
   * @param p_activityslotids Activityslotids with plannedtime
   * @param p_startdate Startdate for timespan, default no timespan
   * @param p_stopdate Stopdate for timespan, default no timespan
   * @returns Array of Activitytime, activitytimeid is key
   */
   function getActivitytimeInActivityslot( $p_activityslotids, $p_startdate='1900-01-01', $p_stopdate='9999-12-31')
   {
      $p_activityslotids = $this->createValues($p_activityslotids);
      /* Create and run sql-query */
      return $this->getActivitytime("SELECT activitytimeid FROM pl_activitytime 
                                     WHERE activityslotid IN ($p_activityslotids)", 
                              $p_startdate, $p_stopdate);
   }

  /**
   * Fetches worktime for unit, user and timespan
   *
   * @param p_worktimeids Ids of worktime
   * @returns Array of Worktime, year-month-week-day_in_month is key
   */
   function getWorktime($p_worktimeids)
   {
      $p_worktimeids = $this->createValues($p_worktimeids);
      $retarr = $this->getObjects("Worktime", $p_worktimeids);   
      if ( strlen($p_worktimeids) == 0 )
         $p_worktimeids = "null";
      /* Create and run sql-query */
      $sql = "SELECT worktimeid, unitid, userid, year, month, week, day_in_month, worktime, description
              FROM pl_worktime
              WHERE worktimeid IN ($p_worktimeids)";

      $line = $this->runSql($sql);
     
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create plannedtime */
         $worktime = new Worktime($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], round($row[7]), $row[8]);
         $retarr[$row[0]] = $worktime; 
         $this->addCacheobject("Worktime", $row[0], $worktime);
      }
      $newarr = array();
      foreach ( $retarr as $wt )
      {
         $key = $wt->year."-";
         if ( strlen($wt->month) < 2 )
            $key .= "0";
         $key .= $wt->month."-";
         if ( strlen($wt->week) < 2 )
            $key .= "0";
         $key .= $wt->week."-";
         if ( strlen($wt->day_in_month) < 2 )
            $key .= "0";            
         $key .= $wt->day_in_month;
         $newarr[$key] = $wt;
      }
      return $newarr;
   }

  /**
   * Get costdrivers from database for a Organisation and freetext
   *
   * @param p_organisationid Organisation id connected to Costdriver
   * @param p_freetext Optional searchstring for finding a costdriver
   * @returns Array of String, with costrdriver name
   */
   function getCostdriver($p_organisationid, $p_freetext='%')
   {
      /* Create and run sql-query */
      $sql = "SELECT costdriver, organisationid, description
              FROM pl_costdriver
              WHERE costdriver LIKE '%$p_freetext%' AND organisationid=$p_organisationid";

      $line = $this->runSql($sql);
      
      /* Create an array to return */
      $retarr = array();
      
      /* Add Organisations to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create plannedtime */
         $costdriver = $row[0];
         $retarr[$row[0]] = $costdriver;
      }
      return $retarr;         
   }

  /**
   * Fetches organisationtype names from database
   *
   * @param p_organisationtypeids Comma-separated string with ids or a sql-select that selects ids
   * @returns Array of String, with organisationtypeid as key
   */
   function getOrganisationtype($p_organisationtypeids)
   {
      $p_organisationtypeids = $this->createValues($p_organisationtypeids);   
      /* Create and run sql-query */
      $sql = "SELECT organisationtypeid, typename
              FROM pl_organisationtype
              WHERE organisationtypeid IN ($p_organisationtypeids)";
      $line = $this->runSql($sql);

      return $this->createStringwithId($line);   
   }

  /**
   * Fetches modulefiles in database for module
   *
   * @param $p_moduleid Moduleid where modulefile exist
   * @returns Array of Modulefile, with filename as key
   */
   function getModulefile($p_moduleid)
   {
      $p_moduleid = $this->createValues($p_moduleid);   
     
      /* Create and run sql-query */
      $sql = "SELECT filename, moduleid, filetypename, version, pagename
            FROM pl_modulefile
            WHERE moduleid=$p_moduleid";
      $line = $this->runSql($sql);
      
      /* Create an array to return */
      $retarr = array();
      
      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create modulefile */
         $modulefile = new Modulefile($row[0], $row[1], $row[2], $row[3], $row[4]);
         $retarr[$row[0]] = $modulefile;
      }
      return $retarr;   
   }

  /**
   * Fetches unittypes from database
   *
   * @param p_unittypeids Comma-separated string with ids or a sql-select that selects ids, default all are fetched
   * @returns Array of String, with unittypeid as key
   */
   function getUnittype($p_unittypeids='SELECT unittypeid FROM pl_unittype')
   {
      $p_unittypeids = $this->createValues($p_unittypeids);   
      /* Create and run sql-query */
      $sql = "SELECT unittypeid, unittypename
            FROM pl_unittype
            WHERE unittypeid IN ($p_unittypeids)";
      $line = $this->runSql($sql);
   
      return $this->createStringwithId($line);         
   }

  /**
   * Fetches plannedtypes from database
   *
   * @param p_planningtypeids Comma-separated string with ids or a sql-select that selects ids, default all are fetched
   * @returns Array of Planningtype, with planningtypeid as key
   */
   function getPlanningtype($p_planningtypeids='SELECT planningtypeid FROM pl_planningtype')
   {
      $p_planningtypeids = $this->createValues($p_planningtypeids);   
      /* Create and run sql-query */
      $sql = "SELECT planningtypeid, planningtypename, timeunit, description
              FROM pl_planningtype
              WHERE planningtypeid IN ($p_planningtypeids)";
      $line = $this->runSql($sql);
   
      /* Create an array to return */
      $retarr = array();
      
      /* Add Planningtype to array */
      while ( $row = $this->getRow($line) )
      {
         /* Create organisation */
         $planningtype = new Planningtype($row[0], $row[1], $row[2], $row[3]);
         $retarr[$row[0]] = $planningtype;
      }
      return $retarr;
   }

  /**
   * Fetches activityslots from database with given timespan
   *
   * @param p_activityslotids Comma-separated string with ids or a sql-select that selects ids
   * @param p_startdate Startdate of timespan to fetch, default all
   * @param p_stopdate Stopdate of timespan to fetch, default all
   * @returns Array of Activityslot, with activityslotid as key
   */
   function getActivityslot($p_activityslotids, $p_startdate="1900-01-01 00:00:00", $p_stopdate="9999-12-31 00:00:00")
   {
      $p_activityslotids = $this->createValues($p_activityslotids);   
      $retarr = $this->getObjects("Activityslot", $p_activityslotids);   
      if ( strlen($p_activityslotids) == 0 )
         return $retarr;          

      /* Create and run sql-query */
      $sql = "SELECT activityslotid, activityid, startdate, stopdate, plannedtime, isnotified, unitsymbol, description
            FROM pl_activityslot
            WHERE activityslotid IN ($p_activityslotids) AND startdate BETWEEN '$p_startdate' AND '$p_stopdate'";
      $line = $this->runSql($sql);

      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         $activityslot = new Activityslot($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
         $this->addCacheobject('Activityslot', $activityslot->activityslotid, $activityslot);                  
         $retarr[$row[0]] = $activityslot;
      }
      return $retarr;      
   }

  /**
   * Fetches Units from database for users other units
   *
   * @param p_userid User id with other units
   * @returns Array of Unit, with unitid as key
   */
   function getOtherunits($p_userid)
   {
      return $this->getUnit("SELECT unitid FROM pl_otherunit WHERE userid=$p_userid");
   }

  /**
   * Fetches help from database
   *
   * @param $p_helppage
   * @returns Helpobjekt
   */
   function getHelp($p_pagename)
   {
      /* Create and run sql-query */
      $sql = "SELECT pagename, title, supportmail, supplier, version
              FROM pl_help
              WHERE pagename='$p_pagename'";
      $line = $this->runSql($sql);
      
      /* Create an array to return */
      $retarr = array();
      
      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         $help = new Help($row[0], $row[1], $row[2], $row[3], $row[4]);
         return $help;
      }
      return null;         
   }

  /**
   * Fetches helptext from database
   *
   * @param p_pagename Pagename for helptexts
   * @param p_subpagename Optional subpagename, default all helptexts returned
   * @returns Array of Helptext
   */
   function getHelptext($p_pagename, $p_subpagename='%')
   {
      /* Create and run sql-query */
      // #1.0.01 - line changed, Markus Svensson
      $sql = "SELECT pagename, subpagename, title, helptext
              FROM pl_helptext
              WHERE pagename='$p_pagename' AND subpagename LIKE '%$p_subpagename%'";
      $line = $this->runSql($sql);

      /* Create an array to return */
      $retarr = array();

      /* Add User to array */
      while ( $row = $this->getRow($line) )
      {
         $helptext = new Helptext($row[0], $row[1], $row[2], $row[3]);
         array_push($retarr, $helptext);
      }
      return $retarr;            
   }

  /**
   * Fetches error object from database
   *
   * @param p_errorid Id of the error to find
   * @returns Error object, false if none found
   */
   function getError($p_errorid)
   {
      /* Create and run sql-query */
      $sql = "SELECT errorid, errorheader, errormsg
              FROM pl_error
              WHERE errorid=$p_errorid";

      $line = $this->runSql($sql);
      $error = false;

      if ( $row = $this->getRow($line) )
      {
         $error = new Error($row[0], $row[1], $row[2]);
      }
      return $error;            
   }


  /**
   * Updates or inserts Organisation in database
   * On insert a organisationunit is created
   *
   * @param p_organiation in/out Organisation, returns updated organisation
   * @param p_updateonly If true the organisation is updated, default true
   * @returns 0 if all ok, else errorcode
   */
   function updateOrganisation(&$p_organisation, $p_updateonly=true)
   {
      /* Check if organisation is to be inserted */
      if ( !$p_updateonly )
      {
         /* Check that organisationname not used before */
         $orgtest = 
            $this->getOrganisation("SELECT organisationid FROM pl_organisation 
                                    WHERE organisationname='$p_organisation->organisationname'");
         if ( sizeof($orgtest) > 0 )
            return 10009;
            
         $sql = "INSERT INTO pl_organisation (organisationname,  description, no_users, 
                 address, zipcode, city, 
                 phonenumbers, contact, organisationtypeid )
                 VALUES
                 ( '$p_organisation->organisationname', '$p_organisation->description', $p_organisation->no_users,
                   '$p_organisation->address', '$p_organisation->zipcode', '$p_organisation->city',
                   '$p_organisation->phonenumbers', '$p_organisation->contact', $p_organisation->organisationtypeid )";
     
         /* Insert organisation */
         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
            $sql = "SELECT MAX(organisationid) FROM pl_organisation";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_organisation->organisationid = $row[0];
            else
               return 10002;
            /* Create organisatinunit  */
            $orgunit = new Unit('NULL', $p_organisation->organisationid, 'null', $p_organisation->organisationname,
                                 '',    0, 0, 0);
            $this->updateUnit($orgunit, false);
            $p_organisation->organisationunit = $orgunit;
         }
         else
         {
            return 10001;
         }
      }
      else
      {
         /* Check that organisationname not used before */
         $orgtest = $this->getOrganisation("SELECT organisationid FROM pl_organisation WHERE organisationname='$p_organisation->organisationname' AND organisationid!=$p_organisation->organisationid");
         if ( sizeof($orgtest) > 0 )
            return 10009;
      
         $sql = "UPDATE pl_organisation SET organisationname='$p_organisation->organisationname',
                 description='$p_organisation->description', no_users=$p_organisation->no_users, 
                 address='$p_organisation->address', zipcode='$p_organisation->zipcode', 
                 city='$p_organisation->city', phonenumbers='$p_organisation->phonenumbers', 
                 contact='$p_organisation->contact', organisationtypeid=$p_organisation->organisationtypeid
                 WHERE organisationid=$p_organisation->organisationid";
         /* Update organisation */
         if ( ! $this->runSql($sql) )
            return 10003;
      }
      return 0;
   }
   
  /**
   * Inserts Modules in Organisation in database
   * First all modules in organisation is removed, the they are inserted
   *
   * @param p_organiationid Id of organisation
   * @param p_moduleids Array of Moduleids
   * @returns 0 if all ok, else errorcode
   */
   function updateModulesInOrganisation($p_organisationid, $p_moduleids)
   {
      $sql = "DELETE FROM pl_module_in_organisation WHERE organisationid=$p_organisationid";
      if ( !$this->runSql($sql) )
         return 10014;
      foreach ( $p_moduleids as $moduleid )
      {
         $sql = "INSERT INTO pl_module_in_organisation ( organisationid, moduleid )
                 VALUES ( '$p_organisationid', '$moduleid' )";
         if ( !$this->runSql($sql) )
            return 10015;
      }
      return 0;
   }

  /**
   * Updates or inserts Unit into database
   * Informationtext for units is updated with function DatabaseControl::updateInformationtext
   * Resource for units is update with function DatabaseControl::updateResource
   *
   * @param p_unit in/out Unit, returns updated unit
   * @param p_updateonly If true the unit is updated, default true
   * @returns 0 if all ok, else errorcode
   */
   function updateUnit(&$p_unit, $p_updateonly=true)
   {
      /* Check if unit is to be inserted */
      if ( !$p_updateonly )
      {
         /* Check that unitname not used before */
         $line = $this->runSql("SELECT COUNT(unitid) FROM pl_unit 
                                WHERE unitname='$p_unit->unitname' AND organisationid=$p_unit->organisationid");
         $row = $this->getRow($line);
         if ( $row[0] > 0 )
            return 10010;
            
         $sql = "INSERT INTO pl_unit (organisationid,  parentunitid, unitname, 
                 description, unittypeid, planningtypeid, hour_limit )
                 VALUES
                 ( '$p_unit->organisationid', $p_unit->parentunitid, '$p_unit->unitname',
                   '$p_unit->description', '$p_unit->unitypeid', '".$p_unit->planningtype->planningtypeid."', 
                   '$p_unit->hour_limit' )";

         /* Insert unit */
         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
            $sql = "SELECT MAX(unitid) FROM pl_unit";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_unit->unitid = $row[0];
            else
               return 10011;
         }
         else
         {
            return 10012;
         }
      }
      else
      {
         /* Check that unitname not used before */
         $line = $this->runSql("SELECT COUNT(unitid) FROM pl_unit 
                                WHERE unitname='$p_unit->unitname' AND organisationid=$p_unit->organisationid 
                                AND unitid!=$p_unit->unitid");
         $row = $this->getRow($line);
         if ( $row[0] > 0 )
            return 10010;         
         if ( $p_unit->parentunitid == '')
            $p_unit->parentunitid='null';
         $sql = "UPDATE pl_unit SET organisationid='$p_unit->organisationid',
                 parentunitid=$p_unit->parentunitid, unitname='$p_unit->unitname', 
                 description='$p_unit->description', unittypeid='$p_unit->unittypeid', 
                 planningtypeid='".$p_unit->planningtype->planningtypeid."', hour_limit='$p_unit->hour_limit'
                 WHERE unitid=$p_unit->unitid";
         /* Update organisation */
         if ( ! $this->runSql($sql) )
            return 10013;
      }
      return 0;
   }

  /**
   * Updates or inserts User (+ rights for user and otherunits) into database
   *
   * @param p_user in/out User, returns updated user
   * @param p_updateonly If true the user is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateUser(&$p_user, $p_updateonly=true)
   {
      /* Get resourceid, if resource is connected to user */
      $resourceid = null;
      if ( is_object($p_user->resource) )
         $resourceid = $p_user->resource->resourceid;   
      /* Check if user is to be inserted */
      if ( !$p_updateonly )
      {
         /* Check that username not used before */
         $usertest = $this->getUser("SELECT userid FROM pl_user WHERE username='$p_user->username'");
         if ( sizeof($usertest) > 0 )
            return 10019;
            
         $sql = "INSERT INTO pl_user (head_unitid, username, name, password, 
                 email, phonenumber, color,
                 resourceid, internal, default_moduleid, locked, isappadmin, isadmin, editdata, description )
                 VALUES
                 ( '".$p_user->headunit->unitid."', '$p_user->username', '$p_user->name', '$p_user->password',
                   '$p_user->email', '$p_user->phonenumber', '".$p_user->color."',
                   '".$resourceid."', '$p_user->internal', '$p_user->default_moduleid', '$p_user->locked', 
                   '$p_user->isappadmin', '$p_user->isadmin', '$p_user->editdata', '$p_user->description' )";

         /* Insert unit */
         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
            $sql = "SELECT MAX(userid) FROM pl_user";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_user->userid = $row[0];
            else
               return 10020;
         }
         else
         {
            return 10021;
         }
      }
      else
      {
         /* Check that username not used before */
         $usertest = $this->getUser("SELECT userid FROM pl_user WHERE username='$p_user->username' 
                                     AND userid!='$p_user->userid'");
         if ( sizeof($usertest) > 0 )
            return 10019;
      
         $sql = "UPDATE pl_user SET head_unitid='".$p_user->headunit->unitid."',
                 username='$p_user->username', name='$p_user->name', password='$p_user->password',
                 email='$p_user->email', phonenumber='$p_user->phonenumber', 
                 color='".$p_user->color."', resourceid='$resourceid', internal='$p_user->internal',
                 default_moduleid='$p_user->default_moduleid', locked='$p_user->locked',
                 isappadmin='$p_user->isappadmin', isadmin='$p_user->isadmin', editdata='$p_user->editdata',
				 description='$p_user->description'
                 WHERE userid=$p_user->userid";
         /* Update organisation */
         if ( ! $this->runSql($sql) )
            return 10022;
      }
      /* Set others units for user */
      $sql = "DELETE FROM pl_otherunit WHERE userid=$p_user->userid";
      if ( !$this->runSql($sql) )
         return 10023;
      foreach ( $p_user->otherunits as $unit )
      {
         $sql = "INSERT INTO pl_otherunit ( unitid, userid ) VALUES
                 ( '$unit->unitid', '$p_user->userid' ) ";
         if ( !$this->runSql($sql) )
            return 10023;
      }

      /* Add rights for user */
      $sql = "DELETE FROM pl_right_in_unit WHERE userid=$p_user->userid";
      if ( !$this->runSql($sql) )
         return 10024;
         
      foreach ( $p_user->userright as $userright_for_unit )
      {
         foreach ( $userright_for_unit as $userright )
         {
            if ( $userright->setval )
            {
               $sql = "INSERT INTO pl_right_in_unit ( userid, unitid, rightname, isset ) VALUES
                       (  '$p_user->userid', '$userright->unitid', '$userright->rightname', '1') ";
               if ( !$this->runSql($sql) )
                  return 10024;
            }
         }
      }
      return 0;   
   }

  /**
   * Updates or inserts Activity into database
   *
   * @param p_activity in/out Activity, returns updated activity
´  * @param p_updateonly If true the activity is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateActivity(&$p_activity, $p_updateonly=true)
   {
      /* Check if unit is to be inserted */
      if ( !$p_updateonly )
      {
         /* Check that unitname not used before */
         $sql = "INSERT INTO pl_activity (unitid,  activityname, description, 
                 costdriver, isinternaltime, notifytime )
                 VALUES
                 ( '$p_activity->unitid', '$p_activity->activityname', '$p_activity->description',
                   '$p_activity->costdriver', '$p_activity->isinternaltime', '$p_activity->notifytime' )";
     
         /* Insert activity */
         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
            $sql = "SELECT MAX(activityid) FROM pl_activity";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_activity->activityid = $row[0];
            else
               return 10033;
         }
         else
         {
            return 10034;
         }
      }
      else
      {
         $sql = "UPDATE pl_activity SET unitid='$p_activity->unitid',
                 activityname='$p_activity->activityname', description='$p_activity->description', 
                 costdriver='$p_activity->costdriver', isinternaltime='$p_activity->isinternaltime', 
                 notifytime='$p_activity->notifytime'
                 WHERE activityid=$p_activity->activityid";
         /* Update organisation */
         if ( ! $this->runSql($sql) )
            return 10035;
      }
      return 0;
   }

  /**
   * Updates or inserts Module into database
   * It also updates modulesfiles in the database
   *
   * @param p_module in/out Module, returns updated module
   * @param p_updateonly If true the module is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateModule(&$p_module, $p_updateonly=true)
   {
      /* Check if user is to be inserted */
      if ( !$p_updateonly )
      {
         /* Delete module if it exits before */
         $sql = "DELETE FROM pl_module WHERE modulename='$p_module->modulename'";
         if ( !$this->runSql($sql) )
            return 10056;

         /* Insert new module */
         $sql = "INSERT INTO pl_module (modulename, supplier, version, 
                 description, defaultpagename )
                 VALUES
                 ( '$p_module->modulename', '$p_module->supplier', '$p_module->version',
                   '$p_module->description', '$p_module->defaultpagename' )";

         /* Insert unit */
         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
            $sql = "SELECT MAX(moduleid) FROM pl_module";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_module->moduleid = $row[0];
            else
               return 10053;
         }
         else
         {
            return 10054;
         }
      }
      else
      {
         $sql = "UPDATE pl_module SET modulename='$p_module->modulename',
                 supplier='$p_module->supplier', version='$p_module->version', 
                 description='$p_module->description', defaultpagename='$p_module->defaultpagename' 
                 WHERE moduleid=$p_module->moduleid";
         /* Update module */
         if ( ! $this->runSql($sql) )
            return 10055;
      }

      /* Insert modulefiles for user, first remove then insert */
      $sql = "DELETE FROM pl_modulefile WHERE moduleid=$p_module->moduleid";
      if ( !$this->runSql($sql) )
         return 10058;
         
      foreach ( $p_module->modulefile as $modulefile )
      {
         $sql = "INSERT INTO pl_modulefile ( filename, moduleid, filetypename, version, pagename ) VALUES
                 (  '$modulefile->filename', '$p_module->moduleid', '$modulefile->filetypename', 
                    '$modulefile->version', '$modulefile->pagename' ) ";
         if ( !$this->runSql($sql) )
            return 10057;
      }
      return 0;
   }

  /**
   * Updates or inserts Informationtext into database
   *
   * @param p_informationtext in/out Informationtext, returns updated informationtext
   * @param p_updateonly If true the informationtext is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateInformationtext(&$p_informationtext, $p_updateonly=true)
   {
      /* Check if unit is to be inserted */
      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO pl_informationtext (unitid, informationtext, 
                 startdate, stopdate, description )
                 VALUES
                 ( '$p_informationtext->unitid', '$p_informationtext->informationtext', 
                   '$p_informationtext->startdate', '$p_informationtext->stopdate',
				   '$p_informationtext->description' )";
     
         /* Insert unit */
         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
            $sql = "SELECT MAX(informationtextid) FROM pl_informationtext";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_informationtext->informationtextid = $row[0];
            else
               return 10025;
         }
         else
         {
            return 10026;
         }
      }
      else
      {
         $sql = "UPDATE pl_informationtext SET unitid='$p_informationtext->unitid',
                 informationtext='$p_informationtext->informationtext', 
                 startdate='$p_informationtext->startdate', 
                 stopdate='$p_informationtext->stopdate',
				 description='$p_informationtext->description'
                 WHERE informationtextid=$p_informationtext->informationtextid";
         /* Update informationtext */
         if ( ! $this->runSql($sql) )
            return 10027;
         
      }
      return 0;
   }

  /**
   * Updates or inserts Resource into database
   *
   * @param p_user in/out Resource, returns updated resource
   * @param p_updateonly If true the resource is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateResource(&$p_resource, $p_updateonly=true)
   {
      /* Check if resourceis to be inserted */
      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO pl_resource (unitid, resourcename, description )
                 VALUES
                 ( '$p_resource->unitid', '$p_resource->resourcename', '$p_resource->description' )";

         /* Insert resource */
         if ( $this->runSql($sql) )
         {
            /* Get created resource id */
            $sql = "SELECT MAX(resourceid) FROM pl_resource";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_resource->resourceid = $row[0];
            else
               return 10017;
         }
         else
            return 10016;
      }
      else
      {
         $sql = "UPDATE pl_resource SET unitid='$p_resource->unitid', resourcename='$p_resource->resourcename',
		                description='$p_resource->description'
               WHERE resourceid=$p_resource->resourceid";
         /* Update resource */
         if ( ! $this->runSql($sql) )
            return 10018;
      }
      return 0;   
   }

  /**
   * Updates or inserts Activitytime into database
   *
   * @param p_plannedtime in/out Activitytime, returns updated plannedtime
   * @param p_updateonly If true the plannedtime is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateActivitytime(&$p_activitytime, $p_updateonly=true)
   {
      $user = array_pop($this->getUser($p_activitytime->userid));
      if ( $user->headunit->unittypeid == 1 )
      {
         /* Check if activitytime overlaps an other activity time */
         $sql = "SELECT COUNT(*) FROM pl_activitytime
                 WHERE userid=$p_activitytime->userid AND
                 ( startdate BETWEEN '$p_activitytime->startdate' AND 
                   DATE_ADD('$p_activitytime->startdate', INTERVAL $p_activitytime->plannedtime HOUR) ) AND
                   activitytimeid!='$p_activitytime->activitytimeid'";
         $line = $this->runSql($sql);
         if ( $row = $this->getRow($line) )
         {
            if ( $row[0] > 0 )
                return 10073;
         }
      }
       
      /* Check if activitytime is to be inserted */
      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO pl_activitytime (activityslotid, userid, startdate, 
                 plannedtime, workedtime, unitsymbol, description )
                 VALUES
                 ( '$p_activitytime->activityslotid', '$p_activitytime->userid', '$p_activitytime->startdate',
                   '$p_activitytime->plannedtime', '$p_activitytime->workedtime', '$p_activitytime->unitsymbol',
				   '$p_activitytime->description' )";

         /* Insert activitytime */
         if ( $this->runSql($sql) )
         {
            /* Get created activitytime id */
            $sql = "SELECT MAX(activitytimeid) FROM pl_activitytime";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_activitytime->activitytimeid = $row[0];
            else
               return 10041;
         }
         else
            return 10042;
      }
      else
      {
         $sql = "UPDATE pl_activitytime SET userid='$p_activitytime->userid',
                 startdate='$p_activitytime->startdate', plannedtime='$p_activitytime->plannedtime',
                 workedtime='$p_activitytime->workedtime', unitsymbol='$p_activitytime->unitsymbol',
				 description='$p_activitytime->description'
                 WHERE activitytimeid=$p_activitytime->activitytimeid";
         /* Update activitytime */
         if ( ! $this->runSql($sql) )
            return 10043;
      }
      return 0;   
   }

  /**
   * Updates or inserts Resourceplannedtime into database
   *
   * @param p_resourceplannedtime in/out Activitytime, returns updated plannedtime
   * @param p_updateonly If true the plannedtime is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateResourceplannedtime(&$p_resourceplannedtime, $p_updateonly=true)
   {
      /* Check if object is to be inserted */
      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO pl_resourceplannedtime (activityslotid, resourceid,  
                 plannedtime, unitsymbol, description )
                 VALUES
                 ( '$p_resourceplannedtime->activityslotid', '$p_resourceplannedtime->resourceid', 
                   '$p_resourceplannedtime->plannedtime', '$p_resourceplannedtime->unitsymbol',
				   '$p_resourceplannedtime->description' )";

         /* Insert object */
         if ( $this->runSql($sql) )
         {
            /* Get created object id */
            $sql = "SELECT MAX(resourceplannedtimeid) FROM pl_resourceplannedtime";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_resourceplannedtime->resourceplannedtimeid = $row[0];
            else
               return 10041;
         }
         else
            return 10042;
      }
      else
      {
         $sql = "UPDATE pl_resourceplannedtime SET resourceid='$p_resourceplannedtime->resourceid',
                 plannedtime='$p_resourceplannedtime->plannedtime', unitsymbol='$p_resourceplannedtime->unitsymbol',
				 description='$p_resourceplannedtime->description'
                 WHERE resourceplannedtimeid=$p_resourceplannedtime->resourceplannedtimeid";
         /* Update object */
         if ( ! $this->runSql($sql) )
            return 10043;
      }
      return 0;   
   }

  /**
   * Updates or inserts Workedtime into database
   *
   * @param p_workedtime in/out Workedtime, returns updated workedtime
   * @param p_updateonly If true the workedtime is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
/*    function updateWorkedtime(&$p_workedtime, $p_updateonly=true)
   {
      /* Check if organisation is to be inserted */
/*      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO workedtime (activityslotid, userid, startdate, 
                  workedtime )
               VALUES
                  ( '$p_workedtime->activityslotid', '$p_workedtime->userid', '$p_workedtime->startdate',
                     '$p_workedtime->workedtime' )";
     
         /* Insert organisation */
/*         if ( $this->runSql($sql) )
         {
            /* Get created organisation id */
/*            $sql = "SELECT MAX(workedtimeid) FROM workedtime";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_workedtime->workedtimeid = $row[0];
            else
               return 10045;
         }
         else
            return 10046;
      }
      else
      {
         $sql = "UPDATE workedtime SET activityslotid='$p_workedtime->activityslotid', userid='$p_workedtime->userid',
                  startdate='$p_workedtime->startdate', workedtime='$p_workedtime->workedtime'
               WHERE workedtimeid=$p_workedtime->workedtimeid";
         /* Update organisation */
/*         if ( ! $this->runSql($sql) )
            return 10047;
      }
      return 0;      
   }
   */

  /**
   * Updates or inserts Worktime into database
   *
   * @param p_worktime in/out Worktime, returns updated worktime
   * @param p_updateonly If true the worktime is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateWorktime(&$p_worktime, $p_updateonly=true)
   {
      /* Check if object is to be inserted */
      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO pl_worktime (unitid, userid, year, 
                  month, week, day_in_month, 
                  worktime, description )
                  VALUES
                  ( '$p_worktime->unitid', '$p_worktime->userid', '$p_worktime->year',
                    '$p_worktime->month', '$p_worktime->week', '$p_worktime->day_in_month',
                    '$p_worktime->worktime', '$p_worktime->description' )";
     
         /* Insert object */
         if ( $this->runSql($sql) )
         {
            /* Get created object id */
            $sql = "SELECT MAX(worktimeid) FROM pl_worktime";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_worktime->worktimeid = $row[0];
            else
               return 10049;
         }
         else
            return 10050;
      }
      else
      {
         $sql = "UPDATE pl_worktime SET unitid='$p_worktime->unitid', userid='$p_worktime->userid',
                 year='$p_worktime->year', month='$p_worktime->month',
                 week='$p_worktime->week', day_in_month='$p_worktime->day_in_month',
                 worktime='$p_worktime->worktime', description='$p_worktime->description'
                 WHERE worktimeid=$p_worktime->worktimeid";
         /* Update object */
         if ( ! $this->runSql($sql) )
            return 10051;
      }
      return 0;         
   }

  /**
   * Updates or inserts Costdriver into database
   * Checks if costdriver exists, if not it is added
   *
   * @param p_costdriver Costdriver, name of costdrvier
   * @param p_updateonly If true the costdriver is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateCostdriver($p_organisationid, $p_costdriver)
   {
      /* Check if costdriver is to be inserted */
      $costdrivers = $this->getCostdriver($p_organisationid, $p_costdriver);
      foreach ( $costdrivers as $cd )
      {
         if ( $cd == $p_costdriver )
            return 0;
      }
      /* It does not exist in database, so it is added */
      $sql = "INSERT INTO pl_costdriver (organisationid, costdriver, description )
              VALUES
              ( '$p_organisationid', '$p_costdriver', '' )";
     
      /* Insert costdriver */
      if ( !$this->runSql($sql) )
         return 10060;
      return 0;         
   }

  /**
   * Updates or inserts Activityslot into database
   *
   * @param p_activityslot in/out Activityslot, returns updated activityslot
   * @param p_updateonly If true the activityslot is updated, default true   
   * @returns 0 if all ok, else errorcode
   */
   function updateActivityslot(&$p_activityslot, $p_updateonly=true)
   {
      /* Check if activityslot is to be inserted */
      if ( !$p_updateonly )
      {
         $sql = "INSERT INTO pl_activityslot (activityid, startdate, stopdate, 
                 plannedtime, isnotified, unitsymbol, 
				 description )
                 VALUES
                 ( '$p_activityslot->activityid', '$p_activityslot->startdate', '$p_activityslot->stopdate',
                   '$p_activityslot->plannedtime', '$p_activityslot->isnotified', '$p_activityslot->unitsymbol',
				   '$p_activityslot->description' )";

         /* Insert object */
         if ( $this->runSql($sql) )
         {
            /* Get created object id */
            $sql = "SELECT MAX(activityslotid) FROM pl_activityslot";
            $line = $this->runSql($sql);
            if ( $row = $this->getRow($line) )
               $p_activityslot->activityslotid = $row[0];
            else
               return 10037;
         }
         else
         {
            return 10038;
         }
      }
      else
      {
         $sql = "UPDATE pl_activityslot SET activityid='$p_activityslot->activityid',
                 startdate='$p_activityslot->startdate', stopdate='$p_activityslot->stopdate', 
                 plannedtime='$p_activityslot->plannedtime', isnotified='$p_activityslot->isnotified',
                 unitsymbol='$p_activityslot->unitsymbol', description='$p_activityslot->description'
                 WHERE activityslotid=$p_activityslot->activityslotid";
         /* Update object */
         if ( ! $this->runSql($sql) )
            return 10039;
         
      }
      return 0;   
   }

  /**
   * Adds relatedorganisation connection
   * @param orgid_1 Id of one of the related organisations
   * @param orgid_2 Id of the other related organisations
   * @returns 0 if all ok, else errorcode   
   */
   function updateRelatedorganisation($orgid_1, $orgid_2)
   {
      $err = $this->removeRelatedorganisation($orgid_1, $orgid_2);
      if ( $err )
         return $err;
         
      $sql = "INSERT INTO pl_relatedorganisation ( organisationid_1, organisationid_2) VALUES ( $orgid_1, $orgid_2 )";
      if ( !$this->runSql($sql) )
         return 10067;
      
      $sql = "INSERT INTO pl_relatedorganisation ( organisationid_1, organisationid_2) VALUES ( $orgid_2, $orgid_1 )";
      if ( !$this->runSql($sql) )
         return 10067;
         
      return 0;
   }

  /**
   * Removes relatedorganisation connection
   * @param orgid_1 Id of one of the related organisations
   * @param orgid_2 Id of the other related organisations<br>
   * @returns 0 if all ok, else errorcode
   */
   function removeRelatedorganisation($orgid_1, $orgid_2)
   {
      $sql = "DELETE FROM pl_relatedorganisation  WHERE ( organisationid_1=$orgid_1 AND organisationid_2=$orgid_2 ) OR 
                                          ( organisationid_1=$orgid_2 AND organisationid_2=$orgid_1 ) ";
      if ( !$this->runSql($sql) )
         return 10066;
      return 0;
   }
   
  /**
   * Removes organisations with given id from database
   * Removes all costdrives, subunits and users in organisation
   *
   * @param p_organisationid Id of Organisation
   * @returns 0 if all ok, else errorcode
   */
   function removeOrganisation($p_organisationid)
   {
      $p_organisationid = $this->createValues($p_organisationid);         
      /* Remove modules in organisation */
      $sql = "DELETE FROM pl_module_in_organisation WHERE organisationid=$p_organisationid";
      if ( !$this->runSql($sql) )
         return 10005;

      /* Remove costdrivers */
      $sql = "DELETE FROM pl_costdriver WHERE organisationid=$p_organisationid";
      if ( !$this->runSql($sql) )
         return 10006;

      /* Remove related organisation */
      $sql = "DELETE FROM pl_relatedorganisation WHERE organisationid_1=$p_organisationid OR organisationid_2=$p_organisationid";
      if ( !$this->runSql($sql) )
         return 10007;

      /* Remove subunits */
      if ( ($err = $this->removeUnit("SELECT unitid FROM pl_unit WHERE organisationid=$p_organisationid AND parentunitid is null") )!= 0 )
         return $err;

      /* Remove organisation */
      $sql = "DELETE FROM pl_organisation WHERE organisationid=$p_organisationid";
      if ( !$this->runSql($sql) )
         return 10008;
   
      return 0;
   }

  /**
   * Removes all units with given ids from database
´  * Removes all subunits, informationtexts, resources, activities and users in unit
   *
   * @param p_unitids Comma-separated ids of Unit, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeUnit($p_unitids)
   {
      $p_unitids = $this->createValues($p_unitids);      
      /* Get units to remove */
      $sql = "SELECT unitid FROM pl_unit WHERE unitid IN ( $p_unitids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )
      {
         /* Remove subunits */
         $err = $this->removeUnit("SELECT unitid FROM pl_unit WHERE parentunitid=$row[0]");
         if ( $err )
            return $err;

         $err = $this->removeInformationtext("SELECT informationtextid FROM pl_informationtext WHERE unitid=$row[0]");
         if ( $err )
            return $err;

         $err = $this->removeUser("SELECT userid FROM pl_user WHERE head_unitid=$row[0]");
         if ( $err )
            return $err;

         $err = $this->removeResource("SELECT resourceid FROM pl_resource WHERE unitid=$row[0]");
         if ( $err )
            return $err;

         $err = $this->removeActivity("SELECT activityid FROM pl_activity WHERE unitid=$row[0]");
         if ( $err )
            return $err;

         /* Remove the unit itself */
         $sql = "DELETE FROM pl_unit WHERE unitid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10004;
      }
      return 0;
   }

  /**
   * Removes all users with given ids from database
   *
   * @param p_userids Comma-separated ids of User, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeUser($p_userids)
   {
      $p_userids = $this->createValues($p_userids);   

      /* Get users to remove */
      $sql = "SELECT userid FROM pl_user WHERE userid IN ( $p_userids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {
         /* Remove rights for unit */
         $sql ="DELETE FROM pl_right_in_unit WHERE userid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10030;

         /* Remove other units connections */      
         $sql ="DELETE FROM pl_otherunit WHERE userid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10029;

         /* Remove Activitytime */
         $err  = $this->removeActivitytime("SELECT activitytimeid FROM pl_activitytime WHERE userid=$row[0]");
         if ( $err )
            return $err;

         /* Remove worktime */
         $sql ="DELETE FROM pl_worktime WHERE userid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10028;

         /* Remove the user itself */
         $sql ="DELETE FROM pl_user WHERE userid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10031;      
      }
      return 0;      
   }

  /**
   * Removes all Activity with given ids from database
   *
   * @param p_actvitiyuds Comma-separated ids of Activity, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeActivity($p_activityids)
   {
      $p_activityids = $this->createValues($p_activityids);   

      /* Get activities to remove */
      $sql = "SELECT activityid FROM pl_activity WHERE activityid IN ( $p_activityids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {
         /* Remove activityslots */
         $err = $this->removeActivityslot("SELECT activityslotid FROM pl_activityslot WHERE activityid=$row[0]");
         if ( $err )
            return $err;      

         /* Remove the activity itself */
         $sql ="DELETE FROM pl_activity WHERE activityid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10036;               
      }
      return 0;
   }

  /**
   * Removes all Informationtext with given ids from database
   *
   * @param p_informationtextids Comma-separated ids of Informationtext, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeInformationtext($p_informationtextids)
   {
      $p_informationtextids = $this->createValues($p_informationtextids);   

      /* Get users to remove */
      $sql = "SELECT informationtextid FROM pl_informationtext WHERE informationtextid IN ( $p_informationtextids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_informationtext WHERE informationtextid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10032;
      }
      return 0;
   }

  /**
   * Removes all Resource with given ids from database
   *
   * @param p_resourceids Comma-separated ids of Resource, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeResource($p_resourceids)
   {
      $p_resourceids = $this->createValues($p_resourceids);   

      /* Get users to remove */
      $sql = "SELECT resourceid FROM pl_resource WHERE resourceid IN ( $p_resourceids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_resource WHERE resourceid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10070;
      }
      return 0;   
   }

  /**
   * Removes all Activitytime with given ids from database
   *
   * @param p_activitytimeids Comma-separated ids of Activitytime, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeActivitytime($p_activitytimeids)
   {
      $p_activitytimeids = $this->createValues($p_activitytimeids);   

      /* Get plannedtime to remove */
      $sql = "SELECT activitytimeid FROM pl_activitytime WHERE activitytimeid IN ( $p_activitytimeids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_activitytime WHERE activitytimeid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10044;
      }
      return 0;      
   }
   
  /**
   * Removes all Resourceplannedtime with given ids from database
   *
   * @param p_resourceplannedtimeids Comma-separated ids of Resouceplannedtime, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeResourceplannedtime($p_resourceplannedtimeids)
   {
      $p_resourceplannedtimeids = $this->createValues($p_resourceplannedtimeids);   

      /* Get plannedtime to remove */
      $sql = "SELECT resourceplannedtimeid FROM pl_resourceplannedtime WHERE resourceplannedtimeid IN ( $p_resourceplannedtimeids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_resourceplannedtime WHERE resourceplannedtimeid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10044;
      }
      return 0;      
   }   

  /**
   * Removes all Workedtime with given ids from database
   *
   * @param p_workedtimeids Comma-separated ids of Workedtime, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeWorkedtime($p_workedtimeids)
   {
      $p_workedtimeids = $this->createValues($p_workedtimeids);   

      /* Get plannedtime to remove */
      $sql = "SELECT workedtimeid FROM pl_workedtime WHERE workedtimeid IN ( $p_workedtimeids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_workedtime WHERE workedtimeid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10048;
      }
      return 0;      
   }

  /**
   * Removes all Worktime with given ids from database
   *
   * @param p_worktimeids Comma-separated ids of Worktime, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeWorktime($p_worktimeids)
   {
      $p_worktimeids = $this->createValues($p_worktimeids);   

      /* Get plannedtime to remove */
      $sql = "SELECT worktimeid FROM pl_worktime WHERE worktimeid IN ( $p_worktimeids )";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_worktime WHERE worktimeid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10052;
      }
      return 0;      
   }

  /**
   * Removes all Costdriver with given ids from database
   *
   * @param p_organisationid Id of Organisation where costdriver exist
   * @param p_costdrivernames Comma-separated names of Costdriver, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeCostdriver($p_organisationid, $p_costdrivernames)
   {
      $p_costdrivernames = $this->createValues($p_costdrivernames);   

      /* Get plannedtime to remove */
      $sql = "SELECT organisationid, costdriver FROM pl_costdriver 
              WHERE costdriver IN ( $p_costdrivernames ) AND organisationid=$p_organisationid";

      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {   
         $sql = "DELETE FROM pl_costdriver WHERE organisationid=$row[0] AND costdriver='$row[1]'";
         if ( !$this->runSql($sql) )
            return 10059;
      }
      return 0;      
   }

  /**
   * Removes all Activityslot with given ids from database
   *
   * @param p_activityslotids Comma-separated ids of Activityslot, or sql-select
   * @returns 0 if all ok, else errorcode
   */
   function removeActivityslot($p_activityslotids)
   {
      $p_activityslotids = $this->createValues($p_activityslotids);   

      /* Get activities to remove */
      $sql = "SELECT activityslotid FROM pl_activityslot WHERE activityslotid IN ( $p_activityslotids )";
      
      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )      
      {
         /* Remove activitytime */
         $err = $this->removeActivitytime("SELECT activitytimeid FROM pl_activitytime WHERE activityslotid=$row[0]");
         if ( $err )
            return $err;      
            
         /* Remove the activity itself */
         $sql ="DELETE FROM pl_activityslot WHERE activityslotid=$row[0]";
         if ( !$this->runSql($sql) )
            return 10040;               
      }
      return 0;
   }
   
  /**
   * Insert data to jounaltable for activity 
   *
   * @param p_type Type of change (INSERT, DELETE, UPDATE)
   * @param p_activityid Id of activity
   * @param p_activityname Name of activity
   * @param p_userid Id of user that made change
   * @param p_username User name of the user that made change
   */
   function insertActivityJournal($p_type, $p_activityid, $p_activityname, $p_userid, $p_username)
   {
      $sql = "INSERT INTO pl_activity_journal (type_of_change, date, activityid,
              activityname, userid, username )
              VALUES ( '$type', CURDATE(), '$activityid',
              '$activityname', '$userid', '$username')";
      $this->runSql($sql);
   }

  /**
   * Sends mail to a reciepient
   * @param p_subject Subject on mail
   * @param p_message Message content
   * @param p_from From e-mail address
   * @param p_fromname From name
   * @param p_to To e-mail address
   * @param p_toname To name
   * @returns 0 if all ok, 1 error
   */
   function sendMail($p_subject, $p_message, $p_from, $p_fromname, $p_to, $p_toname)
   {
      $err = 0;
      mail($p_to, $p_subject, $p_message, "From: ".$p_fromname." <$p_form>") or $err = 1;
      return $err;
   }

  /**
   * Create a textstring that forms a PageFactoryclass-file 
   * with data from module 
   *
   * @returns String with data that can build a PageFactoryclass-file
   */
   function createPageFactoryFile()
   {
      $retstr = "<?php
include_once ('AbstractPageFactory.class.php');
include_once ('IPageControl.class.php');
include_once ('ErrorControl.php');
";
/*      $sql = "SELECT moduleid, modulename, supplier, version, description, defaultpagename FROM module";
      $line = $this->runSql($sql);
      while ( $row = $this->getRow($line) )
      { */
         $sql = "SELECT filename, filetypename, version, pagename FROM pl_modulefile WHERE filetypename='PAGFILE'";
         $line2 = $this->runSql($sql);
         while ( $row2 = $this->getRow($line2) )
         {
            $retstr = $retstr."
include_once ('$row2[0]');";
         }
//	  }
      $retstr = "$retstr
class PageFactory extends AbstractPageFactory {
   var "."$"."id;
   var "."$"."page;
   function createPage("."$"."p_id, "."$"."p_page)
   {
      "."$"."this->id = "."$"."p_id;
      "."$"."this->page = "."$"."p_page;
      return "."$"."this->createObject();
   }

   function createObject()
   {
      switch ("."$"."this->page)
      {
";
         $sql = "SELECT filename, filetypename, version, pagename FROM pl_modulefile WHERE filetypename='PAGFILE'";
         $line2 = $this->runSql($sql);
         while ( $row2 = $this->getRow($line2) )
         {
            $controlname = substr($row2[0], strrpos($row2[0], "/")+1);
            $controlname = substr($controlname, 0, -4);
            $retstr = "$retstr
         case '$row2[3]':
            return new $controlname();
            break;
         ";
      }
      $retstr = "$retstr
         default:
            break;
      }
   }
}
?>
";
      return $retstr;      
   }
}
?>