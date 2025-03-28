<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TradeController extends AbstractController
{
    #[Route('/', name: 'app_trade')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
