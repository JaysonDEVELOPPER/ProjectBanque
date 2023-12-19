<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Forecast;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ForecastController extends AbstractController
{
    #[Route('/forecast', name: 'app_forecast')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ForecastController.php',
        ]);
    }
    #[Route('/forecasts', name: 'app_forecasts', methods: ['GET'])]
    public function getAll(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $forecast = $em->getRepository(Forecast::class)->findAll(); 
        $json = $serializer->serialize($forecast, 'json', ['groups' => 'forecast_group']); 

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/forecast/{id}', name: 'forecast_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $forecast = $entityManager->getRepository(Forecast::class)->find($id);

        if (!$forecast) {
            throw $this->createNotFoundException(
                'No forecast found for id ' . $id
            );
        }

        return new Response('Check out this great product: ' . $forecast->getFrcAmounts() . ',' . $forecast->getId() . ' .');

    }

    // #[Route('/productnew', name: 'create_product', methods: ['POST'])]
    // public function createProduct(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer, ): Response
    // {
    //     $data = $request->getContent();
    //     $product = $serializer->deserialize($data, Product::class, 'json');
    //     $entityManager->persist($product);
    //     $entityManager->flush();
    //     $json = $serializer->serialize($product, 'json', ["groups" => "product_group"]);
    //     return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    // }


    // #[Route('/product/update/{id}', name: 'update_product', methods: ['PUT'])]
    // public function updateProduct(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, int $id): Response
    // {
    //     $product = $entityManager->getRepository(Product::class)->find($id);
    //     if(!$product) {
    //         throw $this->createNotFoundException('No product found for' . $id);
    //     }
    //     $serializer->deserialize($request->getContent(), Product::class, 'json', ['object_to_populate' => $product]);
    //     $entityManager->flush();
    //     return new JsonResponse(['status' => 'Product updated!'], Response::HTTP_OK);
    // }

    // #[Route('/product/delete/{id}', name: 'product_edit')]
    // public function delete(EntityManagerInterface $entityManager, int $id): Response
    // {
    //     $product = $entityManager->getRepository(Product::class)->find($id);

    //     if (!$product) {
    //         throw $this->createNotFoundException(
    //             'No product found for id ' . $id
    //         );
    //     }

    //     $entityManager->remove($product);
    //     $entityManager->flush();

    //     return $this->redirectToRoute('app_product', [
    //         'id' => $product->getId()
    //     ]);
    // }
}
