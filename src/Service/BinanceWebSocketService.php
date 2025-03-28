<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use WebSocket\Client;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

// https://developers.binance.com/docs/derivatives/usds-margined-futures/websocket-market-streams
class BinanceWebSocketService
{
    private Client $client;
    public function __construct(
        private HubInterface $hub,
        private LoggerInterface $logger
    )
    {
    }

    private string $wsUrl = "wss://fstream.binance.com/stream?streams=btcusdt@trade/ethusdt@trade/bnbusdt@trade";

    public function listen(): void
    {
        try {
            $this->client = new Client($this->wsUrl);

            /*
             * {"stream":"ethusdt@trade","data":{"e":"trade","E":1743166595666,"T":1743166595666,"s":"ETHUSDT","t":5404710166,"p":"1889.80","q":"0.128","X":"MARKET","m":true}}
             * {"stream":"btcusdt@trade","data":{"e":"trade","E":1743166595259,"T":1743166595258,"s":"BTCUSDT","t":6141979216,"p":"85118.90","q":"0.002","X":"MARKET","m":false}}
             * {"stream":"bnbusdt@trade","data":{"e":"trade","E":1743166663179,"T":1743166663178,"s":"BNBUSDT","t":1649099777,"p":"628.900","q":"0.01","X":"MARKET","m":true}}
             */
            while (true) {
                $message = $this->client->receive();

                $data = json_decode($message->getContent(), true);

                if (array_key_exists('data', $data) && $data['data'] != null) {
                    $tradeData = $data['data'];
                    $this->broadcastTradeUpdate($tradeData['s'], $tradeData['p'], $tradeData['q'], $tradeData['t']);
                }
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        } finally {
            $this->client->close();
        }
    }

    /**
     * @param string $market
     * @param string $price
     * @param string $quantity
     * @param int $timestamp
     * @return void
     */
    public function broadcastTradeUpdate(string $market, string $price, string $quantity, int $timestamp): void
    {
        $update = new Update(
            'binance/trades',
            json_encode([
                'market'   => $market,
                'price'    => $price,
                'quantity' => $quantity,
                'timestamp' => $timestamp
            ])
        );

        $this->hub->publish($update);
    }
}
