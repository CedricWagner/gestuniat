<?php

namespace AppBundle\Utils;

class CSVGenerator
{

	private $name;
	private $lines;

	function __construct() {
	}

    public function setName($name=''){

    	$this->name = $name;

        return $this;
    }

    public function addLine($line=array()){

    	$this->lines[]= $line;

        return $this;
    }

    public function getHeaders($addtime=true){
    	$datetime = new \DateTime();
    	
		$filename = $this->name;
    	
    	if($addtime){
    		$filename.= '_'.$datetime->format('YmdHis');
    	}

    	$filename.= '.csv';

    	return array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        );
    }

    public function generateContent(){

    	$handle = fopen('php://memory', 'r+');
    	foreach ($this->lines as $line) {
    		fputcsv($handle, $line);
    	}

    	rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }


}