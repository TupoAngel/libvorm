<?php

class DualHost extends Model {
  protected $table = 'host';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('name', String (32));
  }
}

class DualEvent extends Model {
  protected $table = 'event';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('type', String (64));
    $this->Field ('description', String (256));
    $this->Field ('host_id', Integer (), ForeignKey ('DualHost'));
  }
}

class DualSession extends Session {
  public function __construct ($uri) {
    parent::__construct ($uri);

    $this->AddModel (new DualHost ());
    $this->AddModel (new DualEvent ());

    $this->Init ();
  }
}

class DualModelTests extends UnitTestCase {
  private $uri = 'sqlite://localhost/test.db';
  private $s = null;
  private $m = null;

  private function init () {
    $this->s = new DualSession ($this->uri);
    $this->m = new stdClass ();
    $this->m->host = $this->s->GetModel ('DualHost');
    $this->m->event = $this->s->GetModel ('DualEvent');
  }
  
  public function test_direct_relationship () {
    $this->Init ();

    $res = $this->s->query ($this->m->host->name, $this->m->event->type, $this->m->event->description)->all ();
    $this->AssertEqual ($res->queryString,
			"select {$this->m->host->name}, {$this->m->event->type}, {$this->m->event->description} from {$this->m->host->table} inner join {$this->m->event->table} on {$this->m->host->id} = {$this->m->event->host_id}");
  }

  public function test_inverse_relationship () {
    $this->Init ();

    $res = $this->s->query ($this->m->event->type, $this->m->event->description, $this->m->host->name)->all ();
    $this->AssertEqual ($res->queryString,
			"select {$this->m->event->type}, {$this->m->event->description}, {$this->m->host->name} from {$this->m->event->table} inner join {$this->m->host->table} on {$this->m->event->host_id} = {$this->m->host->id}");
  }

  function test_or_filter () {
    $this->init ();

    $res = $this->s->query ($this->m->host->name, $this->m->event->type, $this->m->event->description)->filter (or_ ("{$this->m->event->type} = 'Type1'",
														     "{$this->m->event->type} = 'Type2'"));
    $this->AssertEqual ($res->queryString,
			"select {$this->m->host->name}, {$this->m->event->type}, {$this->m->event->description} from {$this->m->host->table} inner join {$this->m->event->table} on {$this->m->host->id} = {$this->m->event->host_id} where ({$this->m->event->type} = 'Type1' or {$this->m->event->type} = 'Type2')");
  }

  function test_mixed_filter () {
    $this->init ();

    $res = $this->s->query ($this->m->host->name, $this->m->event->type, $this->m->event->description)->filter (and_ ("{$this->m->host->name} = 'Teste'", 
                                                                                                                 or_ ("{$this->m->event->type} = 'Type1'",
                                                                                                                      "{$this->m->event->type} = 'Type2'")));
    $this->AssertEqual ($res->queryString,
			"select {$this->m->host->name}, {$this->m->event->type}, {$this->m->event->description} from {$this->m->host->table} inner join {$this->m->event->table} on {$this->m->host->id} = {$this->m->event->host_id} where {$this->m->host->name} = 'Teste' and ({$this->m->event->type} = 'Type1' or {$this->m->event->type} = 'Type2')");
  }
}
?>
