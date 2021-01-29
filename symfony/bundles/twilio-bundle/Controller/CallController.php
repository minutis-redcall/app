<?php

namespace Bundles\TwilioBundle\Controller;

use Bundles\TwilioBundle\Component\HttpFoundation\XmlResponse;
use Bundles\TwilioBundle\Manager\TwilioCallManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\TwiML\TwiML;

/**
 * @Route(name="twilio_", path="/twilio/")
 */
class CallController extends BaseController
{
    /**
     * @var TwilioCallManager
     */
    private $callManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(RequestStack $requestStack,
        TwilioCallManager $callManager,
        LoggerInterface $logger = null)
    {
        parent::__construct($requestStack);

        $this->callManager = $callManager;
        $this->logger      = $logger ?? new NullLogger();
    }

    /**
     * @Route(name="incoming_call", path="incoming-call")
     */
    public function incoming(Request $request)
    {
        $this->validateRequestSignature($request);

        $this->logger->info('Twilio webhooks - incoming call', [
            'payload' => $request->request->all(),
        ]);

        $response = $this->callManager->handleIncomingCall(
            $request->request->all()
        );

        if (!$response) {
            return new Response();
        }

        if ($response instanceof Response) {
            return $response;
        }

        return new XmlResponse($response->asXml());
    }

    /**
     * @Route(name="outgoing_call", path="outgoing-call/{uuid}")
     */
    public function outgoing(Request $request, string $uuid)
    {
        $this->validateRequestSignature($request);

        $this->logger->info('Twilio webhooks - outgoing call', [
            'payload' => $request->request->all(),
        ]);

        $call = $this->callManager->get($uuid);
        if (!$call) {
            throw $this->createNotFoundException();
        }

        $keys = $request->get('Digits');
        if (null === $keys) {
            $response = $this->callManager->handleCallEstablished($call);
        } else {
            $response = $this->callManager->handleKeyPressed($call, $keys);
        }

        if (!$response) {
            return new Response();
        } elseif ($response instanceof Response) {
            return $response;
        } elseif ($response instanceof TwiML) {
            return new XmlResponse($response->asXml());
        }

        // Repeat
        return $this->redirectToRoute('twilio_outgoing_call', [
            'uuid' => $uuid,
        ]);
    }

    /**
     * @Route(name="answering_machine", path="answering-machine/{uuid}")
     */
    public function answeringMachine(Request $request, string $uuid)
    {
        $this->validateRequestSignature($request);

        $this->logger->info('Twilio webhooks - answering machine hook', [
            'payload' => $request->request->all(),
        ]);

        $call = $this->callManager->get($uuid);
        if (!$call) {
            throw $this->createNotFoundException();
        }

        if ('machine_start' === $request->get('AnsweredBy')) {
            $this->callManager->handleAnsweringMachine($call);
        }

        return new Response();
    }
}
