<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public const COMMENT_REFERENCE = 'comment';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $comment = new Comment();
        $comment->setContent($faker->realText($maxNbChars = 10, $indexSize = 2));
        $comment->setPublishedAt(new \DateTime('now'));
        $comment->setAuthor($this->getReference(UserFixtures::USER_REFERENCE));

        $manager->persist($comment);
        $manager->flush();

        $this->addReference(self::COMMENT_REFERENCE, $comment);
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
