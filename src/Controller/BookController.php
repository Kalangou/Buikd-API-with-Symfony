<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/books/{id}", name="book", methods="GET")
     */
//    public function getOneBook(int $id, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
//    {
//        $book       = $bookRepository->find($id);
//        // Si le livre est trouvé
//        if($book) {
//            // Format JSON
//            $jsonBook   = $serializer->serialize($book, 'json');
//            // Retour OK
//            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
//        }
//        // Livre non trouvé
//        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
//    }

    public function getOneBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        // Format JSON
        $jsonBook   = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        // Retour OK
        return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
    }


}
