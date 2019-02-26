<?php


$context = new ZMQContext(1);

var_dump($context);

//  Socket to talk to clients
$responder = new ZMQSocket($context, ZMQ::SOCKET_PUB);
$responder->bind("tcp://*:5555");

$i=0;
while (true) {
    //  Wait for next request from client
    $responder->send("EVENT HERE! YOo");

   $i++;

    //  Send reply back to client
    $responder->send("19 World #$i");
    $responder->send("10 World #$i");
    $responder->send("11 World #$i");
    $responder->send("12 World #$i");
    $responder->send("14 World #$i");
    $responder->send("18 World #$i");
}