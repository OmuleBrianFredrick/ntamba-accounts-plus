<?php

namespace App\Enums;

/**
 * The four roles from the plan. Each sees only what its job needs.
 */
enum Role: string
{
    case Agent = 'agent';
    case Accountant = 'accountant';
    case ProductionManager = 'production_manager';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Agent => 'Agent',
            self::Accountant => 'Accountant',
            self::ProductionManager => 'Production Manager',
            self::Admin => 'Admin',
        };
    }

    /** Roles that sign in with email + password (agents use phone + PIN). */
    public function usesEmailPassword(): bool
    {
        return $this !== self::Agent;
    }

    /** Accountant and admin get a second sign-in step (plan: Security). */
    public function requiresSecondFactor(): bool
    {
        return in_array($this, [self::Accountant, self::Admin], true);
    }
}
