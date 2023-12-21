<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => 'user_basic']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }


    #[Route('/new', name: 'user_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // Hashage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($user, 'json', ['groups' => 'user_basic']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/email/{email}', name: 'user_by_email', methods: ['GET'])]
    public function getByEmail(string $email, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findByEmailLike($email);
        if (!$users) {
            return new JsonResponse(['status' => 'No users found'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize($users, 'json', ['groups' => 'user_basic']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}/edit', name: 'user_edit', methods: ['PUT'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Mise à jour de l'email
        if (array_key_exists('email', $data)) {
            $user->setEmail($data['email']);
        }

        // Mise à jour du nom
        if (array_key_exists('username', $data)) {
            $user->setUsername($data['username']);
        }

        // Mise à jour du mot de passe
        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        // Mise à jour des rôles
        if (array_key_exists('roles', $data)) {
            $user->setRoles($data['roles']);
        }

        // Enregistrement des modifications
        $entityManager->flush();

        return new JsonResponse(['status' => 'Utilisateur bien mis à jours'], Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $entityManager): JsonResponse
    {

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User deleted'], Response::HTTP_OK);
    }
}