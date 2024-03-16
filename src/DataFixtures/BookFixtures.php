<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $androidCategory = $this->getReference(BookCategoryFixtures::ANDROID_CATEGORY);
        $devicesCategory = $this->getReference(BookCategoryFixtures::DEVICES_CATEGORY);

        $book = (new Book())
            ->setTitle('Java')
            ->setPublicationDate(new \DateTimeImmutable('2019-04-01'))
            ->setMeap(false)
            ->setIsbn('asdasdasd')
            ->setDescription('sdfsdfsdfsdfs')
            ->setAuthors(['Timon'])
            ->setSlug('java')
            ->setImage('dfgdfgdfg')
            ->setCategories(new ArrayCollection([$androidCategory, $devicesCategory]));

        $manager->persist($book);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            BookCategoryFixtures::class
        ];
    }
}
