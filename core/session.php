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

class Session {
  private $conn = null;
  private $tablemap = array ();

  // Database sync functions // some depends on metadata information
  public function create () {
    foreach ($this->models as $model)
      $this->CreateModel ($model);
  }

  protected function init () {
    foreach ($this->models as $model) {
      foreach ($model->GetReferences () as $ref) {
	$m = $this->GetModel ($ref);
	$m->ReferencedBy ($model);
      }
    }
  }

  /**
    * Cria uma tabela para um objeto Model informado.
    *
    * Agora é uma função public, permitindo que o programador crie tabelas individualmente
    */
  public function CreateModel ($model) {

    if(gettype($model) == 'string') {
      ErrorHandler::error(
        array(
          'title' => 'Erro ao criar tabela para o Model',
          'description' => 'Este erro acontece quando a função de criação de tabela do model recebe uma string em vez do objeto Model em si.'
        )
      );
    }

    $sql = "\ncreate table {$model->table} (\n";

    $i = 0;
    $len = count($model->fields);

    foreach ($model as $field) {

      $sql .= " {$field->name} ";
	  
      $sql .= " {$field->type}\t";

      // Se o campo atual contiver constraints
      if ($field->constraint) {
        $sql .= " {$field->constraint}";
      }
	  
	  if($field->comment && !is_a($field->constraint, 'ForeignKeyField')) {
        $sql .= " comment '{$field->comment}' ";
      }

      // Finaliza a linha
      if($i < $len - 1) {
        $sql .= ",\n";
      }

      $i++;

    } // end foreach

    $sql .= ')';

    var_dump($sql);

  }

  // Model management stuff
  private $models = array ();

  protected function AddModel (Model $model) {
    $name = get_class ($model);
    $this->tablemap[$model->table] = $name;
    $this->models[$name] = $model;
  }

  public function GetModel ($name) {

    if (!array_key_exists ($name, $this->models)) {

      ErrorHandler::error (
        array(
          'title' => 'Erro: o model ' . $name . ' não foi definido para esta Session',
        )
      );

    }

    return $this->models[$name];
  }

  public function GetModelFor ($table) {
    if (!$table) return null;
    return $this->models[$this->tablemap[$table]];
  }

