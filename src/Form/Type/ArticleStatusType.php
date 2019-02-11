<?php

namespace App\Form\Type;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleStatusType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'Draft' => Article::STATUS_DRAFT,
                'Pending' => Article::STATUS_PENDING,
                'Publish' => Article::STATUS_PUBLISH,
            ],
        ]);
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
