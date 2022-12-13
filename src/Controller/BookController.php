<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/api/books", name="books", methods="GET")
     */
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);

        // Retourne 200
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/books/{id}", name="detailBook", methods="GET")
     */
    public function getOneBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        // Format JSON
        $jsonBook   = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        // Retourne 200
        return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/books/{id}", name="deleteBook", methods="DELETE")
     */
    public function deleteBook(Book $book, EntityManagerInterface $em): JsonResponse
    {
        // Suppression
        $em->remove($book);
        // Confirmation
        $em->flush();
        // Retourne 204
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @Route("/api/books", name="addBook", methods="POST")
     */
    public function addBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        // Deserialise en JSON
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        // On enregistre
        $em->persist($book);
        // On confirme l'enregistrement
        $em->flush();
        // Format JSON
        $jsonBook   = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        // Redirection : Location au niveau de header
        $location = $urlGenerator->generate('detailBook', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        // Retour OK
        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ['Location' => $location], true);
    }


}
