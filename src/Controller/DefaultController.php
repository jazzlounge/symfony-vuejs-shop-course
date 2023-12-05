<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/', name: 'main_homepage')]
    public function index(): Response {
        $productList = $this->em->getRepository(Product::class)->findAll();
        //dd($productList);
        return $this->render('main/default/index.html.twig', []);
    }

    #[Route(
        '/{path}/{id}',
        name: 'product_edit',
        requirements: ['path'=>'product-edit|product-add', "id"=>'\d+'],
        methods: ['GET', 'POST']
    )]
    public function editProduct(Request $request, int $id = null): Response  {
        if($id) {
            $product = $this->em->getRepository(Product::class)->find($id);

        } else {
            $product = new Product();
        }


        $form = $this->createForm(EditProductFormType::class, $product);


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId(), 'path'=>'product-edit'] );
        }

        return $this->render('main/default/edit_product.html.twig', [
            'form'  => $form->createView()
        ]);
    }

}