<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\String\u;

final class FontsController extends AbstractController
{
    private const array FONTS = [
        'mansalva',
        'alex-brush',
        'covered-by-your-grace',
        'architects-daughter',
        'bilbo',
        'babylonica',
        'annie-use-your-telescope',
        'grand-hotel',
    ];

    #[Route('/fonts.css', name: 'app_fonts')]
    public function index(): Response
    {
        $font = self::FONTS[array_rand(self::FONTS)];

        return new Response($this->renderView('fonts/fonts.css.twig', [
            'font_identifier' => $font,
            'font_name' => u($font)->replace('-', ' ')->title(true)->toString(),
        ]), Response::HTTP_OK, ['Content-Type' => 'text/css; charset=UTF-8']);
    }
}
