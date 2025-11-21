<?php

class SkinRidvan extends SkinMustache {

    public function getTemplateData() {
        $data = parent::getTemplateData();

        // ---------------------------------------------------------
        // PART 1: HEADER BUTTONS (EXISTING LOGIC)
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
        // PART 2: CLEANUP LANGUAGES (EXISTING LOGIC)
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
        // PART 3: MOBILE DATA PREPARATION (WITH DEBUGGING)
        // ---------------------------------------------------------

        // Initialize containers
        $mobileMenu = [];
        $mobileTools = [];
        $mobileLinks = [];
        
        // DEBUG: Collection array to print to screen
        $debugLog = [
            'Available_Portlet_Keys' => array_keys($data['data-portlets'] ?? []),
            'Sidebar_First_Exists' => isset($data['data-portlets-sidebar']['data-portlets-first']),
            'Sidebar_Rest_Count' => count($data['data-portlets-sidebar']['array-portlets-rest'] ?? []),
        ];

        // --- A. MOBILE MENU (Attempt to find Navigation) ---
        // Strategy: Check Sidebar First, then fallback to 'p-navigation' in main portlets
        if (isset($data['data-portlets-sidebar']['data-portlets-first']['array-items'])) {
            $mobileMenu = $data['data-portlets-sidebar']['data-portlets-first']['array-items'];
            $debugLog['Mobile_Menu_Source'] = 'Sidebar-First';
        } elseif (isset($data['data-portlets']['p-navigation']['array-items'])) {
            $mobileMenu = $data['data-portlets']['p-navigation']['array-items'];
            $debugLog['Mobile_Menu_Source'] = 'p-navigation (Fallback)';
        } else {
            $debugLog['Mobile_Menu_Source'] = 'NOT FOUND';
            // Add a dummy item so you see the menu on screen
            $mobileMenu[] = ['text' => 'DEBUG: Menu Not Found', 'href' => '#'];
        }

        // --- B. MOBILE TOOLS (Toolbox + Others) ---
        // Strategy: Get p-tb, then get anything else in sidebar rest
        if (isset($data['data-portlets']['p-tb']['array-items'])) {
            $mobileTools = array_merge($mobileTools, $data['data-portlets']['p-tb']['array-items']);
        }
        
        // Add other sidebar items (usually 'p-interaction' etc)
        if (isset($data['data-portlets-sidebar']['array-portlets-rest'])) {
            foreach($data['data-portlets-sidebar']['array-portlets-rest'] as $key => $portlet) {
                $portletId = $portlet['id'] ?? 'unknown';
                // Skip if it is toolbox (already added) or language (goes in Links)
                if ($portletId !== 'p-tb' && $portletId !== 'p-lang') {
                    if (isset($portlet['array-items'])) {
                        $mobileTools = array_merge($mobileTools, $portlet['array-items']);
                        $debugLog['Mobile_Tools_Added'][] = $portletId;
                    }
                }
            }
        }

        // --- C. MOBILE LINKS (Wikibase + Langs) ---
        // 1. Languages
        $langs = $data['data-portlets']['data-languages']['array-items'] ?? [];
        
        // 2. Wikibase / Other Projects
        // We explicitly look for p-wikibase-otherprojects or p-wikibase-otherprojects
        $wikibase = [];
        if (isset($data['data-portlets']['p-wikibase-otherprojects']['array-items'])) {
            $wikibase = $data['data-portlets']['p-wikibase-otherprojects']['array-items'];
            $debugLog['Wikibase_Source'] = 'p-wikibase-otherprojects';
        } elseif (isset($data['data-portlets']['p-wikibase']['array-items'])) {
             $wikibase = $data['data-portlets']['p-wikibase']['array-items'];
             $debugLog['Wikibase_Source'] = 'p-wikibase';
        }
        
        $mobileLinks = array_merge($wikibase, $langs);

        // ASSIGN TO TEMPLATE
        $data['ridvan-mobile-menu'] = $mobileMenu;
        $data['ridvan-mobile-tools'] = $mobileTools;
        $data['ridvan-mobile-links'] = $mobileLinks;

        // ---------------------------------------------------------
        // PART 4: VISUAL DEBUG OUTPUT
        // ---------------------------------------------------------
        // This injects a PRE block into the footer copyright area temporarily
        // so you can see what keys are available without checking server logs.
        
        $debugHtml = "<div style='background:#333; color:#0f0; padding:20px; z-index:99999; position:relative; clear:both;'>";
        $debugHtml .= "<h3>RIDVAN DEBUG DATA</h3><pre>";
        $debugHtml .= print_r($debugLog, true);
        $debugHtml .= "</pre></div>";

        // We append this to the html-after-content or footer to ensure visibility
        $data['html-after-content'] .= $debugHtml;

        return $data;
    }
}
