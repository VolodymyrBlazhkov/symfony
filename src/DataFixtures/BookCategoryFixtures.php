<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

class BookCategoryFixtures extends Fixture
{
    public const ANDROID_CATEGORY = 'android';
    public const DEVICES_CATEGORY = 'devices';

    public function load(ObjectManager $manager): void
    {
        $categories = [
            self::ANDROID_CATEGORY => (new Category())->setTitle('Android')->setSlug('android'),
            self::DEVICES_CATEGORY => (new Category())->setTitle('Devices')->setSlug('devices')
        ];
        foreach ($categories as $category) {
            $manager->persist($category);
        }

        $manager->persist((new Category())->setTitle('Networking')->setSlug('networking'));
        $manager->flush();

        foreach ($categories as $code => $category) {
            $this->addReference($code, $category);
        }
    }
}
