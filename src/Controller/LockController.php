<?php

namespace App\Controller;

use App\Repository\AwardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LockController extends AbstractController
{
    #[Route('/locked', name: 'app_locked')]
    public function index(AwardRepository $awardRepository): Response
    {
        if ($awardRepository->findDefault() !== null) {
            return $this->redirectToRoute('app_voting');
        }

        return $this->render('lock/index.html.twig');
    }
}
