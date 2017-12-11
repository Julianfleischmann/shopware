<?php declare(strict_types=1);

namespace Shopware\Rest\Command;

use Ramsey\Uuid\Uuid;
use Shopware\Api\Search\Criteria;
use Shopware\Api\Search\Query\TermQuery;
use Shopware\Context\Struct\TranslationContext;
use Shopware\User\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\User;

class UserCreateCommand extends Command
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, EncoderFactoryInterface $encoderFactory)
    {
        parent::__construct(null);

        $this->userRepository = $userRepository;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rest:user:create')
            ->addArgument('username', InputArgument::REQUIRED, 'Username for the user')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password for the user')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $password = $input->getOption('password');

        if (empty($password)) {
            $passwordQuestion = new Question('Password for the user');
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setMaxAttempts(3);

            $password = $io->askQuestion($passwordQuestion);
        }

        if ($this->userExists($username)) {
            $io->error(sprintf('User with username "%s" already exists.', $username));
            exit(1);
        }

        $this->createUser($username, $password);

        $io->success(sprintf('User "%s" successfully created.', $username));
    }

    private function userExists(string $username): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new TermQuery('user.username', $username));

        $result = $this->userRepository->searchUuids($criteria, new TranslationContext('SWAG-SHOP-UUID-1', true, null));

        return $result->getTotal() > 0;
    }

    private function createUser(string $username, string $password)
    {
        $encoder = $this->encoderFactory->getEncoder(User::class);
        $password = $encoder->encodePassword($password, $username);

        $context = new TranslationContext('SWAG-SHOP-UUID-1', true, null);

        $this->userRepository->create([
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => $username,
                'email' => 'admin@example.com',
                'username' => $username,
                'password' => $password,
                'localeUuid' => 'SWAG-LOCALE-UUID-1',
                'roleUuid' => '123',
                'active' => true,
            ],
        ], $context);
    }
}
