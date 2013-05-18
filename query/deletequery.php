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

class DeleteQuery extends ModelQuery {
  public function prepare () {
    $sql = 'delete from '.$this->model->table;

    $filter = array ();
    foreach ($this->fields as $field) {
      if ($field->value !== null)
	$filter[] = "{$field->name} = ".$field->type->store ($field->value);
    }

    $sql .= ' where '.and_ ($filter).';';

    return $sql;
  }
}
