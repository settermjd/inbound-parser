<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Entity\Attachment;
use App\Entity\Image;
use App\Entity\Note;
use App\Entity\User;
use App\Handler\HomePageHandler;
use App\Repository\UserRepository;
use App\Service\EmailParserService;
use App\Service\TwilioService;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use JustSteveKing\StatusCode\Http;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Mail\Message;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class HomePageHandlerTest extends TestCase
{
    protected MockObject|ContainerInterface $container;
    private EntityManager|MockObject $entityManager;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->entityManager = $this->createMock(EntityManager::class);
    }

    public static function invalidSubjectLineProvider(): array
    {
        return [
            [
                ['subject' => 'Refd ID: 123AQPOIU98765'],
            ],
            [
                ['subject' => ''],
            ],
            [[]],
        ];
    }

    /**
     * @dataProvider invalidSubjectLineProvider
     */
    public function testHandlesIncorrectSubjectLines(array $postData): void
    {
        $homePage = new HomePageHandler(
            $this->entityManager,
            new EmailParserService(),
            $this->createMock(UserService::class),
            $this->createMock(TwilioService::class),
            $this->createMock(LoggerInterface::class)
        );
        $request = $this->createMock(
            ServerRequestInterface::class
        );
        $request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($postData);

        $response = $homePage->handle($request);
        $this->assertSame(
            Http::UNPROCESSABLE_ENTITY->value,
            $response->getStatusCode()
        );
        $responseBody = file_get_contents(__DIR__ . '/../../_files/invalid-subject-response-body.json');

        $this->assertSame(
            $responseBody,
            $response->getBody()->getContents()
        );
    }

    public function testHandlesValidRequest(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $email = file_get_contents(
            __DIR__ . '/../../_files/mail_with_pdf_attachment.eml'
        );
        $pdf = file_get_contents(__DIR__ . '/../../_files/test.pdf');

        $responseBody = file_get_contents(__DIR__ . '/../../_files/successful-note-creation-response-body.json');

        $requestData = [
            "charsets" => '{\"to\":\"UTF-8\",\"from\":\"UTF-8\",\"subject\":\"UTF-8\"}',
            "SPF" => "pass",
            "from" => "Example Sender <sender@example.net>",
            "subject" => "Reference ID: 123AQPOIU98765",
            "envelope" => '{"to":["inbound@example.org"],"from":"sender@example.net"}',
            "email" => $email,
            "dkim" => "{@twilio.com : pass}",
            "sender_ip" => "148.163.153.13",
            "to" => "inbound@example.org",
            "spam_score" => "-0.1",
            "spam_report" => "Spam detection software, running on the system \"parsley-p1las1-spamassassin-65fbf9c65-95hhl\",\nhas NOT identified this incoming email as spam.  The original\nmessage has been attached to this so you can view it or label\nsimilar future email.  If you have any questions, see\nthe administrator of that system for details.\n\nContent preview:  ￼ Here’s the attachment. Best, \n\nContent analysis details:   (-0.1 points, 5.0 required)\n\n pts rule name              description\n---- ---------------------- --------------------------------------------------\n 0.0 URIBL_BLOCKED          ADMINISTRATOR NOTICE: The query to URIBL was\n 0.0 URIBL_ZEN_BLOCKED      ADMINISTRATOR NOTICE: The query to\n 0.0 RCVD_IN_ZEN_BLOCKED    RBL: ADMINISTRATOR NOTICE: The query to\n 0.0 HTML_MESSAGE           BODY: HTML included in message\n-0.1 DKIM_VALID             Message has at least one valid DKIM or DK signature\n-0.1 DKIM_VALID_AU          Message has a valid DKIM or DK signature from\n 0.1 DKIM_SIGNED            Message has a DKIM or DK signature, not necessarily\n-0.0 DKIMWL_WL_HIGH         DKIMwl.org - High trust sender\n"
        ];

        $logger
            ->expects($this->once())
            ->method('info')
            ->with(
                'Inbound parsed data from SendGrid',
                [
                    'subject' =>"Reference ID: 123AQPOIU98765",
                    "spam_score" => "-0.1",
                    "envelope" => '{"to":["inbound@example.org"],"from":"sender@example.net"}',
                    'email' => $email,
                ]
            );

        $userService = $this->createMock(UserService::class);
        $message = Message::fromString($email);
        $userService
            ->expects($this->once())
            ->method('createNote')
            ->with($message)
            ->willReturn(true);

        $userEmail = "example@example.com";

        $smsBody = "Hi Matthew. This a quick confirmation that \"test document.pdf\" has been added as a note on your account, along with the text, which you can find in the attachment to this SMS.";
        $recipient = "+6140912341234";
        $sender = "+6140912341244";

        $userRepository = $this->createMock(UserRepository::class);
        $user = $this->createMock(User::class);
        $userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $userEmail])
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepository);

        $twilioService = $this->createMock(TwilioService::class);
        $twilioService
            ->expects($this->once())
            ->method('sendNewNoteNotification')
            ->with($user, $message->getAttachments())
            ->willReturn(true);

        $homePage = new HomePageHandler(
            $this->entityManager,
            new EmailParserService(),
            $userService,
            $twilioService,
            $logger
        );
        $request = $this->createMock(
            ServerRequestInterface::class
        );
        $request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);
        $response = $homePage->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Http::CREATED->value, $response->getStatusCode());
        self::assertSame(
            $responseBody,
            $response->getBody()->getContents()
        );
    }
}
