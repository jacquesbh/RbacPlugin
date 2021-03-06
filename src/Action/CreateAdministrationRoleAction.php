<?php

declare(strict_types=1);

namespace Odiseo\SyliusRbacPlugin\Action;

use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Odiseo\SyliusRbacPlugin\Creator\CommandCreatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class CreateAdministrationRoleAction
{
    /** @var CommandBus */
    private $commandBus;

    /** @var CommandCreatorInterface */
    private $createAdministrationRoleCommandCreator;

    /** @var Session */
    private $session;

    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(
        CommandBus $commandBus,
        CommandCreatorInterface $createAdministrationRoleCommandCreator,
        Session $session,
        UrlGeneratorInterface $router
    ) {
        $this->commandBus = $commandBus;
        $this->createAdministrationRoleCommandCreator = $createAdministrationRoleCommandCreator;
        $this->session = $session;
        $this->router = $router;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->commandBus->dispatch($this->createAdministrationRoleCommandCreator->fromRequest($request));

            $this->session->getFlashBag()->add(
                'success',
                'sylius_rbac.administration_role_successfully_created'
            );
        } catch (CommandDispatchException $exception) {
            Assert::notNull($exception->getPrevious());
            $this->session->getFlashBag()->add('error', $exception->getPrevious()->getMessage());
        } catch (\InvalidArgumentException $exception) {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse($this->router->generate('sylius_rbac_admin_administration_role_create_view', []));
    }
}
