<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * Permet d'afficher tout les produits
     *
     * @param ProductRepository $repo
     * @return Response
     */
    #[Route('/products', name: 'products_index')]
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * Permet d'ajouter un produit
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/product/new', name: 'product_create')]
    public function create(Request $request, EntityManagerInterface $manager):Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($product->getImages() as $image)
            {
                $image->setProduct($product);
                $manager->persist($image);
            }
            $manager->persist($product);

            $manager->flush($product);

            $this->addFlash('success',"Le produit <strong>".$product->getName()."</strong> a bien été ajouté");

            return $this->redirectToRoute('product_show', [
                'slug' => $product->getSlug()
            ]);

        }
        
        return $this->render('product/new.html.twig', [
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier un produit 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Product $product
     * @return void
     */
    #[Route('/product/{slug}/edit', name:'product_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Product $product)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($product->getImages() as $image)
            {
                $image->setProduct($product);
                $manager->persist($image);
            }
            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le produit a bien été mis à jour'
            );

            return $this->redirectToRoute('product_show',[
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/edit.html.twig',[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher un seul produit ( en fonction de son slug )
     *
     * @param string $slug
     * @param Product $product
     * @return Response
     */
    #[Route('/product/{slug}', name:'product_show')]
    public function show(string $slug, Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
