<?php
class Page {
    var $title, $stylesheets = array() , $javascripts = array() , $body ;
      //Contructor
    public function __construct() {
    }
    public function setTitle($title){
        $this->title = $title;
    }
    public function addCSS($path){
        array_push($this->stylesheets, $path);
    }
    public function addJavascript($path){
        array_push($this->javascripts, $path);
    }
    // Start and End boy Captute
    
    public function startBody(){
         ob_start(); //stores output to a buffer instead of sending it to the user's browser
    }
    public function endBody(){
         $this->body = ob_get_clean();//gets the contents of the buffer and then deletes the buffer.
    }
    //Render the Page
    public function render($path){
        ob_start();
        include($path);
        return ob_get_clean();
    }
}
