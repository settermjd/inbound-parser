<?php

declare(strict_types=1);

namespace App;

use App\Repository\NoteRepository;
use App\Repository\NoteRepositoryFactory;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryFactory;
use App\Service\MonologServiceFactory;
use App\Service\TwilioRestClientFactory;
use App\Service\TwilioService;
use App\Service\TwilioServiceFactory;
use App\Service\UserNoteService;
use App\Service\UserNoteServiceFactory;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Client;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories'  => [
                Client::class => TwilioRestClientFactory::class,
                Handler\HomePageHandler::class => Handler\HomePageHandlerFactory::class,
                NoteRepository::class => NoteRepositoryFactory::class,
                UserRepository::class => UserRepositoryFactory::class,
                LoggerInterface::class => MonologServiceFactory::class,
                TwilioService::class => TwilioServiceFactory::class,
                UserNoteService::class => UserNoteServiceFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }
}
