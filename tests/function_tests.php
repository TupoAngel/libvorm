<?php

require_once (dirname(__FILE__).'/../src/vorm.php');

class FunctionTests extends UnitTestCase {
  function test_array_union_simple () {
    $a = array ('Bola');
    $b = array ('Biscoito');

    $c = array_union ($a, $b);
    $d = array_union ($b, $a);

    $this->AssertEqual (count ($c), 2);
    $this->AssertEqual (count ($d), 2);
    $this->AssertEqual ($c[0], 'Bola');
    $this->AssertEqual ($c[1], 'Biscoito');
    $this->AssertEqual ($d[0], 'Biscoito');
    $this->AssertEqual ($d[1], 'Bola');
  }

  function test_array_union_repeated_vals_in_b () {
    $a = array ('Bala', 'Bombom');
    $b = array ('Bombom', 'Chiclete');

    $c = array_union ($a, $b);
    $d = array_union ($b, $a);

    $this->AssertEqual (count ($c), 3);
    $this->AssertEqual (count ($d), 3);
    $this->AssertEqual ($c[0], 'Bala');
    $this->AssertEqual ($c[1], 'Bombom');
    $this->AssertEqual ($c[2], 'Chiclete');
    $this->AssertEqual ($d[0], 'Bombom');
    $this->AssertEqual ($d[1], 'Chiclete');
    $this->AssertEqual ($d[2], 'Bala');
  }
}