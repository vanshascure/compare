<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

declare(strict_types=1);

namespace Amasty\Acart\Block\Adminhtml\Buttons;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    const DEFAULT_ID_FIELD_NAME = 'id';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var string
     */
    private $idFieldName;

    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        string $idFieldName = self::DEFAULT_ID_FIELD_NAME
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        $this->idFieldName = $idFieldName;
    }

    public function getButtonData()
    {
        $id = (int)$this->request->getParam($this->idFieldName);

        if ($id) {
            $alertMessage = $this->escaper->escapeHtml(__('Are you sure you want to do this?'));
            $onClick = sprintf('deleteConfirm("%s", "%s")', $alertMessage, $this->getDeleteUrl($id));

            return [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => $onClick,
                'sort_order' => 30,
            ];
        }

        return [];
    }

    public function getDeleteUrl(int $id): string
    {
        return $this->urlBuilder->getUrl('*/*/delete', [$this->idFieldName => $id]);
    }
}
