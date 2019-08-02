<?php
/**
 * @package   Walkthechat\Walkthechat
 * @author    WalktheChat <info@walkthechat.com>
 * @copyright 2019 Walkthechat
 * @license   See LICENSE.txt for license details.
 */

namespace Walkthechat\Walkthechat\Model;

/**
 * Class ActionFactory
 *
 * @package Walkthechat\Walkthechat\Model
 */
class ActionFactory
{
    /**
     * Allowed action
     *
     * @var array
     */
    const ALLOWED_ACTIONS = [
        \Walkthechat\Walkthechat\Model\Action\Add::ACTION,
        \Walkthechat\Walkthechat\Model\Action\Update::ACTION,
        \Walkthechat\Walkthechat\Model\Action\Delete::ACTION,
    ];

    /**
     * Action namespace
     *
     * @string
     */
    const ACTION_NAMESPACE = '\Walkthechat\Walkthechat\Model\Action\\';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * ActionFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $action
     *
     * @return \Walkthechat\Walkthechat\Model\Action\AbstractAction
     * @throws \Exception
     */
    public function create($action)
    {
        if (in_array($action, static::ALLOWED_ACTIONS)) {
            $class = static::ACTION_NAMESPACE.ucfirst(strtolower($action));

            if (class_exists($class)) {
                return $this->objectManager->create($class);
            }
        }

        throw new \Exception(__('Unable to load action. Undefined action "%1".'));
    }
}
