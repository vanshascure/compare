<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Source;

/**
 * Class Methods
 */
class Methods implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Amasty\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    public function __construct(
        \Amasty\Sorting\Model\MethodProvider $methodProvider
    ) {
        $this->methodProvider = $methodProvider;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->methodProvider->getMethods() as $methodObject) {
            $options[] = [
                'value' => $methodObject->getMethodCode(),
                'label' => $methodObject->getMethodName()
            ];
        }

        return $options;
    }
}
