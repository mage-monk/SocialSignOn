<?php

declare(strict_types=1);

namespace MageMonk\SocialSignOn\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * This class to check if Google Sso is enabled
 * and is mobile view.
 */
class IsEnableSso implements ArgumentInterface
{
    /**
     * @var array
     */
    private array $configs;

    /**
     * Initialization
     *
     * @param array $configs
     */
    public function __construct(
        array $configs = []
    ) {
        $this->configs = $configs;
    }

    /**
     * Is enable google login
     *
     * @return bool
     */
    public function isEnable() : bool
    {
        foreach ($this->configs as $config) {
            if ($config->isEnable()) {
                return true;
            }
        }
        return false;
    }
}
