<?php
namespace Yeah\Fw\Event;

class EventDispatcher {
    private $handlers = array(
        0 => array(),
        1 => array(),
        2 => array(),
        3 => array(),
        4 => array(),
        5 => array(),
        6 => array(),
        7 => array(),
        8 => array(),
        9 => array(),
        10 => array(),
        11 => array()
    );

        /**
     * Adds simple interface for registering event handlers
     * @param integer $event Choose one of the predefined events:
     * - PRE_ROUTING 0
     * - POST_ROUTING 1
     * - PRE_SECURITY 2
     * - POST_SECURITY 3
     * - PRE_CACHE 4
     * - POST_CACHE 5
     * - PRE_ACTION 6
     * - POST_ACTION 7
     * - PRE_RENDER 8
     * - POST_RENDER 9
     * - PRE_REPONSE_CACHE 10
     * - POST_REPONSE_CACHE 11
     * @param \\Closure|\\Yeah\\Fw\\Event\\EventHandlerInterface. Handler to be executed when registered event fires
     * @param integer $priority Determines the queue for the handler execution
     */
    public function register($event, $handler, $priority) {
        $this->handlers[$event][$priority] = $handler;
    }

    public function remove($event, $handler) {
        $tmp = $this->handlers[$event];

        foreach($this->handlers[$event] as $priority => $h) {
            if($h === $handler) {
                unset($tmp[$priority]);
            }
        }

        $this->handlers[$event] = $tmp;
    }

    public function dispatch(Event $event) {
        foreach($this->handlers[$event->getEvent()] as $handler) {
            $handler->handle($event);
        }
    }
}
