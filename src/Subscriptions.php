<?php

namespace nostriphant\Relay;

use \nostriphant\NIP01\Transmission;
use nostriphant\NIP01\Message;
use nostriphant\NIP01\Event;
use nostriphant\Stores\Engine\Memory\Condition;
use nostriphant\Stores\Conditions;

class Subscriptions {

    private static array $subscriptions = [];

    public function __construct(private Transmission $relay, private \Psr\Log\LoggerInterface $log) {
        
    }
    
    public static function reset() {
        self::$subscriptions = [];
    }

    public function __invoke(mixed ...$args): mixed {
        return match (true) {
            $args[0] instanceof Transmission => self::countFor($args[0]),
            $args[0] instanceof Event => self::apply($args[0], $this->log),
            is_string($args[0]) && count($args) === 1 => self::unsubscribe($args[0]),
            is_string($args[0]) => self::subscribe($this->relay, $args[0], $args[1]),
        };
    }

    static function apply(Event $event, \Psr\Log\LoggerInterface $log): mixed {
        $log->debug("Applying to " . $event->id);
        array_find(self::$subscriptions, function (callable $subscription, string $subscriptionId) use ($event, $log) {
            $to = $subscription($event);
            if ($to === false) {
                $log->debug("Matched subscription " . $subscriptionId . ', but recipient is missing');
                return false;
            }
            $log->debug("Matched subscription " . $subscriptionId);
            $to(Message::event($subscriptionId, $event));
            $to(Message::eose($subscriptionId));
            return true;
        });
        yield Message::ok($event->id, true, '');
    }

    static function countFor(Transmission $client) : int {
        return count(array_filter(self::$subscriptions, fn(callable $subscription) => new \ReflectionFunction($subscription)->getClosureUsedVariables()['relay']));
    }
    
    static function subscribe(Transmission $relay, string $subscription_id, array $filter_prototypes): void {
        $test = Condition::makeConditions(new Conditions($filter_prototypes));
        self::$subscriptions[$subscription_id] = fn(Event $event) => $test($event) ? $relay : fn() => false;
    }

    static function unsubscribe(string $subscription_id): void {
        unset(self::$subscriptions[$subscription_id]);
    }
}
