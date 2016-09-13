<?php
class iphp_MW_Master
{
	public $link = null;
	private $mw = null;
	private $name = '';
	
	function __construct($mw){
		global $argv;
		$this->name = basename($argv[0]);
		$this->mw = $mw;
	}

	function init($ip, $port){
		$this->link = new iphp_MW_Link();
		$this->link->connect($ip, $port);
		$this->link->send('role', 'master');
		$resp = $this->link->recv();
		if(!$resp){
			throw new Exception("manager gone");
		}
		if($resp['type'] == 'ok'){
			//Logger::debug("[{$this->name}] master started");
		}else{
			throw new Exception("bad response");
		}
	}
	
	function add_job($job){
		$ret = $this->link->send('job', $job);
		if(!$ret){
			throw new Exception("manager gone, failed to add job");
		}
	}
	
	function wait(){
		//Logger::debug("[{$this->name}] send wait");
		$ret = $this->link->send('wait');
		if(!$ret){
			throw new Exception("manager gone, failed to wait");
		}
		$resp = $this->link->recv();
		if(!$resp){
			throw new Exception("manager gone");
		}
		if($resp['type'] == 'ok'){
			//Logger::debug("[{$this->name}] wait return ok");
		}else{
			Logger::debug("[{$this->name}] wait return error: " . json_encode($resp));
		}
	}

	function run($manager){
		$this->init($manager->link->ip, $manager->link->port);
		$this->mw->master();
		$this->wait();
		//Logger::debug("[{$this->name}] master quit");
	}
}
