<?php

declare(strict_types=1);

namespace Tests\Sylius\RbacPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RbacPlugin\Entity\AdministrationRoleInterface;
use Sylius\RbacPlugin\Model\Permission;
use Sylius\RbacPlugin\Model\PermissionAccess;

final class AdministrationRolesContext implements Context
{
    /** @var FactoryInterface */
    private $administrationRoleFactory;

    /** @var RepositoryInterface */
    private $administrationRoleRepository;

    /** @var ObjectManager */
    private $administrationRoleManager;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        FactoryInterface $administrationRoleFactory,
        RepositoryInterface $administrationRoleRepository,
        ObjectManager $administrationRoleManager,
        SharedStorageInterface $sharedStorage
    ) {
        $this->administrationRoleFactory = $administrationRoleFactory;
        $this->administrationRoleRepository = $administrationRoleRepository;
        $this->administrationRoleManager = $administrationRoleManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is already an Administration role :name in the system
     */
    public function thereIsAlreadyAdministrationRoleInTheSystem(string $name): void
    {
        /** @var AdministrationRoleInterface $administrationRole */
        $administrationRole = $this->administrationRoleFactory->createNew();
        $administrationRole->setName($name);

        $this->administrationRoleRepository->add($administrationRole);
        $this->sharedStorage->set('administration_role', $administrationRole);
    }

    /**
     * @Given /^(this administration role) has "([^"]+)" permission$/
     * @Given /^(this administration role) has "([^"]+)" and "([^"]+)" permissions$/
     */
    public function thisAdministrationRoleHasAndPermissions(
        AdministrationRoleInterface $administrationRole,
        string $firstPermissionName,
        string $secondPermissionName
    ): void {
        $administrationRole
            ->addPermission(Permission::ofType(strtolower(str_replace(' ', '_', $firstPermissionName)),
                [PermissionAccess::READ, PermissionAccess::WRITE]
            )
        );

        $administrationRole
            ->addPermission(Permission::ofType(strtolower(str_replace(' ', '_', $secondPermissionName)),
                [PermissionAccess::READ, PermissionAccess::WRITE]
            )
        );

        $this->administrationRoleManager->flush();
    }
}
