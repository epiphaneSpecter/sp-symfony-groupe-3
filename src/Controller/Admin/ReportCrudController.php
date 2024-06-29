<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Report::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $report = new Report();
        $report->setAuthor($this->getUser());
        $report->setCreatedAt(new \DateTimeImmutable());

        return $report;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextEditorField::new('reason'),
            AssociationField::new('comment')
                ->setFormTypeOption('choice_label', 'id')
        ];
    }

}
