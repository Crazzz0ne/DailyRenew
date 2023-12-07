<?php


namespace App\Models\SalesFlow\Lead;


use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Exceptions\AuditableTransitionException;
use OwenIt\Auditing\Exceptions\AuditingException;

class BaseLeadUpload extends Authenticatable implements AuditableInterface
{
	use  SoftDeletes;

	protected $table = 'lead_uploads';

	protected $fillable = [
		'lead_id',
		'user_id',
		'type',
		'size',
		'path',
	];

	/**
	 * Auditable Model audits.
	 *
	 * @return MorphMany
	 */
	public function audits(): MorphMany
	{
		// TODO: Implement audits() method.
	}

	/**
	 * Set the Audit event.
	 *
	 * @param string $event
	 *
	 * @return AuditableInterface
	 */
	public function setAuditEvent(string $event): AuditableInterface
	{
		// TODO: Implement setAuditEvent() method.
	}

	/**
	 * Get the Audit event that is set.
	 *
	 * @return string|null
	 */
	public function getAuditEvent()
	{
		// TODO: Implement getAuditEvent() method.
	}

	/**
	 * Get the events that trigger an Audit.
	 *
	 * @return array
	 */
	public function getAuditEvents(): array
	{
		// TODO: Implement getAuditEvents() method.
	}

	/**
	 * Is the model ready for auditing?
	 *
	 * @return bool
	 */
	public function readyForAuditing(): bool
	{
		// TODO: Implement readyForAuditing() method.
	}

	/**
	 * Return data for an Audit.
	 *
	 * @return array
	 * @throws AuditingException
	 *
	 */
	public function toAudit(): array
	{
		// TODO: Implement toAudit() method.
	}

	/**
	 * Get the (Auditable) attributes included in audit.
	 *
	 * @return array
	 */
	public function getAuditInclude(): array
	{
		// TODO: Implement getAuditInclude() method.
	}

	/**
	 * Get the (Auditable) attributes excluded from audit.
	 *
	 * @return array
	 */
	public function getAuditExclude(): array
	{
		// TODO: Implement getAuditExclude() method.
	}

	/**
	 * Get the strict audit status.
	 *
	 * @return bool
	 */
	public function getAuditStrict(): bool
	{
		// TODO: Implement getAuditStrict() method.
	}

	/**
	 * Get the audit (Auditable) timestamps status.
	 *
	 * @return bool
	 */
	public function getAuditTimestamps(): bool
	{
		// TODO: Implement getAuditTimestamps() method.
	}

	/**
	 * Get the Audit Driver.
	 *
	 * @return string|null
	 */
	public function getAuditDriver()
	{
		// TODO: Implement getAuditDriver() method.
	}

	/**
	 * Get the Audit threshold.
	 *
	 * @return int
	 */
	public function getAuditThreshold(): int
	{
		// TODO: Implement getAuditThreshold() method.
	}

	/**
	 * Get the Attribute modifiers.
	 *
	 * @return array
	 */
	public function getAttributeModifiers(): array
	{
		// TODO: Implement getAttributeModifiers() method.
	}

	/**
	 * Transform the data before performing an audit.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function transformAudit(array $data): array
	{
		// TODO: Implement transformAudit() method.
	}

	/**
	 * Generate an array with the model tags.
	 *
	 * @return array
	 */
	public function generateTags(): array
	{
		// TODO: Implement generateTags() method.
	}

	/**
	 * Transition to another model state from an Audit.
	 *
	 * @param Audit $audit
	 * @param bool $old
	 *
	 * @return AuditableInterface
	 * @throws AuditableTransitionException
	 *
	 */
	public function transitionTo(Audit $audit, bool $old = false): AuditableInterface
	{
		// TODO: Implement transitionTo() method.
	}
}
