<?php

  //DELETE TABLE + RULES
  function deleteClockTables($PDO, $clockName){
    
    //replace whitespace with _ and sanitise names
    $clockName = str_replace(' ', '_', trim($clockName));
    $clkTable = escapeTableName($PDO, $clockName . '_CLK');
    $rulesTable = escapeTableName($PDO, $clockName . '_RULES');
    
    $sql = 'DROP TABLE ';
    
    if($PDO->query($sql . $clkTable) === FALSE)
      die('Error dropping CLK table: ' . $clkTable . ' ' . $PDO->errorInfo());
    
    if($PDO->query($sql . $rulesTable) === FALSE)
      die('Error dropping RULES table: ' . $rulesTable . ' ' . $PDO->errorInfo());
    
  }

  //DELETE FROM CLOCKS AND CLOCK_PERMS
  function deleteClock($PDO, $clockName){
    
    $sql = 'DELETE FROM CLOCKS WHERE NAME = :name';
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':name', $clockName);
    
    if($stmt->execute() === FALSE)
      die('Could not delete from CLOCKS table: ' . $stmt->errorInfo());
    
    $stmt = NULL;
    
    $sql = 'DELETE FROM CLOCK_PERMS WHERE CLOCK_NAME = :name';
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':name', $clockName);
    
    if($stmt->execute() === FALSE)
      die('Could not delete from CLOCK_PERMS table: ' . $stmt->errorInfo());
    
    $stmt = NULL;
    
  }
    
?>