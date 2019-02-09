<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $category = new Category();
        $category->setTitle($faker->lexify('Category ?'));
        $manager->persist($category);
        $manager->flush();

        $this->addReference(self::CATEGORY_REFERENCE, $category);
    }
}
