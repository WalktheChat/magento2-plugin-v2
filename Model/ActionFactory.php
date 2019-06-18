<?php
/**
 * @package   WalktheChat\Walkthechat
 * @author    Alex Yeremenko <madonzy13@gmail.com>
 * @copyright 2019 WalktheChat
 * @license   See LICENSE.txt for license details.
 */

namespace WalktheChat\Walkthechat\Model;

/**
 * Class ActionFactory
 *
 * @package WalktheChat\Walkthechat\Model
 */
class ActionFactory
{
    /**
     * Allowed action
     *
     * @var array
     */
    const ALLOWED_ACTIONS = [
        \WalktheChat\Walkthechat\Model\Action\Add::ACTION,
        \WalktheChat\Walkthechat\Model\Action\Update::ACTION,
        \WalktheChat\Walkthechat\Model\Action\Delete::ACTION,
    ];

    /**
     * Action namespace
     *
     * @string
     */
    const ACTION_NAMESPACE = '\WalktheChat\Walkthechat\Model\Action\\';

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
     * @return \WalktheChat\Walkthechat\Model\Action\AbstractAction
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
