<?php

class AutoIncrementField extends FieldType {
	public function __construct () {
		parent::__construct ('integer unsigned auto_increment');
	}
}