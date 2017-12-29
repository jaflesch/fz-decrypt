<?php
class FZExportDecrypt {
	private $file;
	private $content;

	public function __construct() {
		global $argv;
		if(count($argv) < 2) die('Error');
		
		$this->file = simplexml_load_file($argv[1]);
		$this->content = "";
	}

	public function exportXML() {
		$servers = array();

		foreach ($this->file->Servers as $value) {
			foreach($value->Server as $server)
				$servers[] = $server;

			foreach($value->Folder as $key => $value) {
				foreach($value->Server as $server_folder) {
					$servers[trim($value)][] = $server_folder;
				}
			}
		}

		$output = "";

		foreach ($servers as $key => $value) {
			if(!is_array($value)) {
				$this->content .= $this->formatXMLData($value);
			}
			else {
				$this->content .= "***************************************\r\n";
				$this->content .= "* ".$key." Folder \r\n";
				$this->content .= "***************************************\r\n";

				foreach ($servers[$key] as $key => $value) {
					$this->content .= $this->formatXMLData($value);
				}
			}
		}

		$this->outputFile();
	}

	private function formatXMLData($node) {
		$string = "";
		$string .= "Name: ".trim($node->Name)."\r\n";
		$string .= "Host: ".trim($node->Host)."\r\n";
		$string .= "Port: ".trim($node->Port)."\r\n";
		$string .= "User: ".trim($node->User)."\r\n";
		$string .= "Pass: ".base64_decode(trim($node->Pass))."\r\n";
		
		$string .= trim($node->Comment) != "" ? "Comments: ".trim($node->Comment) : "";
		$string .= "\r\n\r\n";

		return $string;
	}

	private function outputFile() {
		global $argv;
		$filename = isset($argv[2]) ? $argv[2] : 'output.txt';
		$handle = fopen($filename, 'a+');
		fwrite($handle, $this->content);
		fclose($handle);
	}
}