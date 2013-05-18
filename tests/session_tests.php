<?php

require_once (dirname(__FILE__).'/../src/vorm.php');

class SessionTests extends UnitTestCase {
  private $uri = 'sqlite://localhost/test.db';

  function test_uri_to_object () {
    $obj = Session::uri_get_object ($this->uri);

    $this->AssertEqual ($obj->scheme, 'sqlite');
    $this->AssertEqual ($obj->host, 'localhost');
    $this->AssertEqual ($obj->user, null);
    $this->AssertEqual ($obj->pass, null);
    $this->AssertEqual ($obj->path, '/test.db');
  }

  function test_uri_to_pdo () {
    $obj = Session::uri_get_object ($this->uri);
    $pdo = Session::pdo_get_connection_string ($obj);

    $this->AssertEqual ($pdo, 'sqlite:test.db');
  }

  function test_session () {
    $sess = new Session ($this->uri);
  }
}
?>
