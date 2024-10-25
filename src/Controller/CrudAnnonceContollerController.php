<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Agence;
use App\Entity\Annonce;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\AnnonceType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/annonce')]
class CrudAnnonceContollerController extends AbstractController
{
    #[Route('/list', name: 'app_list_annonce')]

    public function listAnnonce(AnnonceRepository $repository): Response
    {
        
        $annonces=$repository->findAll();
        return $this->render('crud_annonce_contoller/ListAnnonce.html.twig',['annonces'=>$annonces]);
    }
    
    #[Route("/delete/{id}", name: 'app_delete_annonce')]
    public function deleteAnnonce($id, AnnonceRepository $repository, ManagerRegistry $doctrine): Response
{
    
    $annonce = $repository->find($id);

    
    if (!$annonce) {
        throw $this->createNotFoundException('Annonce not found');
    }

    $em = $doctrine->getManager();
    $em->remove($annonce);
    $em->flush();

    
    $this->addFlash('success', 'Annonce deleted successfully.');

    return $this->redirectToRoute('app_list_annonce');
}

        #[Route('/edit/{id}', name: 'app_annonce_edit')]
        public function edit(
            int $id,
            Request $request,
            EntityManagerInterface $entityManager
        ): Response {
            $annonce = $entityManager->getRepository(Annonce::class)->find($id);
        
            if (!$annonce) {
                throw $this->createNotFoundException('Annonce not found');
            }
        
            $form = $this->createForm(AnnonceType::class, $annonce);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
        
                $this->addFlash('success', 'Annonce updated successfully.');
        
                return $this->redirectToRoute('app_list_annonce');
            }
        
            return $this->render('crud_annonce_controller/form_Edit_annonce.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        #[Route('/insert', name: 'app_insertform_annonce')]
        public function insertFormAnnonce(Request $request, ManagerRegistry $doctrine): Response
        {
            $annonce = new Annonce();
            $form = $this->createForm(AnnonceType::class, $annonce);  
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager(); //entity maager
                $em->persist($annonce);
                $em->flush();

                // Flash message for successful addition
            $this->addFlash('success', 'Annonce added successfully.');
    
                return $this->redirectToRoute('app_list_annonce');
            }
    
            return $this->render('crud_annonce_contoller//formAnnonce.html.twig', [
                'form' => $form->createView(),
            ]);
        }
}
