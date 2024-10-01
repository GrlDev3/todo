<?php 
class Task
{
    private $id;
    private $title;
    private $timestamp;


    public function __construct($id, $title){
        $this->id = $id;
        $this->title = $title;
        $this->timestamp = date("Y-m-d H:i:s");
    }

    public function getTaskData(){
        return ['id' => $this->id, 'title' => $this->title,'timestamp'=> $this->timestamp];
    }

    public static function validateTitle($title){
        return !empty($title);
    }
}


?>