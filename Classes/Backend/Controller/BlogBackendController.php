<?php

declare(strict_types=1);

namespace Lanius\Blogext\Backend\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerInterface;

class BlogBackendController 
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var StreamFactoryInterface */
    protected $streamFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Do awesome stuff

        return $this->responseFactory->createResponse()->withBody(
            $this->streamFactory->createStream('Response content from BlogBackendController with route path: ' . $request->getAttribute('route')->getPath())
        );
    }
}
