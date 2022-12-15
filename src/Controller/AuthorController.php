<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
     * @Route("api/authors/{id}", name="detailAuthor", methods="GET")
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


    /**
     * @Route("/api/authors", name="addAuthor", methods="POST")
     */
    public function addAuthor(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                            UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        // Deserialise en JSON
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json');
        // On enregistre
        $em->persist($author);
        // On confirme l'enregistrement
        $em->flush();
        // Format JSON
        $jsonBook   = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);
        // Redirection : Location au niveau de header
        $location = $urlGenerator->generate('detailAuthor', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        // Retour OK
        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    /**
     * @Route("/api/authors/{id}", name="updateAuthor", methods="PUT")
     */
    public function updateAuthor(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                               Author $currentAuthor): JsonResponse
    {
        // Deserialise en JSON
        $updateAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor]);
        // On enregistre
        $em->persist($updateAuthor);
        // On confirme l'enregistrement
        $em->flush();
        // Retour OK
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
