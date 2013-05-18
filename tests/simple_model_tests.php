<?php

class SimpleHost extends Model {
  protected $table = 'host';

  public function Init () {
    $this->Field ('id', Integer (), PrimaryKey ());
    $this->Field ('name', String (32));
  }
}

class SimpleSession extends Session {
  public function __construct ($uri) {
    parent::__construct ($uri);

    $this->AddModel (new SimpleHost ());

    $this->Init ();
  }
}

class SimpleModelTests extends UnitTestCase {
  private $uri = 'sqlite://localhost/test.db';
  private $s = null;
  private $m = null;

  private function init () {
    $this->s = new SimpleSession ($this->uri);
    $this->m = new stdClass ();
    $this->m->host = $this->s->GetModel ('SimpleHost');
  }
  
  function test_host_query_all () {
    $this->init ();

    $s = $this->s;
    $host = $this->m->host;

    $res = $s->query ($host)->all ();
    $this->AssertEqual ($res->queryString,
    			"select {$host->id}, {$host->name} from {$host->table}");
  }

  function test_host_query_one () {
    $this->init ();

    $res = $this->s->query ($this->m->host)->one ();
    $this->AssertEqual ($res->queryString,
  			"select {$this->m->host->id}, {$this->m->host->name} from {$this->m->host->table} limit 1");
  }

  function test_insert () {
    $this->init ();

    $h = $this->m->host;

    $host = new SimpleHost ();
    $host->name = 'ney';

    $res = $this->s->save ($host);
    $this->s->commit ();
    $this->AssertEqual ($res->queryString,
  			"insert into {$h->table} ({$h->name->name}) values ('ney');");
  }

  function test_update () {
    $this->init ();
    $h = $host = $this->s->GetModel ('SimpleHost');

    $res = $this->s->query ($host)->filter ("{$host->name} = 'ney'");
    $host = $res->fetch ();
    $host->name = 'joao';
    $res = $this->s->save ($host);
    $this->AssertEqual ($res->queryString,
  			"update {$h->table} set {$h->name->name} = 'joao' where {$h->id} = {$host->id}");
  }

  function test_delete () {
    $this->init ();
    $h = $this->s->GetModel ('SimpleHost');

    $host = new SimpleHost ();
    $host->name = 'ney';

    $res = $this->s->delete ($host);
    $this->AssertEqual ($res->queryString,
  			"delete from {$h->table} where {$h->name->name} = 'ney';");

    $host->name = 'joao';
    $res = $this->s->delete ($host);
    $this->AssertEqual ($res->queryString,
  			"delete from {$h->table} where {$h->name->name} = 'joao';");
  }

  function test_filter () {
    $this->init ();

    $res = $this->s->query ($this->m->host->name)->filter ("{$this->m->host->name} = 'Teste'");
    $this->AssertEqual ($res->queryString,
			"select {$this->m->host->name} from {$this->m->host->table} where {$this->m->host->name} = 'Teste'");
  }
}
?>
