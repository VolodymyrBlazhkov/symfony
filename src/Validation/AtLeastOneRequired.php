<?php

namespace App\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

#[Attribute(Attribute::TARGET_CLASS)]
class AtLeastOneRequired extends Constraint
{
    /**
     * @var string[]
     */
    public array $requiredFields;

    public string $message = 'At least one of {{ fields }} is required.';

    public const ONE_REQ_ERROR = '221323-jhj123-1231-1231-12312';

    protected static $errorNames = [
        self::ONE_REQ_ERROR => 'ONE_REQ_ERROR'
    ];

    public function __construct(
        array $options = null,
        array $requiredFields = null,
        string $message = null,
        array $groups = null,
        $payload = null
    ) {
        if (!empty($options) && array_is_list($options)) {
            $requiredFields = $requiredFields ?? $options;
            $options = [];
        }

        if (empty($requiredFields)) {
            throw new ConstraintDefinitionException('the requiredFields is requred');
        }

        $options['value'] = $requiredFields;

        parent::__construct($options, $groups, $payload);

        $this->requiredFields = $requiredFields;
        $this->message = $message ?? $this->message;
    }

    public function getRequiredOptions(): array
    {
        return ['requiredFields'];
    }

    public function getDefaultOption(): string
    {
        return 'requiredFields';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}