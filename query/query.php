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

class Query extends BaseQuery {
  private $tables = array ();
  private $models = array ();
  private $limit = null;
  private $filter = null;
  private $order = null;

  public function __construct (Session $session, $fields) {
    parent::__construct ($session, $fields);

    foreach ($fields as $field) {
      if (!in_array ($field->table, $this->tables)) {
	$this->tables[] = $field->table;
	$this->models[] = $session->GetModelFor ($field->table);
      }
    }
  }

  // query preparation functions
  public function prepare () {
    $models = $this->models;
    $main = array_shift ($models);

    // initial select statement
    $sql = 'select '.implode (', ', $this->fields).' from '.$main->table;

    $rels = $this->session->FindRelations ($main, $models);

    foreach ($rels as $rel)
      $sql .= $rel;

    if ($this->filter)
      $sql .= $this->filter;

    if ($this->order)
      $sql .= $this->order;

    if ($this->limit)
      $sql .= " limit {$this->limit}";

    return $sql;
  }
  
  public function one () {
    $this->limit = 1;

    return $this->session->execute ($this);
  }

  public function all () {
    return $this->session->execute ($this);
  }

  public function filter () {
    $filter = ' where ';
    foreach (func_get_args () as $flt)
      $filter .= "{$flt}";

    $this->filter = $filter;

    return $this->session->execute ($this);
  }

  public function order () {
    $order = ' order by ';
    foreach (func_get_args () as $ord)
      $order .= "{$ord}, ";
    $order = substr ($order, 0, -2);

    $this->order = $order;
    return $this;
  }

  function GetModels () {
    return ($this->models);
  }

  function GetFields () {
    return $this->fields;
  }
}
