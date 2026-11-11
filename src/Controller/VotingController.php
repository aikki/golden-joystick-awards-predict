<?php

namespace App\Controller;

use App\Entity\Voter;
use App\Form\VotingFormType;
use App\Repository\AwardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VotingController extends AbstractController
{
    #[Route('/', name: 'app_voting', methods: ['GET', 'POST'])]
    public function index(Request $request, AwardRepository $awardRepository, EntityManagerInterface $entityManager): Response
    {
        $award = $awardRepository->findDefault();
        if ($award === null) {
            throw $this->createNotFoundException();
        }

        if ($award->isClosed()) {
            return $this->redirectToRoute('app_voting_ranking');
        }

        $voter = Voter::create($award);
        $form = $this->createForm(VotingFormType::class, $voter, ['award' => $award ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($voter);
                $entityManager->flush();

                return $this->redirectToRoute('app_voting_result', ['id' => $voter->getId(), 'show_token' => $voter->getShowToken()]);
            }
        }

        return $this->render('voting/index.html.twig', [
            'award' => $award,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/result/{id}/{show_token}', name: 'app_voting_result', methods: ['GET'])]
    public function result(
        #[MapEntity(mapping: ['id' => 'id', 'show_token' => 'show_token',])] Voter $voter,
        Request $request, AwardRepository $awardRepository): Response
    {
        return $this->render('voting/result.html.twig', [
            'voter' => $voter,
        ]);
    }

    #[Route('/ranking', name: 'app_voting_ranking', methods: ['GET'])]
    public function ranking(AwardRepository $awardRepository): Response
    {
        $award = $awardRepository->findDefault();

        return $this->render('voting/ranking.html.twig', [
            'award' => $award,
        ]);
    }
}