  // Initialization functions
  public function __construct ($uri) {

    if(empty($uri)) {
        ErrorHandler::error(
          array(
            'title' => 'Erro ao tentar se conectar a uma fonte de dados via PDO',
            'description' => 'Este erro acontece quando você não define uma URI para conexão ao PDO (PHP Data Objects). Veja o que você pode fazer:',
            'instructions' => array(
              'Verificar o arquivo de configuração' => 'Verifique se você não se esqueceu de informar a URI de conexão no arquivo de configuração.',
              'Atualizar a biblioteca libvorm' => 'Você pode estar utilizando uma versão antiga do vorm. Atualize para obter mais segurança e estabilidade.',
            ),
            'objectToWatch' => array(
              'Estado atual da sessão PDO' => $this
            )
          )
        );
    }

    $objURI = Session::uri_get_object ($uri);

    $pdo_string = Session::pdo_get_connection_string ($objURI);

    if(!isset($objURI->user) || !isset($objURI->pass)) {
      ErrorHandler::error(
          array(
            'title' => 'Erro: falta definir um usuário e/ou uma senha para conexão ao banco de dados',
            'description' => 'Este erro acontece quando você não define um usuário e/ou uma senha para conexão ao PDO (PHP Data Objects).',
            'instructions' => array(
              'Verifique o URI de conexão' => 'Verifique se você não se esqueceu de definir o campo de usuário como user=algumacoisa e pelo menos declarou o pass=',
            ),
            'objectToWatch' => array(
              'Objeto relacionado ao URI de conexão' => $objURI,
              'String do PHP Data Objects' => $pdo_string,
            ),
          )
      );
    }

    try {
      $this->conn = new PDO ($pdo_string, $objURI->user, $objURI->pass);
      $this->conn->SetAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch ( PDOException $Exception ) {
      ErrorHandler::error(
        array(
          'title' => 'Erro '. $Exception->getCode() .' ao utilizar o PDO para se conectar a uma fonte de dados',
          'description' => 'Este erro acontece quando há algum problema com a URI de conexão. Veja o que pode ser feito para corrigir o problema:', 
          'instructions' => array(
            'Confira a sintaxe da URI de conexão' => 'A URI de conexão costuma estar com a seguinte sintaxe: bancodedados://servidor/bancodedados?user=usuario&pass=senha',
            'Confira o usuário e a senha' => 'Sua conexão pode ter sido recusada por um erro ao informar o usuário ou senha, ou por falta de permissões.',
          ),
          'objectToWatch' => array(
            'Descrição do erro fornecida pelo PHP' => $Exception->getMessage(),
            'Estado da sessão' => $this,
          ),
        )
      );
    }

    unset ($objURI->pass);

    /* $this->LoadMetaInfo ($objURI->scheme); */
  }

  function uri_get_object ($uri) {
    $obj = (object)parse_url ($uri);
    $objQuery = explode('&', $obj->query);
    $objQueryArray = array();

    foreach($objQuery as $key => $value) {
      $a = explode('=', $value);
      $objQueryArray[$a[0]] = $a[1];
    }

    foreach($objQueryArray as $key => $value) {
      $obj->$key = $value;
    }

    if ($obj->scheme == 'sqlite')
      $obj->user = $obj->pass = null;

    return $obj;
  }

  function pdo_get_connection_string ($obj) {
    if ($obj->scheme == 'sqlite') {
      $obj->path = substr ($obj->path, 1);
      return "{$obj->scheme}:{$obj->path}";
    }
    $obj->path = substr ($obj->path, 1);
    return "{$obj->scheme}:host={$obj->host};dbname={$obj->path}";
  }

  public function connect (Model $model) {
    foreach ($model as $field)
      if (is_a ($field->type, 'ConnectedFieldType'))
	$field->type->SetConnection ($this->conn);
  }

  // Query execution functions
  public function execute (BaseQuery $query) {
    $sql = $query->prepare ();

    if (!$this->conn->InTransaction ()) $this->conn->BeginTransaction ();

    $ret = $this->conn->Query ($sql);

    if (is_a ($query, 'Query')) {
      $models = $query->GetModels ();
      if (count ($models) == 1) {
	$class = get_class ($models[0]);
	$ret->SetFetchMode (PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $class);
      } else {
	$fields = array ();
	foreach ($query->GetFields () as $field)
	  $fields[$field->name] = $field;
	$ret->SetFetchMode (PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'QueryModel', array ($fields));
      }
    }

    return $ret;
  }

  public function query () {
    $flds = array ();
    $args = func_get_args ();
    foreach ($args as $a) {
      if (is_a ($a, 'Model')) {
	foreach ($a->fields as $f) {
	  $flds[] = $f;
	}
      } elseif (is_a ($a, 'Field')) {
	$flds[] = $a;
      } else {
	throw new Exception ('Session::Query: Invalid parameter');
      }
    }

    return new Query ($this, $flds);
  }

  // Data modification functions
  public function save (Model $model) {
    $type = 'update';
    foreach ($model->GetPrimaryKey () as $name=>$field) {
      if ($field->value === null) {
	$type = 'insert';
	break;
      }
    }

    if ($type == 'insert')
      return $this->execute (new InsertQuery ($this, $model));
    else
      return $this->execute (new UpdateQuery ($this, $model));
  }

  public function delete (Model $model) {
    return $this->execute (new DeleteQuery ($this, $model));
  }


  /* Relation helper functions */
  public function FindRelations ($main, $models) {
    $rels = array ();
    foreach ($models as $model) {
      if (!$model) continue; //Calculated fields has no models
      if ($main->References ($model))
	$rels[get_class ($model)] = new DirectRelationship ($main, $model);
      elseif ($model->References ($main))
	$rels[get_class ($model)] = new InverseRelationship ($main, $model);
      else {
	$int = array_intersect (array_union ($main->GetReferences (), $main->GetReferers ()),
				array_union ($model->GetReferences (), $model->GetReferers ()));
	if (count ($int) != 1) {
	  throw new Exception ('Session::FindRelations: multiple possibilities for indirect relationship. Please check.');
	}

	$int = $this->GetModel (array_shift ($int));
	if ($main->References ($int) && $int->References ($model)) {
	  $rels[get_class ($int)] = new DirectRelationship ($main, $int);
	  $rels[get_class ($model)] = new DirectRelationship ($int, $model);
	} elseif ($main->References ($int) && $model->References ($int)) {
	  $rels[get_class ($int)] = new DirectRelationship ($main, $int);
	  $rels[get_class ($model)] = new InverseRelationship ($int, $model);
	} elseif ($int->References ($main) && $int->References ($model)) {
	  $rels[get_class ($int)] = new InverseRelationship ($main, $int);
	  $rels[get_class ($model)] = new DirectRelationship ($int, $model);
	} elseif ($int->References ($main) && $model->References ($int)) {
	  $rels[get_class ($int)] = new InverseRelationship ($main, $int);
	  $rels[get_class ($model)] = new InverseRelationship ($int, $model);
	} else {
	  throw new Exception ('FindRelations: invalid indirect relationship possibility. Please check.');
	}

	/* var_dump (get_class ($main), $main->GetReferences (), $main->GetReferers ()); exit; */
	/* foreach ($models as $model) */
	/*   var_dump (get_class ($model), $model->GetReferences (), $model->GetReferers ()); */
      }
	//throw new Exception ('Keep going on!!!');
    }

    return $rels;
  }

  // Transaction management
  public function commit () {
    if ($this->conn->InTransaction ()) $this->conn->commit ();
  }

  public function rollback () {
    if ($this->conn->InTransaction ()) $this->conn->rollback ();
  }

  /* // metainfo definitions */
  /* /\* TODO: mover para subclasse *\/ */
  /* private $metainfo = null; */

  /* private function LoadMetaInfo ($scheme) { */
  /*   $file = dirname (__file__)."/{$scheme}.meta"; */
  /*   if (is_file ($file)) */
  /*     $this->metainfo = simplexml_load_file ($file); */
  /* } */

  /* function __call ($name, $args) { */
  /*   $class = get_class ($this); */
  /*   if (!isset ($this->metainfo->$name)) */
  /*     throw new Exception ("Call to undefined method {$class}::{$name}()"); */
  /*   $parms = array_merge (array ($this->metainfo->$name), $args); */
  /*   return $this->query (call_user_func_array ('sprintf', $parms)); */
  /* } */
}
