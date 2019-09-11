<?php
namespace Yeah\Fw\Event;

interface EventHandlerInterface {
    public function handle(Event $event);
}