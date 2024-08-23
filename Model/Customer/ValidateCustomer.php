<?php

declare(strict_types=1);

namespace MageMonk\SocialSignOn\Model\Customer;

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use MageMonk\GoogleSignOn\Exception\AccountLockedException;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Validate Customer and company
 * either customer is locked or marked as inactive
 */
class ValidateCustomer
{

    /**
     * Validate constructor
     *
     * @param AuthenticationInterface $authentication
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyManagementInterface $companyManagement
     */
    public function __construct(
        private readonly AuthenticationInterface $authentication,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyManagement,
    ) {
    }

    /**
     * Validate
     *
     * @param CustomerInterface $customer
     * @return void
     * @throws UserLockedException|InvalidEmailOrPasswordException|LocalizedException
     */
    public function validate(CustomerInterface $customer): void
    {
        $customerId = $customer->getId();
        if ($this->authentication->isLocked($customerId) ||
            $customer->getExtensionAttributes()->getCompanyAttributes()->getStatus() == 0
        ) {
            throw new UserLockedException(__('The account is locked.'));
        }

        $company = $this->companyManagement->getByCustomerId($customerId);
        if ($company) {
            switch ($company->getStatus()) {
                case CompanyInterface::STATUS_REJECTED:
                    throw new LocalizedException(__('This account is locked.'));
                    break;
                case CompanyInterface::STATUS_PENDING:
                    throw new LocalizedException(
                        __('Your account is not yet approved. If you have questions, please contact the seller.')
                    );
                    break;
                default:
                    break;
            }
        }
    }
}
