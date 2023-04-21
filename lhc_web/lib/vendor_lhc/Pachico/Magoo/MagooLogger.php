<?php

namespace Pachico\Magoo;

use Pachico\Magoo\MagooArray;
use Psr\Log\LoggerInterface;

/**
 * MagooLogger acts as a middleware between your application and a PSR3 logger
 * masking every message passed to it
 */
class MagooLogger implements LoggerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MaskManagerInterface
     */
    private $maskManager;

    /**
     * @var MagooArray
     */
    private $magooArray;

    /**
     * @param LoggerInterface $logger
     * @param MaskManagerInterface $maskManager
     */
    public function __construct(LoggerInterface $logger, MaskManagerInterface $maskManager)
    {
        $this->logger = $logger;
        $this->maskManager = $maskManager;
        $this->magooArray = new MagooArray($maskManager);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return MaskManagerInterface
     */
    public function getMaskManager()
    {
        return $this->maskManager;
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'emergency'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'alert'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'critical'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'error'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'warning'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'notice'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'info'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        call_user_func_array([$this->logger, 'debug'], $maskedArguments);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $maskedArguments = $this->maskLogArguments($message, $context);
        array_unshift($maskedArguments, $level);
        call_user_func_array([$this->logger, 'log'], $maskedArguments);
    }

    /**
     * @param string $message
     * @param array $context
     *
     * @return array Masked arguments
     */
    private function maskLogArguments($message, array $context)
    {
        return $this->magooArray->getMasked([$message, $context]);
    }
}
