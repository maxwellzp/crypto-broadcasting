<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\BinanceWebSocketService;

#[AsCommand(
    name: 'app:binance-websocket',
    description: 'Add a short description for your command',
)]
class BinanceWebsocketCommand extends Command
{
    private BinanceWebSocketService $webSocketService;
    public function __construct(BinanceWebSocketService $webSocketService)
    {
        parent::__construct();
        $this->webSocketService = $webSocketService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $output->writeln('Listener...');

        $this->webSocketService->listen();
        return 0;
    }
}
