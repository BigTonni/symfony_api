<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public const ARTICLE_REFERENCE = 'article';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 1; $i < 11; ++$i) {
            $article = new Article();
            $article->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true));
            $article->setBody($faker->realText($maxNbChars = 200, $indexSize = 2));
            $article->setStatus(Article::STATUS_PUBLISH);
            $article->setAuthor($this->getReference(UserFixtures::USER_REFERENCE));
            $article->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE));
            $article->addTag($this->getReference(TagFixtures::TAG_REFERENCE));
            $article->addComment($this->getReference(CommentFixtures::COMMENT_REFERENCE));

            $manager->persist($article);
        }
        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixtures::class, CategoryFixtures::class, TagFixtures::class, CommentFixtures::class];
    }
}
