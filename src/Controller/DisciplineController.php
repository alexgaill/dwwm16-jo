<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Form\DisciplineType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisciplineController extends AbstractController
{
    #[Route('/', name: 'discipline')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $discipline = new Discipline;
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $manager->getManager();
                $em->persist($discipline);
                $em->flush();
                $this->addFlash('success', 'Ajout ok');
                return $this->redirectToRoute('discipline');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('discipline/index.html.twig', [
            'disciplineList' => $manager->getRepository(Discipline::class)->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route("/discipline/update/{id}", name:"discipline_update")]
    public function update(Discipline $discipline, ManagerRegistry $manager, Request $request):Response
    {
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $manager->getManager();
                $em->persist($discipline);
                $em->flush();
                $this->addFlash('success', "Mise à jour ok");
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
            return $this->redirectToRoute('discipline');
        }

        return $this->render('discipline/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/discipline/delete/{id}', name:'discipline_delete')]
    public function delete (Discipline $discipline, ManagerRegistry $manager):Response
    {
        try {
            $em = $manager->getManager();
            $em->remove($discipline);
            $em->flush();
            $this->addFlash('success', "Suppression réussie");
        } catch (\Throwable $th) {
            $this->addFlash('danger', "Erreur lors de la suppression");
        }
        return $this->redirectToRoute('discipline');
    }
}
