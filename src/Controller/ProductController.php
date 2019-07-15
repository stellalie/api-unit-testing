<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $results = [];
        foreach ($products as $product) {
            /** @var Product $product */
            $results[] = [
                'id' => $product->getId(),
                'name' => $product->getName()
            ];
        }
        return $this->json($results);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function singleAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName()
        ]);
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName()
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}