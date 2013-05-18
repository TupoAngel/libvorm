#!/usr/bin/php -f
<?php

require_once 'simpletest/autorun.php';

class AllTests extends TestSuite {
  function __construct () {
    parent::__construct ();
    $this->TestSuite ('All VORM Tests');
    $this->AddFile ('function_tests.php');
    $this->AddFile ('session_tests.php');
    $this->AddFile ('model_tests.php');
    $this->AddFile ('query_return_tests.php');
  }
}
?>
