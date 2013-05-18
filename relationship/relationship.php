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

abstract class Relationship {
  protected $from = null;
  protected $to = null;
  protected $type = null;
  protected $from_fields = null;
  protected $to_fields = null;

  public function __construct (Model $from, Model $to, $type = 'inner') {
    $this->from = $from;
    $this->to = $to;
    $this->type = $type;
  }

  public function __ToString () {
    $join_condition = '';
    for ($i = 0; $i < count($this->from_fields); $i++)
      $join_condition .= "{$this->from->{$this->from_fields[$i]}} = {$this->to->{$this->to_fields[$i]}}";
      
    return ' '.$this->type.' join '.$this->to->table.' on '.$join_condition;
  }
}
