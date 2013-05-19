<?php

class RequiredField extends FieldConstraint {
  function __construct () {
    parent::__construct ('not null');
  }
}