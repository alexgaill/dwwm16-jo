<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Form\PaysType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaysController extends AbstractController
{
    #[Route('/pays', name: 'pays')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $pays = new Pays;
        $form = $this->createForm(PaysType::class, $pays);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $drapeau = $form->get('drapeau')->getData();
            $pays->setDrapeau(pathinfo($drapeau->getClientOriginalName(), PATHINFO_FILENAME).".". $drapeau->guessExtension());
            $drapeau->move($this->getParameter('upload_drapeau'), $pays->getDrapeau());
            $em = $manager->getManager();
            $em->persist($pays);
            $em->flush();
            $this->addFlash('success', 'Enregistrement Réussi');
            return $this->redirectToRoute('pays');
        }

        return $this->render('pays/index.html.twig', [
            'paysList' => $manager->getRepository(Pays::class)->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/pays/update/{id}', name:'pays_update')]
    public function update (Pays $pays, ManagerRegistry $manager, Request $request):Response
    {
        $pays->setDrapeau(new File($this->getParameter('upload_drapeau').'/'. $pays->getDrapeau()));
        $form = $this->createForm(PaysType::class, $pays);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $drapeau = $form->get('drapeau')->getData();
            $pays->setDrapeau(pathinfo($drapeau->getClientOriginalName(), PATHINFO_FILENAME).".". $drapeau->guessExtension());
            $drapeau->move($this->getParameter('upload_drapeau'), $pays->getDrapeau());
            $em = $manager->getManager();
            $em->persist($pays);
            $em->flush();
            $this->addFlash('success', 'Pays modifié');
            return $this->redirectToRoute('pays');
        }

        return $this->render('pays/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route("/pays/delete/{id}", name: "pays_delete")]
    public function delete (Pays $pays, ManagerRegistry $manager): Response
    {
        $em = $manager->getManager();
        $em->remove($pays);
        $em->flush();
        $this->addFlash('success', 'Pays supprimé');
        return $this->redirectToRoute('pays');
    }
}
