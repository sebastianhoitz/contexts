<?php

namespace Sanpi\Behatch\HttpCall\Request;

use Behat\Mink\Mink;

class Goutte
{
    protected $mink;

    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function getMethod()
    {
        return $this->getRequest()
            ->getMethod();
    }

    public function getUri()
    {
        return $this->getRequest()
            ->getUri();
    }

    public function getServer()
    {
        return $this->getRequest()
            ->getServer();
    }

    public function getParameters()
    {
        return $this->getRequest()
            ->getParameters();
    }

    protected function getRequest()
    {
        return $this->mink->getSession()->getDriver()->getClient()->getRequest();
    }

    public function getContent()
    {
        return $this->mink->getSession()->getPage()->getContent();
    }

    public function send($method, $url, $parameters = [], $files = [], $content = null, $headers = [])
    {
        $client = $this->mink->getSession()->getDriver()->getClient();

        $client->followRedirects(false);
        $client->request($method, $url, $parameters, $files, $headers, $content);
        $client->followRedirects(true);

        return $this->mink->getSession()->getPage();
    }

    public function setHttpHeader($name, $value)
    {
        $client = $this->mink->getSession()->getDriver()->getClient();
        $client->setHeader($name, $value);
    }

    public function getHttpHeaders()
    {
        return array_change_key_case(
            $this->mink->getSession()->getResponseHeaders(),
            CASE_LOWER
        );
    }

    public function getHttpHeader($name)
    {
        $name = strtolower($name);
        $headers = $this->getHttpHeaders();

        if (isset($headers[$name])) {
            if (is_array($headers[$name])) {
                $value = implode(', ', $headers[$name]);
            }
            else {
                $value = $headers[$name];
            }
        }
        else {
            throw new \OutOfBoundsException(
                "The header '$name' doesn't exist"
            );
        }
        return $value;
    }
}
