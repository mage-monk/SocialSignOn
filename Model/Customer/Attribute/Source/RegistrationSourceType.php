<?php

declare(strict_types=1);

namespace MageMonk\SocialSignOn\Model\Customer\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use MageMonk\SocialSignOn\Model\Customer\SessionSourceKey;

/**
 * Class to map the attribute for Source type
 */
class RegistrationSourceType extends AbstractSource
{
    /**
     * Constants for List of option available
     */
    public const MAGENTO = 0;

    /**
     * @inheritDoc
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::MAGENTO, 'label' => __('Manual')]
            ];
        }

        return $this->_options;
    }
}
