<?php

namespace App\Controller;

use App\Repository\AgenceRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Agence;
use App\Entity\Annonce;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\AgenceType; 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/agence')]
class CrudAgenceController extends AbstractController
{
    #[Route('/list', name: 'app_list_agence')]

    public function listAgence(AgenceRepository $repository): Response
    {
        
        $agences=$repository->findAll();
        return $this->render('crud_agence/ListAgence.html.twig',['agences'=>$agences]);
    }

    #[Route("/search", name:"app_search_agence")]
    public function searchByName(Request $request, AgenceRepository $repository): Response
    {
        
        $name = $request->query->get('name');  
        $agences = $repository->findByName($name);
    
        
    
        return $this->render('crud_agence/ListAgence.html.twig', [
            'agences' => $agences,
        ]);
    }
    
    #[Route("/delete/{id}", name: 'app_delete_agence')]
    public function deleteAgence($id, AgenceRepository $repository, ManagerRegistry $doctrine): Response
{
    
    $agence = $repository->find($id);

    
    if (!$agence) {
        throw $this->createNotFoundException('Agence not found');
    }

    $em = $doctrine->getManager();
    $em->remove($agence);
    $em->flush();

    
    $this->addFlash('success', 'Agence deleted successfully.');

    return $this->redirectToRoute('app_list_agence');
}

        #[Route('/edit/{id}', name: 'app_agence_edit')]
        public function edit(
            int $id,
            Request $request,
            EntityManagerInterface $entityManager
        ): Response {
            $agence = $entityManager->getRepository(Agence::class)->find($id);
        
            if (!$agence) {
                throw $this->createNotFoundException('Agence not found');
            }
        
            $form = $this->createForm(AgenceType::class, $agence);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
        
                $this->addFlash('success', 'Agence updated successfully.');
        
                return $this->redirectToRoute('app_list_agence');
            }
        
            return $this->render('crud_agence/form_Edit_agence.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        #[Route('/insert', name: 'app_insertform_agence')]
        public function insertFormAgence(Request $request, ManagerRegistry $doctrine): Response
        {
            $agence = new Agence();
            $form = $this->createForm(AgenceType::class, $agence);  
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager(); //entity maager
                $em->persist($agence);
                $em->flush();

                // Flash message for successful addition
            $this->addFlash('success', 'Agence added successfully.');
    
                return $this->redirectToRoute('app_list_agence');
            }
    
            return $this->render('crud_agence/formAgence.html.twig', [
                'form' => $form->createView(),
            ]);
        }


}
