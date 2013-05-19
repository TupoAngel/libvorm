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

class Model implements Iterator {
  private $fields = array ();
  protected $table = null;
  private $constraints = array ();
  private $references = array ();
  private $referers = array ();

  public function __construct () {
    if ($this->table == null)
      $this->table = strtolower (get_class ($this));

    if (method_exists ($this, 'init'))
      $this->init ();
  }

  /* Relationship helper functions */
  public function References (Model $model) {
    return in_array (get_class ($model), $this->references);
  }

  public function GetReferences () {
    return $this->references;
  }

  public function GetReferers () {
    return $this->referers;
  }

  public function ReferencedBy (Model $model) {
    $this->referers[] = get_class ($model);
  }

  /* Field Functions */
  public function Field ($name, FieldType $type , FieldConstraint $constraint = null , $comment = null) {

    $this->fields[$name] = new Field ($name, $type, $constraint, $this->table, $comment);
    if (is_a ($constraint, 'ForeignKeyField'))
      $this->references[] = $constraint->GetModelName ();
  }

  public function SetFields ($fields) {
    $this->fields = $fields;
  }

  public function GetPrimaryKey () {
    return array_filter ($this->fields, function ($item) { return is_a ($item->constraint, 'PrimaryKeyField'); });
  }

  public function GetForeignKeyFor (Model $model) {
    $name = get_class ($model);
    return array_filter ($this->fields, function ($item) use ($name) { return is_a ($item->constraint, 'ForeignKeyField') && 
	  $item->constraint->GetModelName () == $name; });
  }

  public function __get ($prop) {
    if (isset ($this->$prop))
      return $this->$prop;

    if (!array_key_exists ($prop, $this->fields))
      throw new Exception ("Field '{$prop}' not defined for model ".get_class ($this));
    return $this->fields[$prop]->value === null ?
      $this->fields[$prop] :
      $this->fields[$prop]->type->retrieve ($this->fields[$prop]->value);
  }

  public function __set ($name, $value) {
    if (!array_key_exists ($name, $this->fields))
      throw new Exception ("Field '{$name}' not defined for model ".get_class ($this));
    $this->fields[$name]->value = $value;
  }

  // Iterator implementation
  public function rewind () {
    reset ($this->fields);
  }

  public function current () {
    return current ($this->fields);
  }

  public function key () {
    return key ($this->fields);
  }

  public function next () {
    return next ($this->fields);
  }

  public function valid () {
    $key = key($this->fields);
    return ($key !== null && $key !== false);
  }
}
