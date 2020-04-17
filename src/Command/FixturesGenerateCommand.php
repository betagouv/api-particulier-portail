<?php

namespace App\Command;

use App\Entity\Api;
use App\Entity\ApiKey;
use App\Entity\Application;
use App\Entity\Organization;
use App\Entity\Subscription;
use DateTime;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class FixturesGenerateCommand extends Command
{
    protected static $defaultName = 'app:fixtures:generate';

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate fixtures from a legacy database JSON dump')
            ->addArgument('jsonFile', InputArgument::REQUIRED, 'Path to the legacy JSON dump');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $jsonFilePath = $input->getArgument('jsonFile');

        $content = file_get_contents($jsonFilePath);

        $tokens = $this->decoder->decode($content, "json");

        $legacyOrganizationId = Uuid::uuid4()->toString();
        $sinkholeApiId = Uuid::uuid4()->toString();
        $fixtures = [
            Application::class => [],
            Organization::class => [
                $legacyOrganizationId => [
                    "siret" => "21750001600019"
                ]
            ],
            ApiKey::class => [],
            Subscription::class => [],
            Api::class => [
                $sinkholeApiId => [
                    "id" => $sinkholeApiId,
                    "name" => "Sinkhole API",
                    "backend" => "https://api-particulier-portail-bin.herokuapp.com/",
                    "path" => "sinkhole"
                ]
            ]
        ];

        foreach ($tokens as $token) {
            if (!isset($token["name"])) {
                // Incomplete token, skip
                continue;
            }

            // Application fixture
            $applicationId = Uuid::uuid4()->toString();
            $application = [
                "id" => $applicationId,
                "name" => $token["name"],
                "signupId" => $token["signup_id"],
                "organization" => sprintf("@%s", $legacyOrganizationId)
            ];
            $fixtures[Application::class][$applicationId] = $application;

            // Subscription
            $subscriptionId = Uuid::uuid4()->toString();
            $subscription = [
                "id" => $subscriptionId,
                "api" => sprintf("@%s", $sinkholeApiId),
                "application" => sprintf("@%s", $applicationId),
                "active" => true
            ];
            $fixtures[Subscription::class][$subscriptionId] = $subscription;

            // ApiKey
            $apiKeyId = Uuid::uuid4()->toString();
            $apiKey = [
                "id" => $apiKeyId,
                "active" => true,
                "hash" => $token["hashed_token"],
                "expires_at" => (new DateTime())->modify('+1 year'),
                "application" => sprintf("@%s", $applicationId)
            ];
            $fixtures[ApiKey::class][$apiKeyId] = $apiKey;
        }

        // API
        $apiFixtures = $this->encoder->encode(
            [
                Api::class => $fixtures[Api::class]
            ],
            "yaml"
        );
        file_put_contents("./fixtures/prod/legacy_api.yml", $apiFixtures);

        // ApiKey
        $apiKeyFixtures = $this->encoder->encode(
            [
                ApiKey::class => $fixtures[ApiKey::class]
            ],
            "yaml"
        );
        file_put_contents("./fixtures/prod/legacy_api_key.yml", $apiKeyFixtures);

        // Application
        $applicationFixtures = $this->encoder->encode(
            [
                Application::class => $fixtures[Application::class]
            ],
            "yaml"
        );
        file_put_contents("./fixtures/prod/legacy_application.yml", $applicationFixtures);

        // Subscription
        $subscriptionFixtures = $this->encoder->encode(
            [
                Subscription::class => $fixtures[Subscription::class]
            ],
            "yaml"
        );
        file_put_contents("./fixtures/prod/legacy_subscription.yml", $subscriptionFixtures);

        // Organization
        $organizationFixtures = $this->encoder->encode(
            [
                Api::class => $fixtures[Api::class]
            ],
            "yaml"
        );
        file_put_contents("./fixtures/prod/legacy_organization.yml", $organizationFixtures);

        $io->success("Fixtures created");

        return 0;
    }
}
