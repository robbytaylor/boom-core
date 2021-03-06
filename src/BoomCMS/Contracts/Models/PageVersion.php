<?php

namespace BoomCMS\Contracts\Models;

use DateTime;

interface PageVersion
{
    /**
     * @return Page
     */
    public function getEditedBy();

    /**
     * @return DateTme
     */
    public function getEditedTime();

    /**
     * @return DateTime
     */
    public function getEmbargoedUntil();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getPageId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return int
     */
    public function getTemplateId();

    /**
     * @return Template
     */
    public function getTemplate();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return bool
     */
    public function isDraft();

    /**
     * @return bool
     */
    public function isEmbargoed();

    /**
     * @return bool
     */
    public function isPendingApproval();

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * @return string
     */
    public function status();
}
