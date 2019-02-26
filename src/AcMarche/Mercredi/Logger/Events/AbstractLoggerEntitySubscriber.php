<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 17/01/18
 * Time: 11:19
 */

namespace AcMarche\Mercredi\Logger\Events;

use AcMarche\Mercredi\Logger\Service\MonologDBHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractLoggerEntitySubscriber
{
    /**
     * Default action for logs
     */
    const UNKNOWN_ACTION = 'unknown_action';

    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var MonologDBHandler
     */
    private $monologDBHandler;

    /**
     * AbstractSubscriber constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Log(message, context)
     * @param string $action
     * @param string $title
     * @param array $entityFields
     */
    protected function logEntity($action = self::UNKNOWN_ACTION, array $entityFields)
    {
        $this->container->get('monolog.logger.db')->info(
            $action,
            [
                'entity' => $entityFields,
            ]
        );
    }
}
