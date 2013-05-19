<?php

class UnsignedIntegerField extends FieldType {
	public function __construct () {
		parent::__construct ('integer unsigned');
	}
}