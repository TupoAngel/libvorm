<?php

class IndirectHost extends Model {
  protected $table = 'host';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('name', String (32));
  }
}

class IndirectEvent extends Model {
  protected $table = 'event';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('type', String (64));
    $this->Field ('description', String (256));
    $this->Field ('host_id', Integer (), ForeignKey ('IndirectHost'));
  }
}

class IndirectLog extends Model {
  protected $table = 'log';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('level', String (16));
    $this->Field ('message', String (512));
    $this->Field ('event_id', Integer (), ForeignKey ('IndirectEvent'));
  }
}

class IndirectSession extends Session {
  public function __construct ($uri) {
    parent::__construct ($uri);

    $this->AddModel (new IndirectHost ());
    $this->AddModel (new IndirectEvent ());
    $this->AddModel (new IndirectLog ());

    $this->Init ();
  }
}

class IndirectModelTests extends UnitTestCase {
  private $uri = 'sqlite://localhost/test.db';
  private $s = null;
  private $m = null;

  private function init () {
    $this->s = new IndirectSession ($this->uri);
    $this->m = new stdClass ();
    $this->m->host = $this->s->GetModel ('IndirectHost');
    $this->m->event = $this->s->GetModel ('IndirectEvent');
    $this->m->log = $this->s->GetModel ('IndirectLog');
  }

  public function test_indirect_relationship_all () {
    $this->Init ();

    $res = $this->s->query ($this->m->host->name, $this->m->log->level, $this->m->log->message)->all ();
    $this->AssertEqual ($res->queryString,
			"select {$this->m->host->name}, {$this->m->log->level}, {$this->m->log->message} from {$this->m->host->table} inner join {$this->m->event->table} on {$this->m->host->id} = {$this->m->event->host_id} inner join {$this->m->log->table} on {$this->m->event->id} = {$this->m->log->event_id}");
  }

  public function test_indirect_reverse_relationship_all () {
    $this->Init ();

    $res = $this->s->query ($this->m->log->level, $this->m->log->message, $this->m->host->name)->all ();
    $this->AssertEqual ($res->queryString,
			"select {$this->m->log->level}, {$this->m->log->message}, {$this->m->host->name} from {$this->m->log->table} inner join {$this->m->event->table} on {$this->m->log->event_id} = {$this->m->event->id} inner join {$this->m->host->table} on {$this->m->event->host_id} = {$this->m->host->id}");
  }
}
?>
