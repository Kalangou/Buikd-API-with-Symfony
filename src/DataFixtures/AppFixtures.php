<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $listAuthors = [];
        // Ajout de 10 livres
        for ($i = 1; $i <= 20; $i++) {
            $author = new Author();
            $author->setFirstName('First-name ' . $i);
            $author->setLastName("Last-name " . $i);
            $manager->persist($author);
            // Sauvegarde des autheurs dans le tableau
            $listAuthors[] = $author;
        }

        // Ajout de 20 livres
        for ($i = 1; $i <= 20; $i++) {
            $book = new Book();
            $book->setTitle('Livre ' . $i);
            $book->setCoverText("Quatrième de couverture numéro " . $i);
            $book->setAuthor($listAuthors[array_rand($listAuthors)]);
            $manager->persist($book);
        }
        $manager->flush();
    }
}
