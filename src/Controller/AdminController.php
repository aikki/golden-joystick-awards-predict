<?php

namespace App\Controller;

use App\Entity\Award;
use App\Form\DashboardFormType;
use App\Repository\AwardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/access/{admin_token}', name: 'app_access')]
    public function access(
        #[MapEntity(mapping: ['admin_token' => 'admin_key'])] Award $award,
        Request $request,
    ): Response
    {
        $response = $this->redirectToRoute('app_admin_dashboard');
        $response->headers->setCookie(
            Cookie::create('dashboard_access', $award->getAdminKey())
                ->withExpires(new \DateTime('+1 month'))
                ->withPath('/dashboard')
                ->withSecure($request->isSecure())
                ->withHttpOnly(true)
                ->withSameSite(Cookie::SAMESITE_LAX)
        );

        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }

    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(Request $request, AwardRepository $awardRepository, EntityManagerInterface $entityManager): Response
    {
        $award = $awardRepository->findDefault();

        $form = $this->createForm(DashboardFormType::class, $award);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($award);
                $entityManager->flush();

                return $this->redirectToRoute('app_admin_dashboard');
            }
        }

        return $this->render('admin/index.html.twig', [
            'award' => $award,
            'form' => $form->createView(),
        ]);
    }
}
