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
 * Class Date
 * Handles date and date formating
 * @author Markus Svensson
 * @version 1.01
 */
class DatePlanlite
{
   /** Id of activity in database */
   var $year;
   /** Id of unit that holds activity */
   var $month;   
   /** Activityname */
   var $day;
   /** Description */
   var $days_in_month = array(null,31, 28, 31, 30, 31, 30, 31, 31, 30, 31,30 , 31);
   
   /**
    * Constructor, creates DatePlanlite
    *
    * @param p_date Date in format YYYY-MM-DD
    */
    function DatePlanlite($p_date)
   {
      $this->setDate($p_date);
   }
   
  /**
   * Set date 
   * @param p_date Date in format YYYY-MM-DD
   */
   function setDate($p_date)
   {
      $this->year = (int)substr($p_date, 0, 4);
      $this->month = (int)substr($p_date, 5, 2);
      $this->day = (int)substr($p_date, 8, 2);
   }

  /**
   * Adds months to date
   * @param p_months Number of months to add
   */
   function addMonth($p_months)
   {
      $this->month += $p_months;
      if ( $this->month > 12 )
         $this->year += floor($this->month / 12);
      $this->month = ($this->month % 13);
      if ( $this->month == 0 )
         $this->month = 1;
   }

  /**
   * Adds years to date
   * @param p_years Number of years to add
   */
   function addYear($p_years)
   {
      $this->year += $p_years;
   }

  /**
   * Adds days to date
   * @param p_days Number of days to add
   */
   function addDay($p_days)
   {
      $days = $p_days;
      $leapyear = 0;
      if ( $this->isLeapyear() && $this->month == 2 ) 
         $leapyear = 1;
      $days = $days - ( $this->days_in_month[$this->month] + $leapyear - $this->day);
      while ( $days > 0 )
      {
         $this->addMonth(1);
         if ( $this->isLeapyear() && $this->month == 2 ) 
            $leapyear = 1;
         else 
            $leapyear = 0;            
         $days = $days - ( $this->days_in_month[$this->month] + $leapyear );
      }
      $this->day = ( $this->days_in_month[$this->month] + $leapyear ) + $days;
   }
   
  /**
   * Check if the year is a leap year
   * @returns true if year is leap year
   */
   function isLeapyear()
   {
      if ( $this->year % 4 == 0 &&
         (!$this->year % 100 == 0 ||
           $this->year % 400 == 0 ) )
         return true;
      return false;
   }
      
  /**
   * Get week number 
   */
   function getWeek()
   {
      $timestamp = mktime(0, 0, 0,$this->month, $this->day, $this->year);
      
      $week = date("W", $timestamp);
/*      if ( $week == 1 && $this->month == 12 )
         return 53;
      if ( $week > 50 && $this->month == 1 )
         return 1; */
      return $week;
   }
  /** 
   * Get weekday number, 0=sunday, 6=saturday
   */
   function getWeekday()
   {
      $timestamp = mktime(0, 0, 0,$this->month, $this->day, $this->year);
      
      return date("w", $timestamp);
   }
   
  /**
   * Get number of weeks this year
   */
   function getWeeksInYear()
   {
//      $timestamp = mktime(0, 0, 0,12, 31, $this->year);
//      return strftime("%W", $timestamp);
       // If january starts on monday then there are 365/7 weeks = 52.14 weeks => 53 weeks
       return 53;
   } 
   
  /**
   * Get date in format YYYY-MM-DD
   * @returns Date in format YYYY-MM-DD
   */
   function getDate()
   {
      $prefixmon = "";
      $prefixday = "";
      if ( $this->month < 10 ) $prefixmon = "0";
      if ( $this->day < 10 ) $prefixday = "0";       
      return $this->year."-".$prefixmon.$this->month."-".$prefixday.$this->day;
   }
  /**
   * Set first day of current week
   */
   function setFirstDayInweek()
   {
      $timestamp = mktime(0, 0, 0,$this->month, $this->day, $this->year);
      $dayofweek = strftime("%u", $timestamp);
      $timestamp = $timestamp - ($dayofweek-1)*24*3600;
      $this->year = strftime("%Y", $timestamp);
      $this->month = intval(strftime("%m", $timestamp));
      $this->day = intval(strftime("%d", $timestamp));
   }

  /**
   * Set last day of current week
   */
   function setLastDayInweek()
   {
      $timestamp = mktime(0, 0, 0,$this->month, $this->day, $this->year);
      $dayofweek = strftime("%u", $timestamp);
      if ( $dayofweek != 7 )
         $this->addDay(7-$dayofweek);
   }
     
}
?>