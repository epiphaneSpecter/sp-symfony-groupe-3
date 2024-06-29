<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable());

        return $comment;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextEditorField::new('content'),
            AssociationField::new('post')
                ->setFormTypeOption('choice_label', 'title')
        ];
    }

}
