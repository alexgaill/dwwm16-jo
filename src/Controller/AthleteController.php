<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Form\AthleteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AthleteController extends AbstractController
{
    #[Route('/athlete', name: 'athlete')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $athlete = new Athlete;
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            $photoName = $athlete->getNom() . "-" . $athlete->getPrenom() . "." . $photo->guessExtension();
            $athlete->setPhoto($photoName);
            $photo->move($this->getParameter('upload_profil'), $photoName);

            $em = $manager->getManager();
            $em->persist($athlete);
            $em->flush();
            $this->addFlash('success', "Athlete inscrit");
            return $this->redirectToRoute('athlete');
        }

        return $this->render('athlete/index.html.twig', [
            'athleteList' => $manager->getRepository(Athlete::class)->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/athlete/update/{id}', name:'athlete_update')]
    public function update(Athlete $athlete, ManagerRegistry $manager, Request $request) :Response
    {
        $athlete->setPhoto(
            new File($this->getParameter('upload_profil') .'/' . $athlete->getPhoto())
        );
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            $photoName = $athlete->getNom() . "-" . $athlete->getPrenom() . "." . $photo->guessExtension();
            $athlete->setPhoto($photoName);
            $photo->move($this->getParameter('upload_profil'), $photoName);

            $em = $manager->getManager();
            $em->persist($athlete);
            $em->flush();
            $this->addFlash('success', "Athlete modifié");
            return $this->redirectToRoute('athlete');
        }

        return $this->render('athlete/update.html.twig', [
            'athleteList' => $manager->getRepository(Athlete::class)->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route('ahtlete/delete/{id}', name:'athlete_delete')]
    public function delete (Athlete $athlete, ManagerRegistry $manager): Response
    {
        $em = $manager->getManager();
        $em->remove($athlete);
        $em->flush();
        $this->addFlash('success', "Athlete désinscrit");
        return $this->redirectToRoute('athlete');
    }
}
