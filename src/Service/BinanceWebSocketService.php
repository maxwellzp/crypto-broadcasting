<?php

namespace App\Service;

use WebSocket\Client;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

// https://developers.binance.com/docs/derivatives/usds-margined-futures/websocket-market-streams
class BinanceWebSocketService
{
    private HubInterface $hub;
    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    private string $wsUrl = "wss://fstream.binance.com/ws/btcusdt@trade";

    public function listen(): void
    {
        try {
            $client = new Client($this->wsUrl);

            while (true) {
                echo "--------------" . PHP_EOL;
                $message = $client->receive();
//                echo 'Message = ' .  $message . PHP_EOL;
//                echo "Got message: {$message->getContent()} \n";
                $data = json_decode($message->getContent(), true);


                if ($data) {
                    echo "Price: " .
                        $data['p'] .
                        " | Quantity: "
                        . $data['q'] . PHP_EOL;
                }

                // Broadcast trade update
                $update = new Update(
                    'binance/trades',
                    json_encode([
                        'symbol'   => 'BTCUSDT',
                        'price'    => $data['p'],
                        'quantity' => $data['q'],
                        'timestamp' => (new \DateTime())->format('c')
                    ])
                );

                $this->hub->publish($update);

                echo "Broadcasted Trade: " . $data['p'] . " | " . $data['q'] . PHP_EOL;
            }

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
        }
    }
}
