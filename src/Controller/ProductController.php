<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\Tools\Pagination\Paginator;

#[Route('/product')]
class ProductController extends AbstractController
{   
    #[Route('/', name: 'app_product', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $listProduct = $productRepository->findByPage(
            $request->query->getInt('page', 1),
            10
        );

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        return $this->render('dashboard/product.html.twig', [
            'products' => $listProduct["products"],
            'total' => $listProduct["products"]->count(),
            'page' => $listProduct["page"],
            'pages' => $listProduct["pages"],
            'get' => 'page',
            'limit' => $listProduct["limit"],

            'product' => $product,
            'form' => $form
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setUsers($this->getUser());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_get_edit', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        if (!$this->checkProduct($product)) {
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_get_edit', methods: ['GET'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if (!$this->checkProduct($product)) {
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        return $this->render('dashboard/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['POST'])]
    public function editSave(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if (!$this->checkProduct($product)) {
            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product', [], Response::HTTP_SEE_OTHER);
    }

    // check if the product is not null and the user id is equal to the current user id
    private function checkProduct(Product $product): bool
    {
        return $product !== null && $this->getUser()->getId() === $product->getUsers()->getId();
    }
}
