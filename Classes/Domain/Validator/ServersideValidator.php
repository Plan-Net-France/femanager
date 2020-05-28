<?php
declare(strict_types=1);
namespace In2code\Femanager\Domain\Validator;

use In2code\Femanager\Domain\Model\User;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Validation\Error;

/**
 * Class ServersideValidator
 */
class ServersideValidator extends AbstractValidator
{

    /**
     * Validation of given Params
     *
     * @param User $user
     * @return bool
     */
    public function isValid($user): bool
    {
        $this->init();
        if ($this->validationSettings['_enable']['server'] === '1') {
            foreach ($this->validationSettings as $fieldName => $validations) {
                if ($this->shouldBeValidated($user, $fieldName)) {
                    $value = $this->getValue($user, $fieldName);

                    foreach ($validations as $validation => $validationSetting) {
                        switch ($validation) {
                            case 'required':
                                $this->checkRequiredValidation($validationSetting, $value, $fieldName);
                                break;

                            case 'email':
                                $this->checkEmailValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'min':
                                $this->checkMinValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'max':
                                $this->checkMaxValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'intOnly':
                                $this->checkIntOnlyValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'lettersOnly':
                                $this->checkLetterOnlyValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'uniqueInPage':
                                $this->checkUniqueInPageValidation($user, $value, $validationSetting, $fieldName);
                                break;

                            case 'uniqueInDb':
                                $this->checkUniqueInDbValidation($user, $value, $validationSetting, $fieldName);
                                break;

                            case 'mustInclude':
                                $this->checkMustIncludeValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'mustNotInclude':
                                $this->checkMustNotIncludeValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'inList':
                                $this->checkInListValidation($value, $validationSetting, $fieldName);
                                break;

                            case 'sameAs':
                                $this->checkSameAsValidation($user, $validationSetting, $value, $fieldName);
                                break;

                            case 'date':
                                // Nothing to do. ServersideValidator runs after converter
                                // If dateTimeConverter exception $value is the old DateTime Object => True
                                // If dateTimeConverter runs well we have an DateTime Object => True
                                break;

                            default:
                                // e.g. search for method validateCustom()
                                $this->checkAnyValidation($validation, $value, $validationSetting, $fieldName);
                        }
                    }
                }
            }
        }
        return $this->isValid;
    }

    /**
     * @param $message
     * @param $fieldName
     * @return void
     */
    protected function addPropertyError($message, $fieldName)
    {
        $this->result->forProperty($fieldName)->addError(new Error((string)$message, 0));
    }

    /**
     * @param $validationSetting
     * @param $value
     * @param $fieldName
     * @return void
     */
    protected function checkRequiredValidation($validationSetting, $value, $fieldName)
    {
        if ($validationSetting === '1' && !$this->validateRequired($value)) {
            $this->addPropertyError('validationErrorRequired', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkEmailValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && $validationSetting === '1' && !$this->validateEmail($value)) {
            $this->addPropertyError('validationErrorEmail', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkMinValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && !$this->validateMin($value, $validationSetting)) {
            $this->addPropertyError('validationErrorMin', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkMaxValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && !$this->validateMax($value, $validationSetting)) {
            $this->addPropertyError('validationErrorMax', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkIntOnlyValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && $validationSetting === '1' && !$this->validateInt($value)) {
            $this->addPropertyError('validationErrorInt', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkLetterOnlyValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && $validationSetting === '1' && !$this->validateLetters($value)) {
            $this->addPropertyError('validationErrorLetters', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $user
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkUniqueInPageValidation($user, $value, $validationSetting, $fieldName)
    {
        if (!empty($value) &&
            $validationSetting === '1' &&
            !$this->validateUniquePage($value, $fieldName, $user)
        ) {
            $this->addPropertyError('validationErrorUniquePage', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $user
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkUniqueInDbValidation($user, $value, $validationSetting, $fieldName)
    {
        if (!empty($value) &&
            $validationSetting === '1' &&
            !$this->validateUniqueDb($value, $fieldName, $user)
        ) {
            $this->addPropertyError('validationErrorUniqueDb', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkMustIncludeValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && !$this->validateMustInclude($value, $validationSetting)) {
            $this->addPropertyError('validationErrorMustInclude', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkMustNotIncludeValidation($value, $validationSetting, $fieldName)
    {
        if (!empty($value) && !$this->validateMustNotInclude($value, $validationSetting)) {
            $this->addPropertyError('validationErrorMustNotInclude', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkInListValidation($value, $validationSetting, $fieldName)
    {
        if (!$this->validateInList($value, $validationSetting)) {
            $this->addPropertyError('validationErrorInList', $fieldName);
            $this->isValid = false;
        }
    }

    /**
     * @param $user
     * @param $validationSetting
     * @param $value
     * @param $fieldName
     * @return void
     */
    protected function checkSameAsValidation($user, $validationSetting, $value, $fieldName)
    {
        if (method_exists($user, 'get' . ucfirst($validationSetting))) {
            $valueToCompare = $user->{'get' . ucfirst($validationSetting)}();
            if (!$this->validateSameAs($value, $valueToCompare)) {
                $this->addPropertyError('validationErrorSameAs', $fieldName);
                $this->isValid = false;
            }
        }
    }

    /**
     * @param $validation
     * @param $value
     * @param $validationSetting
     * @param $fieldName
     * @return void
     */
    protected function checkAnyValidation($validation, $value, $validationSetting, $fieldName)
    {
        if (method_exists($this, 'validate' . ucfirst($validation))) {
            if (!$this->{'validate' . ucfirst($validation)}($value, $validationSetting)) {
                $this->addPropertyError('validationError' . ucfirst($validation), $fieldName);
                $this->isValid = false;
            }
        }
    }

    /**
     * @param User $user
     * @param string $fieldName
     * @return string
     */
    protected function getValue($user, $fieldName)
    {
        $value = $this->getValueFromProperty($user, $fieldName);
        if (is_object($value)) {
            if (method_exists($value, 'getUid')) {
                $value = $value->getUid();
            }
            if (method_exists($value, 'getFirst')) {
                $value = $value->getFirst()->getUid();
            }
            if (method_exists($value, 'current')) {
                $current = $value->current();
                if (method_exists($current, 'getUid')) {
                    $value = $current->getUid();
                }
            }
        }
        return $value;
    }

    /**
     * @param $user
     * @param $fieldName
     * @return bool
     */
    protected function shouldBeValidated($user, $fieldName): bool
    {
        return is_object($user) && $this->propertyHasGetterMethod($user, $fieldName);
    }

    /**
     * @param $user
     * @param $fieldName
     * @return mixed
     */
    protected function getValueFromProperty($user, $fieldName)
    {
        $value = '';
        try {
            $value = ObjectAccess::getProperty($user, $fieldName);
        } catch (\Exception $exception) {
            unset($exception);
        }
        return $value;
    }

    /**
     * @return bool
     */
    protected function propertyHasGetterMethod($user, $fieldName): bool
    {
        try {
            ObjectAccess::getProperty($user, $fieldName);
            $getterExists = true;
        } catch (\Exception $exception) {
            unset($exception);
            $getterExists = false;
        }
        return $getterExists === true;
    }
}
