<?php
class phParticle{
	private $titolo;
	private function dt($epoch){
		 return gmdate("Y-m-d\TH:i:s\Z",$epoch);
    }
    private function path(){
        $proto;
        if($_SERVER["HTTPS"]==""||$_SERVER["HTTPS"]=="off"){
            $proto="http://";
        }
        else{
            $proto="https://";
        }
        $url=$proto.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $page=explode('/',$url);
        return str_replace($page,'',$url);
    }
    private function pageURL(){
        $proto;
        if($_SERVER["HTTPS"]==""||$_SERVER["HTTPS"]=="off"){
            $proto="http://";
        }
        else{
            $proto="https://";
        }
        $url=$proto.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        return $url;
    }
    private $parserFunc;
    private $urlGenFunc;
    private $icon;
    private $defaultIcon="https://raw.githubusercontent.com/eutampieri/phParticle/master/phParticle.png";
	function __construct($t,$_f,$_p,$i=null){
        if($i==null){
            $i=$this->defaultIcon;
        }
		$this->titolo=$t;
        $this->urlGenFunc=$_f;
        $this->parserFunc=$_p;
        $this->icon=$i;
	}
	function feed($articoli){
		$feed='<?xml version="1.0" encoding="utf-8"?>'."\n".'<feed xmlns="http://www.w3.org/2005/Atom">'."\n";
        $feed=$feed."\t<title>".$this->titolo."</title>\n";
        $feed=$feed."\t\t<link rel=\"self\" href=\"".$this->pageURL()."\"/>\n";
        $feed=$feed."\t<icon>".$this->icon."</icon>\n";
        $feed=$feed."\t<updated>".$this->dt(time())."</updated>\n";
        $feed=$feed."\t".'<generator uri="https://github.com/eutampieri/phParticle" version="0">phParticle</generator>'."\n";
        $md=md5($_SERVER["SERVER_NAME"]);
        $crc=0;
        for($i=0;$i<strlen($md);$i++){
            $crc=$crc+ord($md[$i]);
        }
        $feed=$feed."\t<id>tag:".$_SERVER["SERVER_NAME"].",".strval(($crc%200)+1900).":feed-".strval($crc)."</id>\n";
        $url=$this->urlGenFunc;
        $parser=$this->parserFunc;
        foreach($articoli as $articolo){
            $feed=$feed."\t<entry>\n";
            $feed=$feed."\t\t<title>".$articolo["title"]."</title>\n";
            $feed=$feed."\t\t<link href=\"".$url($articolo["url"],$this->path())."\"/>\n";
            $feed=$feed."\t\t<author><name>".$articolo["author"]."</name></author>\n";
            $feed=$feed."\t\t<id>tag:".$_SERVER["SERVER_NAME"].",".strval(($crc%200)+1900).":article-".$articolo["date"]."</id>\n";
            $feed=$feed."\t\t<updated>".$this->dt($articolo["date"])."</updated>\n";
            $feed=$feed."\t\t<summary type=\"html\">".htmlspecialchars($articolo["content"])."</summary>\n";
            $feed=$feed."\t</entry>\n";
        }
        $feed=$feed."</feed>\n";
        return $feed;
	}
};