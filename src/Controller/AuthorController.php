<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    /**
     * @Route("/api/authors", name="authors", methods="GET")
     */
    public function getAllAuthors(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository->findAll();
        $authorListJson = $serializer->serialize($authorList, 'json', ['groups' => 'getAuthors']);

        return new JsonResponse($authorListJson, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("api/authors/{id}", name="DetailAuthor", methods="GET")
    */
    public function getOneAuthor(Author $author, SerializerInterface $serializer):JsonResponse
    {
        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);

        return new JsonResponse($jsonAuthor, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/authors/{id}", name="deleteAuthor", methods="DELETE")
     */
    public function deleteAuthor(Author $author, EntityManagerInterface $em): JsonResponse
    {
        // Suppression
        $em->remove($author);
        // Confirmation
        $em->flush();
        // Suppression des livres pour l'auteur
        dd($author->getBooks());
        // Retourne 204
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
