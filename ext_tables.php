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
