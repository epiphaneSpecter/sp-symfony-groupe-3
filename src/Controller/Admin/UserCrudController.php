<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->encodePassword($entityInstance);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $this->encodePassword($entityInstance);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstName'),
            TextField::new('lastName'),
            EmailField::new('email'),
            TextField::new('password')
                ->onlyWhenCreating(),
            BooleanField::new('isVerified')
                ->setFormTypeOption('mapped', false),
            ChoiceField::new('roles')->setChoices([
                'Lecteur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])->allowMultipleChoices(),

        ];
    }

    private function encodePassword(User $user): void
    {
        if ($user->getPassword() !== null) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
        }
    }

}
