<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
    public function addBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository): JsonResponse
    {
        // Deserialise en JSON
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        // Recuperation de l'ensemble des données sous forme tableau
        $content = $request->toArray();
        // Recuperation de l'id author, s'il n'est pas defini alors on met -1 par defaut
        $idAuthor = $content['idAuthor'] ?? -1;
        // On cherche l'auteur qui correspont et on l'associe à ce livre
        // Si find ne trouve pas ID alors null sera renvoyé
        $book->setAuthor($authorRepository->find($idAuthor));
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


    /**
     * @Route("/api/books/{id}", name="updateBook", methods="PUT")
     */
    public function updateBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                            Book $currentBook, UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository): JsonResponse
    {
        // Deserialise en JSON
        $updateBook = $serializer->deserialize($request->getContent(), Book::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);
        // Recuperation de l'ensemble des données sous forme tableau
        $content = $request->toArray();
        // Recuperation de l'id author, s'il n'est pas defini alors on met -1 par defaut
        $idAuthor = $content['idAuthor'] ?? -1;
        // On cherche l'auteur qui correspont et on l'associe à ce livre
        // Si find ne trouve pas ID alors null sera renvoyé
        $updateBook->setAuthor($authorRepository->find($idAuthor));
        // On enregistre
        $em->persist($updateBook);
        // On confirme l'enregistrement
        $em->flush();
        // Retour OK
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
