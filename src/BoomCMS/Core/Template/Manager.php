<?php

namespace BoomCMS\Core\Template;

use BoomCMS\Core\Theme\Theme;
use BoomCMS\Repositories\Template as TemplateRepository;
use Illuminate\Filesystem\Filesystem;

class Manager
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TemplateRepository
     */
    protected $repository;

    public function __construct(Filesystem $filesystem, TemplateRepository $repository)
    {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
    }

    public function createTemplateWithFilename($theme, $filename)
    {
        $this->repository->create([
            'name'     => ucwords(str_replace('_', ' ', $filename)),
            'theme'    => $theme,
            'filename' => $filename,
        ]);
    }

    public function findAndInstallNewTemplates()
    {
        $installed = [];

        foreach ($this->findInstalledThemes() as $theme) {
            foreach ($this->findAvailableTemplates($theme) as $template) {
                if (!$this->templateIsInstalled($theme, $template)) {
                    $installed[] = [$theme, $template];
                    $this->createTemplateWithFilename($theme, $template);
                }
            }
        }

        return $installed;
    }

    public function findAvailableTemplates(Theme $theme)
    {
        $files = $this->filesystem->files($theme->getTemplateDirectory());
        $templates = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                if (strpos($file, '.php') !== false) {
                    $file = str_replace($theme->getTemplateDirectory().'/', '', $file);
                    $templates[] = str_replace('.php', '', $file);
                }
            }
        }

        return $templates;
    }

    public function findInstalledThemes()
    {
        $theme = new Theme();
        $themes = $this->filesystem->directories($theme->getThemesDirectory());

        if (is_array($themes)) {
            foreach ($themes as &$t) {
                $t = new Theme(str_replace($theme->getThemesDirectory().'/', '', $t));
            }
        }

        return $themes ?: [];
    }

    public function getAllTemplates()
    {
        return $this->repository->findAll();
    }

    public function getValidTemplates()
    {
        $valid = [];
        $templates = $this->getAllTemplates();

        foreach ($templates as $template) {
            if ($template->fileExists()) {
                $valid[] = $template;
            }
        }

        return $valid;
    }

    public function templateIsInstalled($theme, $filename)
    {
        $template = $this->repository->findByThemeAndFilename($theme, $filename);

        return $template !== null;
    }
}
