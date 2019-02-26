<?php
/*
*  Hello World client
*  Connects REQ socket to tcp://localhost:5555
*  Sends "Hello" to server, expects "World" back
* @author Ian Barber <ian(dot)barber(at)gmail(dot)com>
*/

include __DIR__."/ZMQSubscriber.php";

$subscriber = new ZMQSubscriber("tcp://localhost:5555");

$subscriber->on("10 ", function($message){
   var_dump("Message 10 has successfully arrived ($message)");
});

$subscriber->on("11 ", function ($message){
    var_dump("Congratulation #11 has come $message");
});

$subscriber->listen();