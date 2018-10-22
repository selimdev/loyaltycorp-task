<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Table(name="mail_chimp_members",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="email_address_list_id_unique",
 *            columns={"email_address", "list_id"})
 *    }
 * )
 *
 * @ORM\Entity()
 */
class MailChimpMember extends MailChimpEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $memberId;

    /**
     * @ORM\Column(name="unique_email_id", type="string", nullable=true)
     *
     * @var string
     */
    private $uniqueEmailId;

    /**
     * @ORM\Column(name="mail_chimp_unique_email_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpUniqueEmailId;

    /**
     * @ORM\Column(name="list_id", type="string")
     *
     * @var string
     */
    private $listId;

    /**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="status", type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(name="merge_fields", type="array", nullable=true)
     *
     * @var array
     */
    private $mergeFields;

    /**
     * @ORM\Column(name="interests", type="array", nullable=true)
     *
     * @var array
     */
    private $interests;

    /**
     * @ORM\Column(name="language", type="string", nullable=true)
     *
     * @var string
     */
    private $language;

    /**
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     *
     * @var bool
     */
    private $vip;

    /**
     * @ORM\Column(name="location", type="array", nullable=true)
     *
     * @var array
     */
    private $location;

    /**
     * @ORM\Column(name="marketing_permissions", type="array", nullable=true)
     *
     * @var array
     */
    private $marketingPermissions;

    /**
     * @ORM\Column(name="ip_signup", type="string", nullable=true)
     *
     * @var string
     */
    private $ipSignup;

    /**
     * @ORM\Column(name="timestamp_signup", type="string", nullable=true)
     *
     * @var string
     */
    private $timestampSignup;

    /**
     * @ORM\Column(name="ip_opt", type="string", nullable=true)
     *
     * @var string
     */
    private $ipOpt;

    /**
     * @ORM\Column(name="timestamp_opt", type="string", nullable=true)
     *
     * @var string
     */
    private $timestampOpt;

    /**
     * @ORM\Column(name="tags", type="array", nullable=true)
     *
     * @var array
     */
    private $tags;

    /**
     * Get the ID
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->memberId;
    }

    /**
     * Get the MailChimp member ID
     *
     * @return null|string
     */
    public function getMailChimpUniqueEmailId(): ?string
    {
        return $this->mailChimpUniqueEmailId;
    }

    /**
     * Get the list ID
     *
     * @return null|string
     */
    public function getListId(): ?string
    {
        return $this->listId;
    }

    /**
     * Subscriber Hash:
     * The MD5 hash of the lowercase version of the list member’s email address.
     * @return null|string
     */
    public function getSubscriberHash(): ?string
    {
        return md5(strtolower($this->emailAddress));
    }

    /**
     * Set the MailChimp member Unique Email ID
     *
     * @param string $mailChimpUniqueEmailId
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    public function setMailChimpUniqueEmailId(string $mailChimpUniqueEmailId): MailChimpMember
    {
        $this->mailChimpUniqueEmailId = $mailChimpUniqueEmailId;

        return $this;
    }

    /**
     * @param string $memberId
     * @return MailChimpMember
     */
    public function setMemberId(string $memberId): MailChimpMember
    {
        $this->memberId = $memberId;
        return $this;
    }

    /**
     * @param string $uniqueEmailId
     * @return MailChimpMember
     */
    public function setUniqueEmailId(string $uniqueEmailId): MailChimpMember
    {
        $this->uniqueEmailId = $uniqueEmailId;
        return $this;
    }

    /**
     * @param string $listId
     * @return MailChimpMember
     */
    public function setListId(string $listId): MailChimpMember
    {
        $this->listId = $listId;
        return $this;
    }

    /**
     * @param string $emailAddress
     * @return MailChimpMember
     */
    public function setEmailAddress(string $emailAddress): MailChimpMember
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @param string $emailType
     * @return MailChimpMember
     */
    public function setEmailType(string $emailType): MailChimpMember
    {
        $this->emailType = $emailType;
        return $this;
    }

    /**
     * @param string $status
     * @return MailChimpMember
     */
    public function setStatus(string $status): MailChimpMember
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param array $mergeFields
     * @return MailChimpMember
     */
    public function setMergeFields(array $mergeFields): MailChimpMember
    {
        $this->mergeFields = $mergeFields;
        return $this;
    }

    /**
     * @param array $interests
     * @return MailChimpMember
     */
    public function setInterests(array $interests): MailChimpMember
    {
        $this->interests = $interests;
        return $this;
    }

    /**
     * @param string $language
     * @return MailChimpMember
     */
    public function setLanguage(string $language): MailChimpMember
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @param bool $vip
     * @return MailChimpMember
     */
    public function setVip(bool $vip): MailChimpMember
    {
        $this->vip = $vip;
        return $this;
    }

    /**
     * @param array $location
     * @return MailChimpMember
     */
    public function setLocation(array $location): MailChimpMember
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @param array $marketingPermissions
     * @return MailChimpMember
     */
    public function setMarketingPermissions(array $marketingPermissions): MailChimpMember
    {
        $this->marketingPermissions = $marketingPermissions;
        return $this;
    }

    /**
     * @param string $ipSignup
     * @return MailChimpMember
     */
    public function setIpSignup(string $ipSignup): MailChimpMember
    {
        $this->ipSignup = $ipSignup;
        return $this;
    }

    /**
     * @param string $timestampSignup
     * @return MailChimpMember
     */
    public function setTimestampSignup(string $timestampSignup): MailChimpMember
    {
        $this->timestampSignup = $timestampSignup;
        return $this;
    }

    /**
     * @param string $ipOpt
     * @return MailChimpMember
     */
    public function setIpOpt(string $ipOpt): MailChimpMember
    {
        $this->ipOpt = $ipOpt;
        return $this;
    }

    /**
     * @param string $timestampOpt
     * @return MailChimpMember
     */
    public function setTimestampOpt(string $timestampOpt): MailChimpMember
    {
        $this->timestampOpt = $timestampOpt;
        return $this;
    }

    /**
     * @param array $tags
     * @return MailChimpMember
     */
    public function setTags(array $tags): MailChimpMember
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|string',
            'email_type' => 'nullable|string',
            'status' => 'required|string|in:subscribed,unsubscribed,cleaned,pending',
            'merge_fields' => 'nullable|array',
            'interests' => 'nullable|array',
            'language' => 'nullable|string',
            'vip' => 'nullable|boolean',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|number',
            'location.longitude' => 'nullable|number',
            'marketing_permissions' => 'nullable|array',
            'marketing_permissions.marketing_permission_id' => 'nullable|string',
            'marketing_permissions.enabled' => 'nullable|boolean',
            'ip_signup' => 'nullable|string',
            'timestamp_signup' => 'nullable|string',
            'ip_opt' => 'nullable|string',
            'timestamp_opt' => 'nullable|string',
            'tags' => 'nullable|array'
        ];
    }

    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }
}
