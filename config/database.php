<?php

  /* Database access details and connection functions
   */

  /**
   * Change DB user name/host/password here for Rivendell as necessary
   * @return array with username, password & hostname
   */
  function getDBDetails(){

    $DB = Array();
    $DB['username'] = 'rduser';
    $DB['password'] = 'letmein';
    $DB['hostname'] = 'localhost';
    $DB['database'] = 'Rivendell';

    return $DB;

  }


  /**
   * Gets a PDO connection to MySQL
   * @return PDO MySQL connection, set this to NULL to close connection
   */
  function getPDOMySQL(){

    $PDO = NULL;

    $DB = getDBDetails();

    try{

      $PDO = new PDO('mysql:host=' . $DB['hostname'] . ';dbname='
          . $DB['database'], $DB['username'], $DB['password']);

      //Set Exception mode
      $PDO->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

    }catch(PDOException $e){

      die('Error connecting to database: ' . $e->getMessage());

    }

    return $PDO;

  }

?>
