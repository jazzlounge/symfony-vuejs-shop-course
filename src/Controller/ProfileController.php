<?php

namespace App\Controller;

use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/profile', name: 'main_profile_index')]
    public function index(EntityManagerInterface $entityManager): Response     {
        return $this->render('main/profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'main_profile_edit')]
    public function edit(Request $request) : Response {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('main_profile_index');
        }
        return $this->render('main/profile/edit.html.twig', ['form'=>$form->createView()]);
    }
}
