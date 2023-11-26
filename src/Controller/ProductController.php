<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use \Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'product')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Products::class)->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/add', name: 'app_product')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $products = new Products();
        $productForm = $this->createForm(ProductType::class, $products);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $products->setDeleted(false);
            $products->setCreatedAt(new \DateTimeImmutable());
            $em->persist($products);
            $em->flush();
            $this->addFlash('success', 'Produit ajoute avec success');

            return $this->redirectToRoute('product');
        } else {
            $this->addFlash('danger', "Une erreur c'est porduite lors de l'enregistrement");
            return $this->render('product/add.html.twig', [
                'productForm' => $productForm->createView(),
                'edit' => false
            ]);
        }
    }

    #[Route('/edit/{product}', name: 'edit_product')]
    public function edit(Request $request, EntityManagerInterface $em, Products $product): Response
    {
        if ($product) {
            $productForm = $this->createForm(ProductType::class, $product);
            $productForm->handleRequest($request);

            if ($productForm->isSubmitted() && $productForm->isValid()) {
                $em->persist($product);
                $em->flush();
                $this->addFlash('success', 'Produit modifie avec success');
            } else {
                $this->addFlash('danger', "Une erreur c'est porduite lors de la modification");
                return $this->render('product/add.html.twig', [
                    'productForm' => $productForm->createView(),
                    'edit' => true
                ]);
            }
        } else {
            $this->addFlash('danger', "Le produit n'existe pas");
        }
        return $this->redirectToRoute('product');

    }

    #[Route('/delete/{product}', name: 'delete_product')]
    public function delete(Request $request, EntityManagerInterface $em, Products $product): Response
    {
        if ($product) {
            $product->setDeleted(true);
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produit supprimer avec success');
        } else {
            $this->addFlash('danger', "Une erreur c'est porduite lors de la modification");
        }
        return $this->redirectToRoute('product');
    }


}
