<?php

class ZMQSubscriber
{
    /**
     * @var ZMQSocket
     */
    private $listener;

    private $eventListeners = [];

    public function __construct($dsn)
    {
        $context = new ZMQContext();
        $this->listener = new ZMQSocket($context, ZMQ::SOCKET_SUB);
        $this->listener->connect($dsn);
    }

    public function listen()
    {
        while(1)
        {
            $message = $this->listener->recv();
            $r = $this->handle($message);

            if($r === false)
            {
                return ;
            }
        }
    }

    public function on($prefix, callable $func, $name = null)
    {
        $this->listener->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $prefix);

        if(!isset($this->eventListeners[$prefix]))
        {
            $this->eventListeners[$prefix] = [];
        }

        if(!$name)
        {
            $this->eventListeners[$prefix][] = $func;
        }
        else
        {
            $this->eventListeners[$prefix][$name] = $func;
        }
    }

    public function off($prefix, $name)
    {
        if(isset($this->eventListeners[$prefix][$name]))
        {
            unset($this->eventListeners[$prefix][$name]);
        }

        if(count($this->eventListeners[$prefix])==0)
        {
            $this->listener->setSockOpt( ZMQ::SOCKOPT_UNSUBSCRIBE, $prefix );
        }
    }

    public function clear($prefix)
    {
        $this->eventListeners[$prefix] = [];
        $this->listener->setSockOpt( ZMQ::SOCKOPT_UNSUBSCRIBE, $prefix );
    }

    public function handle($message)
    {
        $prefixes = array_keys($this->eventListeners);

        foreach ($prefixes as $prefix)
        {
            if(strncmp($message, $prefix, strlen($prefix)) === 0)
            {
                foreach ($this->eventListeners[$prefix] as $func)
                {
                    $func($message);
                }
            }
        }
    }
}