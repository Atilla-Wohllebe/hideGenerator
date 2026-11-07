<?php

/**
 * @file plugins/generic/hideGenerator/HideGeneratorPlugin.php
 *
 * Copyright (c) 2026 Wohllebe & Ross Publishing GbR
 * Distributed under the GNU GPL v3.
 *
 * @class HideGeneratorPlugin
 * @brief Removes the <meta name="generator"> tag (the OJS version fingerprint) from the HTML output.
 */

namespace APP\plugins\generic\hideGenerator;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class HideGeneratorPlugin extends GenericPlugin
{
    /** @var bool Prevents registering the output filter more than once per request. */
    private $filterRegistered = false;

    /**
     * @copydoc Plugin::register()
     */
    public function register($category, $path, $mainContextId = null)
    {
        if (parent::register($category, $path, $mainContextId)) {
            if ($this->getEnabled()) {
                Hook::add('TemplateManager::display', [$this, 'handleTemplateDisplay']);
            }
            return true;
        }
        return false;
    }

    /**
     * Hook callback for TemplateManager::display.
     * Registers a Smarty output filter that post-processes the rendered page.
     *
     * @param string $hookName
     * @param array  $args [TemplateManager $templateMgr, string $template, string $output]
     * @return bool false to continue normal processing
     */
    public function handleTemplateDisplay($hookName, $args)
    {
        $templateMgr = $args[0];
        if (!$this->filterRegistered) {
            $templateMgr->registerFilter('output', [$this, 'removeGeneratorMeta']);
            $this->filterRegistered = true;
        }
        return false;
    }

    /**
     * Smarty output filter: strips every <meta name="generator" ...> from the HTML.
     *
     * @param string $output The fully rendered page.
     * @param object $templateMgr
     * @return string
     */
    public function removeGeneratorMeta($output, $templateMgr)
    {
        return preg_replace('/\s*<meta\s+name=(["\'])generator\1[^>]*>/i', '', $output);
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return 'Hide Generator Meta Tag';
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return 'Removes the generator meta tag (the OJS version fingerprint) from the HTML output to make version fingerprinting harder.';
    }
}
