<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Context\Step;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

class SystemContext implements Context
{
    private $root;
    private $createdFiles = [];

    public function __construct($root = '.')
    {
        $this->root = $root;
    }

    public static function getTranslationResources()
    {
        return glob(__DIR__ . '/../../i18n/*.xliff');
    }

    /**
     * Uploads a file using the specified input field
     *
     * @When (I )put the file :file into :field
     */
    public function putFileIntoField($file, $field)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $file;

        return [
            new Step\When("I attach the file '$path' to '$field'")
        ];
    }

    /**
     * Execute a command
     *
     * @Given (I )execute :command
     */
    public function iExecute($cmd)
    {
        exec($cmd, $output, $return);

        if ($return !== 0) {
            throw new \Exception(sprintf("Command %s returned with status code %s\n%s", $cmd, $return, implode("\n", $output)));
        }
    }

    /**
     * Execute a command from project root
     *
     * @Given (I )execute :command from project root
     */
    public function iExecuteFromProjectRoot($cmd)
    {
        $cmd = $this->root . DIRECTORY_SEPARATOR . $cmd;
        $this->iExecute($cmd);
    }

    /**
     * @Given (I )create the file :filename containing:
     * @Given (I )create the file :filename contening:
     */
    public function iCreateTheFileContaining($filename, PyStringNode $string)
    {
        if (!is_file($filename)) {
            file_put_contents($filename, $string);
            $this->createdFiles[] = $filename;
        }
        else {
            throw new \RuntimeException("'$filename' already exists.");
        }
    }

    /**
     * @Then print the content of :filename file
     */
    public function printTheContentOfFile($filename)
    {
        if (is_file($filename)) {
            echo file_get_contents($filename);
        }
        else {
            throw new \RuntimeException("'$filename' doesn't exists.");
        }
    }

    /**
     * @AfterScenario
     */
    public function after()
    {
        foreach ($this->createdFiles as $filename) {
            unlink($filename);
        }
    }
}
