<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class                => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class                          => ['all' => true],
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class            => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class                  => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class                 => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class     => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class                    => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle::class                 => ['all' => true],
    Bundles\ApiBundle\ApiBundle::class                                   => ['all' => true],
    Bundles\PasswordLoginBundle\PasswordLoginBundle::class               => ['all' => true],
    Bundles\PaginationBundle\PaginationBundle::class                     => ['all' => true],
    Bundles\PegassCrawlerBundle\PegassCrawlerBundle::class               => ['all' => true],
    Bundles\SandboxBundle\SandboxBundle::class                           => ['dev' => true],
    Bundles\SettingsBundle\SettingsBundle::class                         => ['all' => true],
    Bundles\TwilioBundle\TwilioBundle::class                             => ['all' => true],
    Bundles\GoogleTaskBundle\GoogleTaskBundle::class                     => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class            => ['dev' => true, 'test' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class                        => ['dev' => true, 'test' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class                        => ['dev' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class               => ['all' => true],
    Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle::class        => ['all' => true],
];
