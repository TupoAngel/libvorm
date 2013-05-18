<?php

class QueryHost extends Model {
  protected $table = 'host';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('name', String (32));
  }
}

class QuerySession extends Session {
  public function __construct ($uri) {
    parent::__construct ($uri);

    $this->AddModel (new QueryHost ());
    $this->AddModel (new QueryEvent ());

    $this->Init ();
  }
}

class QueryEvent extends Model {
  protected $table = 'event';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('type', String (64));
    $this->Field ('description', String (256));
    $this->Field ('host_id', Integer (), ForeignKey ('QueryHost'));
  }
}

class QueryReturnTests  extends UnitTestCase {
  private $uri = 'sqlite://localhost/test.db';
  private $s = null;
  private $m = null;

  private function init () {
    $this->s = new QuerySession ($this->uri);
    $this->m = new stdClass ();
    $this->m->host = $this->s->GetModel ('QueryHost');
    $this->m->event = $this->s->GetModel ('QueryEvent');
  }

  public function test_single_model () {
    $this->Init ();

    $s = $this->s;
    $host = $this->m->host;

    $res = $s->query ($host)->all ();
    $this->AssertEqual (get_class ($res->fetch ()), 'QueryHost');
  }

  public function test_various_models () {
    $this->Init ();

    $s = $this->s;
    $host = $this->m->host;
    $event = $this->m->event;

    $res = $s->query ($host->name, $event->type)->all ();
    $this->AssertEqual (get_class ($res->fetch ()), 'QueryModel');
  }
}
