<?php

class SkinRidvan extends SkinMustache {

    public function getTemplateData() {
        $data = parent::getTemplateData();

        // ---------------------------------------------------------
        // PART 1: HEADER BUTTONS
        // ---------------------------------------------------------
        $allPortlets = array_merge(
            $data['data-portlets']['data-namespaces']['array-items'] ?? [],
            $data['data-portlets']['data-views']['array-items'] ?? [],
            $data['data-portlets']['data-actions']['array-items'] ?? []
        );

        $editButton = null;
        $talkButton = null;
        $hybridMenu = [];

        foreach ( $allPortlets as $item ) {
            $id = $item['id'] ?? '';
            if ( $id === 'ca-edit' || $id === 'ca-viewsource' ) {
                $editButton = $item;
            } elseif ( $id === 'ca-talk' || $id === 'ca-nstab-talk' ) {
                $talkButton = $item;
            } else {
                $hybridMenu[] = $item;
            }
        }

        $data['ridvan-content-edit'] = $editButton ? [ $editButton ] : [];
        $data['ridvan-content-talk'] = $talkButton ? [ $talkButton ] : [];
        $data['ridvan-content-hybrid'] = $hybridMenu;

        // ---------------------------------------------------------
        // PART 2: CLEANUP LANGUAGES
        // ---------------------------------------------------------
        if ( isset( $data['data-portlets']['data-languages']['array-items'] ) ) {
            $langItems = $data['data-portlets']['data-languages']['array-items'];
            $realLanguages = array_filter( $langItems, function( $item ) {
                return strpos( $item['class'] ?? '', 'wbc-editpage' ) === false;
            });
            if ( empty( $realLanguages ) ) {
                unset( $data['data-portlets']['data-languages'] );
            }
        } else {
            unset( $data['data-portlets']['data-languages'] );
        }

        // ---------------------------------------------------------
        // PART 3: MODIFY SIDEBAR DATA (INJECT & REMOVE)
        // ---------------------------------------------------------
        // We must modify the data source BEFORE assigning it to variables for Mobile Logic.

        if ( isset( $data['data-portlets-sidebar']['array-portlets-rest'] ) ) {
            foreach ( $data['data-portlets-sidebar']['array-portlets-rest'] as $key => &$portlet ) {
                $id = $portlet['id'] ?? '';

                // 1. INJECT SPECIAL PAGES INTO TOOLBOX (p-tb)
                if ( $id === 'p-tb' ) {
                    $portlet['array-items'][] = [
                        'id' => 't-specialpages',
                        'text' => $this->msg( 'specialpages' )->text(),
                        'href' => \SpecialPage::getTitleFor( 'SpecialPages' )->getLocalURL(),
                        'class' => ''
                    ];
                }

                // 2. REMOVE FALLBACK NAVIGATION (p-navigation)
                // This removes the section that only contains "Special Pages" redundancy
                if ( $id === 'p-navigation' ) {
                    unset( $data['data-portlets-sidebar']['array-portlets-rest'][$key] );
                }
            }
            unset($portlet); // Clean up reference

            // Re-index array so Mustache treats it as a list, not an object
            $data['data-portlets-sidebar']['array-portlets-rest'] = array_values( $data['data-portlets-sidebar']['array-portlets-rest'] );
        }

        // ---------------------------------------------------------
        // PART 4: MOBILE DATA PREPARATION (STRICT MODE)
        // ---------------------------------------------------------

        // 1. MOBILE MENU (Navigation)
        $mobileMenu = $data['data-portlets-sidebar']['data-portlets-first']['array-items'] ?? [];

        // 2. SORT SIDEBAR "REST" -> TOOLS vs LINKS
        // Now using the MODIFIED array from Part 3
        $sidebarRest = $data['data-portlets-sidebar']['array-portlets-rest'] ?? [];
        
        $mobileTools = [];
        $mobileLinks = [];

        foreach ( $sidebarRest as $portlet ) {
            $id = $portlet['id'] ?? '';
            $items = $portlet['array-items'] ?? [];

            if ( empty( $items ) ) {
                continue;
            }

            // A. WIKIBASE -> LINKS
            if ( $id === 'p-wikibase-otherprojects' || $id === 'p-wikibase' ) {
                $mobileLinks = array_merge( $mobileLinks, $items );
            } 
            // B. TOOLBOX -> TOOLS (STRICT: Only p-tb)
            elseif ( $id === 'p-tb' ) {
                $mobileTools = array_merge( $mobileTools, $items );
            } 
            // C. EVERYTHING ELSE -> DROPPED (Authors, Books, etc)
            else {
                continue;
            }
        }

        // 3. ADD LANGUAGES TO LINKS
        $langRaw = $data['data-portlets']['data-languages']['array-items'] ?? [];
        $langClean = array_filter( $langRaw, function( $item ) {
            return strpos( $item['class'] ?? '', 'wbc-editpage' ) === false;
        });
        $mobileLinks = array_merge( $mobileLinks, $langClean );

        // ASSIGN TO TEMPLATE
        $data['ridvan-mobile-menu'] = $mobileMenu;
        $data['ridvan-mobile-tools'] = $mobileTools;
        $data['ridvan-mobile-links'] = $mobileLinks;
        $data['ridvan-has-mobile-links'] = !empty($mobileLinks);

        return $data;
    }
}
