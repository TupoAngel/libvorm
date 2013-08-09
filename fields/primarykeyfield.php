<?php

class PrimaryKeyField extends FieldConstraint {
  function __construct () {
    parent::__construct ('primary key');
  }
}
