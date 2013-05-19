<?php
/*
 *  Copyright © 2011 Estêvão Samuel Procópio
 *
 *  This file is part of libvorm.
 *
 *  Libvorm is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2, or (at your option)
 *  any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 */

class Field {
  private $name = null;
  private $type = null;
  private $comment = null;
  private $table = null;
  private $constraint = null;
  private $value = null;

  public function __construct ($name, FieldType $type, $constraint, $table, $comment) {
    $this->name = $name;
    $this->type = $type;
    $this->comment = $comment;
    $this->constraint = $constraint;
    $this->table = $table;
  }

  public function __get ($name) {
    if (isset ($this->$name))
      return $this->$name;
  }

  public function __set ($name, $value) {
    if ($name != 'value')
      throw new Exception ("Trying to set an invalid variable: {$name}");

    if ($value === '') $value = null;

    $this->$name = $value;
  }

  function label ($alias) {
    return "{$this->table}.{$this->name} as {$label}";
  }

  public function __ToString () {
    if (is_a ($this->constraint, 'CalculatedField'))
      return '('.$this->constraint->GetFormula ().") as {$this->name}";
    return "{$this->table}.{$this->name}";
  }
}

?>