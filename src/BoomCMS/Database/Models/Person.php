<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Group as GroupInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Support\Traits\Comparable;
use DateTime;
use Hautelook\Phpass\PasswordHash;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Person extends Model implements PersonInterface, CanResetPassword
{
    use Comparable;
    use SoftDeletes;

    const ATTR_ID = 'id';
    const ATTR_NAME = 'name';
    const ATTR_EMAIL = 'email';
    const ATTR_ENABLED = 'enabled';
    const ATTR_PASSWORD = 'password';
    const ATTR_FAILED_LOGINS = 'failed_logins';
    const ATTR_LOCKED_UNTIL = 'locked_until';
    const ATTR_SUPERUSER = 'superuser';
    const ATTR_REMEMBER_TOKEN = 'remember_token';
    const ATTR_LAST_FAILED_LOGIN = 'last_failed_login';

    public $table = 'people';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group)
    {
        $this->groups()->attach($group);

        return $this;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($password)
    {
        $hasher = new PasswordHash(8, false);

        return $hasher->checkPassword($password, $this->getPassword());
    }

    /**
     * @param type $persistCode
     *
     * @return bool
     */
    public function checkPersistCode($persistCode)
    {
        return $persistCode === $this->getId();
    }

    public function getEmail()
    {
        return $this->{self::ATTR_EMAIL};
    }

    public function getEmailForPasswordReset()
    {
        return $this->getEmail();
    }

    public function getFailedLogins()
    {
        return $this->{self::ATTR_FAILED_LOGINS};
    }

    public function getGroups()
    {
        return $this->groups()->get();
    }

    /**
     * @return array
     */
    public function getGroupIds()
    {
        return (array) $this->groups->lists('id');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return  (int) $this->{self::ATTR_ID};
    }

    /**
     * @return DateTime
     */
    public function getLastFailedLogin()
    {
        return (new DateTime())->setTimestamp($this->{self::ATTR_LAST_FAILED_LOGIN});
    }

    public function getLockedUntil()
    {
        return $this->{self::ATTR_LOCKED_UNTIL};
    }

    public function getLogin()
    {
        return $this->getEmail();
    }

    public function getName()
    {
        return $this->{self::ATTR_NAME};
    }

    public function getPassword()
    {
        return $this->{self::ATTR_PASSWORD};
    }

    public function getRememberToken()
    {
        return $this->{self::ATTR_REMEMBER_TOKEN};
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return $this
     */
    public function incrementFailedLogins()
    {
        $this->increment(self::ATTR_FAILED_LOGINS);

        return $this;
    }

    public function isAllowed($role, $pageId = null)
    {
        $result = DB::table('group_role')
            ->select(DB::raw('bit_and(allowed) as allowed'))
            ->join('group_person', 'group_person.group_id', '=', 'group_role.group_id')
            ->join('groups', 'group_person.group_id', '=', 'groups.id')
            ->join('roles', 'roles.id', '=', 'group_role.role_id')
            ->whereNull('groups.deleted_at')
            ->where('group_person.person_id', '=', $this->getId())
            ->where('roles.name', '=', $role)
            ->groupBy('person_id')    // Strange results if this isn't here.
            ->where('group_role.page_id', '=', $pageId)
            ->first();

        return (isset($result->allowed)) ? (bool) $result->allowed : null;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->{self::ATTR_ENABLED} == true;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->getLockedUntil() && ($this->getLockedUntil() > time());
    }

    /**
     * @return bool
     */
    public function isSuperuser()
    {
        return $this->{self::ATTR_SUPERUSER} == true;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->getId() > 0 && !$this->isLocked();
    }

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group)
    {
        $this->groups()->detach($group->getId());

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->{self::ATTR_EMAIL} = $email;

        return $this;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes[self::ATTR_EMAIL] = strtolower(trim($value));
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->{self::ATTR_ENABLED} = $enabled;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setEncryptedPassword($password)
    {
        $this->{self::ATTR_PASSWORD} = $password;

        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setFailedLogins($count)
    {
        $this->{self::ATTR_FAILED_LOGINS} = $count;

        return $this;
    }

    /**
     * @param int $time
     *
     * @return $this
     */
    public function setLastFailedLogin($time)
    {
        $this->{self::ATTR_LAST_FAILED_LOGIN} = $time;

        return $this;
    }

    /**
     * @param int $timestamp
     *
     * @return $this
     */
    public function setLockedUntil($timestamp)
    {
        $this->{self::ATTR_LOCKED_UNTIL} = $timestamp;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->{self::ATTR_NAME} = $name;

        return $this;
    }

    /**
     * @param bool $superuser
     *
     * @return $this
     */
    public function setSuperuser($superuser)
    {
        $this->{self::ATTR_SUPERUSER} = $superuser;

        return $this;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setRememberToken($token)
    {
        $this->{self::ATTR_REMEMBER_TOKEN} = $token;

        return $this;
    }
}
