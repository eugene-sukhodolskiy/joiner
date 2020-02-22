<?php

$time_updated = 1;
$history = [];

class Joiner{
	protected $directive_join = "@join:";
	protected $directive_repeat = "@repeat:";
	protected $directive_repeat_end = "@end";
	public $analize_file;
	public $base_path;

	public function __construct($analize_file){
		$this -> analize_file = str_replace("\\", "/", $analize_file);
		$this -> base_path = str_replace(basename($this -> analize_file), '', $this -> analize_file);
	}

	public function start(){
		$file_content = $this -> get_file($this -> analize_file);
		$file_content = $this -> analize_and_repeat($file_content);
		$result_content = $this -> analize_and_join($file_content);
		return $result_content;
	}

	public function analize_and_join($content){
		$divide = explode($this -> directive_join, $content);
		foreach ($divide as $i => $part) {
			list($join_with) = explode(";", $part);
			$join_with = trim($join_with);

			if(!strlen($join_with)){
				continue;
			}

			$join_with_full = $this -> base_path . $join_with;
			$result_content = (new Joiner($join_with_full)) -> start();
			$content = str_replace($this -> directive_join . $join_with . ';', $result_content, $content);
		}

		return $content;
	}

	public function analize_and_repeat($content){
		$divide = explode($this -> directive_repeat, $content);
		foreach ($divide as $i => $part){
			$repeat_count = substr($divide[$i], 0, strpos($divide[$i], ';'));
			if(!intval($repeat_count) and $repeat_count != '0'){
				continue;
			}

			$divide[$i] = str_replace($repeat_count . ";\n", '', $divide[$i]);
			$repeat_content = substr($divide[$i], 0, strpos($divide[$i], $this -> directive_repeat_end . ';'));
			$divide[$i] = str_replace($repeat_content . $this -> directive_repeat_end . ";\n", '', $divide[$i]);

			for($j=0; $j<intval($repeat_count); $j++){
				$divide[$i] = $repeat_content . $divide[$i];
			}
		}

		$content = implode('', $divide);
		return $content;
	}

	public function get_file($analize_file){
		if(!file_exists($analize_file)){
			return '';
		}

		global $time_updated;
		$time_updated += filemtime($analize_file);
		global $history;
		$history[] = $analize_file;

		echo "Join file {$analize_file}\n";
		return file_get_contents($analize_file);
	}
}

function watch($input_file, $output_file){
	global $time_updated, $history;
	$calc_time_updated = 0;
	while(true){
		$calc_time_updated = 0;
		foreach($history as $file){
			$calc_time_updated += filemtime($file);
		}

		if($calc_time_updated != $time_updated){
			$history = [];
			$time_updated = 0;
			single_join($input_file, $output_file);
		}

		sleep(2);
	}
}

function single_join($input_file, $output_file){
	if(file_put_contents($output_file, (new Joiner($input_file)) -> start())){
		echo "Maked {$output_file}\n\n";
	}else{
		echo "Failed!\n";
	}
}

function process(){
	global $argv;
	$input_file = $argv[1];
	$output_file = $argv[2];
	if(isset($argv[3]) and $argv[3] == 'watch'){
		watch(__DIR__ . '/' . $input_file, __DIR__ . '/' . $output_file);
	}else{
		single_join(__DIR__ . '/' . $input_file, __DIR__ . '/' . $output_file);
	}
}

process();