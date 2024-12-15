<?php



\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.blogext {
            header = Blog
            elements {
                bloglist {
                    iconIdentifier = content-text
                    title = Blog: Listenansicht
                    description = Listenansicht der Blogbeiträge
                    tt_content_defValues {
                        CType = list
                        list_type = blogext_bloglist
                    }
                }
            }
            show = *
        }
    }'
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.blogext {
            header = Blog
            elements {
                blogcategory {
                    iconIdentifier = content-text
                    title = Blog: Kategorie-Menu
                    description = Navigation der Kategorien
                    tt_content_defValues {
                        CType = list
                        list_type = blogext_blogcategory
                    }
                }
            } 
            show = *
        }
    }'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.blogext {
            header = Blog
            elements {
                blogrss {
                    iconIdentifier = content-text
                    title = Blog: RSS Feed
                    description = RSS Feed für die Blogbeiträge
                    tt_content_defValues {
                        CType = list
                        list_type = blogext_blogrss
                    }
                }
            } 
            show = *
        }
    }'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.blogext {
            header = Blog
            elements {
                blogtag {
                    iconIdentifier = content-text
                    title = Blog: Tagcloud
                    description = Einfügen einer Tag-Cloud
                    tt_content_defValues {
                        CType = list
                        list_type = blogext_blogtag
                    }
                }
            } 
            show = *
        }
    }'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.blogext {
            header = Blog
            elements {
                bloglast {
                    iconIdentifier = content-text
                    title = Blog: Letzte Blogartikel
                    description = Anzeige der letzten Blogartikel
                    tt_content_defValues {
                        CType = list
                        list_type = blogext_bloglast
                    }
                }
            } 
            show = *
        }
    }'
);



// CSS for backend
$GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets']['blogext'] = 'EXT:blogext/Resources/Public/Css/Backend/backend.css?'.time();