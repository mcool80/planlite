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
 * Class Organisation
 * Contains information about organisation
 * @author Markus Svensson
 * @version 1.01
 */
class Organisation 
{
   /** Id of the organisation in database */
   var $organisationid;
   /** Organisationname */
   var $organisationname;
   /** Description */
   var $description;
   /** The highest number of users a organisation can have */
   var $no_user;
   /** Address */
   var $address;
   /** Zipcode */
   var $zipcode;
   /** City */
   var $city;
   /** Phonenumbers */
   var $phonenumbers;
   /** Contact name */
   var $contact;
   /** Organisationtypeid */
   var $organisationtypeid;
   /** Organisationtypename */
   var $organisationtypename;
   
   /** Related organisations (private) */
   var $relatedorganisation = array();
   /** Organisation unit (private) */
   var $organisationunit;
   /** Modules used in organisation (private) */
   var $module = array();

  /**
   * Constructor
   * Gets organisationtypename from database
   *
   * @param p_organisationid Id of organisation
   * @param p_organisationname Name of organisation
   * @param p_description Description about organisation
   * @param p_no_users Number of users allowed in organisation
   * @param p_address Address for organisation
   * @param p_zipcode Zip-code for organisation
   * @param p_city City for organisation
   * @param p_contact Contact name
   * @param p_phonenumbers Phone number to contact/organisation
   * @param p_organisationtypeid Organisation type id
   * @returns Organisation object
   */
   function Organisation($p_organisationid, $p_organisationname, $p_description, $p_no_users, $p_address, 
                     $p_zipcode, $p_city, $p_contact, $p_phonenumbers, $p_organisationtypeid)
   {
      global $dc;

      $this->organisationid = $p_organisationid;
      $this->organisationname = $p_organisationname;
      $this->description = $p_description;
      $this->no_users = $p_no_users;
      $this->address = $p_address;                     
      $this->zipcode = $p_zipcode;
      $this->city = $p_city;
      $this->phonenumbers = $p_phonenumbers;
      $this->contact = $p_contact;
      $this->organisationtypeid = $p_organisationtypeid;
      
      /* Get organisationtypename */
      $arr = $dc->getOrganisationtype($this->organisationtypeid);
      $this->organisationtypename = $arr[$this->organisationtypeid];
      
      /* Get organisation unit */
      $this->getOrganisationUnit();
   }
   
  /**
   * Adds a related organisation to organisation
   *
   * @param p_organisation Organisation to add as related organistion
   * @returns 0 if all ok else an errorcode
   */
   function addRelatedOrganisation($p_organisation)
   {
      /* Add organisation */
      array_push($this->relatedorganisation, $p_organisation);
      return 0;
   }

  /**
   * Gets all related organisation to organisation
   *
   * @returns 0 if all ok else an errorcode
   */
   function getRelatedOrganisation()
   {
      global $dc;
      /* Get organisations from database */
      $this->relatedorganisation = $dc->getOrganisation("SELECT DISTINCT organisationid_1 FROM pl_relatedorganisation WHERE organisationid_2='$this->organisationid'");
      return 0;
   }   
   
  /**
   * Gets organistion unit for thís organisation
   *
   * @returns Organisation unit (Unit)
   */
   function getOrganisationUnit()
   {
      global $dc;
      /* Check if organisation unit is fetched from database */
      if ( !is_object($this->organisationunit) )
      {
         /* Fetch organisation unit from database and add to organisation */
         $units = $dc->getUnit("SELECT unitid FROM pl_unit WHERE organisationid='$this->organisationid' AND parentunitid is null");
         if ( sizeof($units) > 0 )
            $this->organisationunit = array_pop($units);
      }
      /* Return organisation unit */   
      return $this->organisationunit;
   }
   
  /**
   * Get modules used in the organisation
   *
   * @returns Array of Module
   */
   function getModules()
   {
      global $dc;
      /* Fetch modules from database and add to organisation */
      $this->module = $dc->getModule("SELECT moduleid FROM pl_module_in_organisation WHERE organisationid='$this->organisationid'");

      /* Return modules */      
      return $this->module;
   }
   
  /**
   * Gets administrator user for organisation
   *
   * @returns Administrator user (User)
   */
   function getOrganisationAdmin()
   {
      $this->getOrganisationunit();
      $users = $this->orgaisationunit->getUsers();
      foreach ( $users as $user )
      {
         if ( $user->isAdmin($this->organisationunit->unitid) )
            return $user;
      }
   }

  /**
   * Add  module to organisation
   *
   * @param p_module Module object
   */
   function addModule($p_module)
   {
      array_push($this->module, $p_module);
   }

  /**
   * Gets costdrivers in organisation
   *
   * @returns Array of String
   */
   function getCostdrivers()
   {
      global $dc;
      return $dc->getCostdriver($this->organisationid);
   }
   
  /**
   * Check if organisation can add more users, due to licens
   * @returns true if a new user can be added.
   */
   function canAdduser()
   {
      $orgunit = $this->getOrganisationUnit();
      $totnumusers = $orgunit->getTotNumOfUsers();
      if ( $totnumusers < $this->no_users )
         return true;
      return false;
   }
}
?>